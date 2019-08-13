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

require_once('../../../../../../config.php');
require_login();

use \logstore_trax\src\controller as trax_controller;
use \logstore_trax\event\proxy_statements_post;

$controller = new trax_controller();

// Get data.
$input = file_get_contents('php://input');
$data = json_decode($input);
if (!$data || empty($data)) {
    http_response_code(400);
    die;
}

// Get profile.
$statement = is_array($data) ? $data[0] : $data;
$mbox = substr($statement->actor->mbox, 7);
list($objectid, $rest) = explode('@', $mbox);
list($objecttable, $objecttype) = explode('.', $rest);

// Only modules.
if ($objecttype != 'mod') {
    http_response_code(400);
    die;
}
$module = 'mod_' . $objecttable;

// Transform statements.
$data = $controller->proxy($module)->get($data);

// POST the statements.
$response = $controller->client()->statements()->post($data);

// Return error.
if ($response->code != 200) {

    // Log it.
    trigger_event($objecttable, $objectid, $data, true);

    // Response.
    http_response_code($response->code);
    die;
}

// Log it.
trigger_event($objecttable, $objectid, $data);

// JSON response.
header('Content-Type: application/json');
header('X-Experience-API-Version: ' . $response->headers->xapi_version);
echo json_encode($response->content);


/**
 * Trigger the event.
 *
 * @param string $objecttable
 * @param int $objectid
 * @param array $statement
 * @param bool $error
 * @return void.
 */
function trigger_event($objecttable, $objectid, $statement, $error = false) {
    global $DB;
    $activity = $DB->get_record($objecttable, ['id' => $objectid]);
    $module = $DB->get_record('modules', ['name' => $objecttable]);
    $cm = $DB->get_record('course_modules', ['instance' => $objectid, 'module' => $module->id]);
    $contextmodule = context_module::instance($cm->id);

    $event = proxy_statements_post::create([
        'context' => $contextmodule,
        'other' => [
            'statement' => $statement,
            'error' => $error
        ]
    ]);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot($objecttable, $activity);
    $event->trigger();    
}



