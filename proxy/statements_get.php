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

$params = $_GET;
$controller = new trax_controller();

// Force the agent and related_agents for security reasons.
$params['agent'] = json_encode($controller->actors->get('user', $USER->id));
$params['related_agents'] = 0;

// Get the statements.
$response = $controller->client()->statements()->get($params);

// Return error.
if ($response->code != 200) {
    http_response_code($response->code);
    die;
}

// Return JSON.
header('Content-Type: application/json');
header('X-Experience-API-Version: ' . $response->headers->xapi_version);
header('X-Experience-API-Consistent-Through: ' . $response->headers->xapi_consistent_through);
echo json_encode($response->content);





