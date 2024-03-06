<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * LRS proxy.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/lib.php');

use \logstore_trax\src\controller as trax_controller;
use \logstore_trax\event\proxy_statements_post;
use \logstore_trax\src\client;
use logstore_trax\src\config;

$userid = protectedUserId();
$controller = new trax_controller();
$putRequest = $_SERVER['REQUEST_METHOD'] == 'PUT';

// Get params.
$params = $putRequest ? [
    'statementId' => required_param('statementId', PARAM_RAW),
] : [];


// Get data.
$input = file_get_contents('php://input');
$data = json_decode($input);
if (!$data || empty($data)) {
    // Requests with attachments (multipart) are not supported by this proxy.
    http_response_code(400);
    die;
}

// We need to know which Moodle plugin is calling the proxy.
list($objectid, $objecttable, $objecttype) = objectInfo($data);

// Info not found.
if (!$objectid) {
    http_response_code(400);
    die;
}

// Only modules.
if ($objecttype != 'mod') {
    http_response_code(400);
    die;
}

// Get data records.
$activity = $DB->get_record($objecttable, ['id' => $objectid], '*', MUST_EXIST);
$module = $DB->get_record('modules', ['name' => $objecttable], '*', MUST_EXIST);
$cm = $DB->get_record('course_modules', ['instance' => $objectid, 'module' => $module->id], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$contextmodule = context_module::instance($cm->id);

// Transform statements.
$data = $controller->proxy($objecttable)->get($data, $userid, $course, $cm, $activity, $contextmodule);

// POST the statements.
$target = config::course_target($course->id);
if ($target == config::TARGET_NO) {
    // xAPI deactivated.
    // This may cause issues with contents waiting for a 200 response with statements IDs.
    http_response_code(204);
    die;
}
if ($putRequest) {
    $response = (new client($target))->statements()->put($data, $params);
} else {
    $response = (new client($target))->statements()->post($data, $params);
}

// Return error.
if ($response->code != 200) {

    // Log it.
    trigger_event($objecttable, $data, $course, $cm, $activity, $contextmodule, true);

    // Response.
    http_response_code($response->code);
    die;
}

// Log it.
trigger_event($objecttable, $data, $course, $cm, $activity, $contextmodule);

// JSON response.
header('Content-Type: application/json');
header('X-Experience-API-Version: ' . $response->headers->xapi_version);
echo json_encode($response->content);


/**
 * Trigger the event.
 *
 * @param string $objecttable
 * @param array $statement
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $activity
 * @param context_module $context
 * @param bool $error
 * @return void.
 */
function trigger_event($objecttable, $statement, $course, $cm, $activity, $context, $error = false) {

    $event = proxy_statements_post::create([
        'context' => $context,
        'other' => [
            'statement' => $statement,
            'error' => $error
        ]
    ]);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot($objecttable, $activity);
    $event->trigger();
}



