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

// Protect and get $userid.
require_once(__DIR__ . '/protect.php');

use \logstore_trax\src\controller as trax_controller;

$controller = new trax_controller();

// Get params
$params = $_POST;
$params['agent'] = json_encode($controller->actors->get('user', $userid));
unset($params['token']);

// Get data.
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// POST the state.
$response = $controller->client()->states()->post($data, $params);  // Not necessarily JSON !!!!!!!!!!!!!!!!!!!.

// Return error.
if ($response->code != 200) {
    http_response_code($response->code);
    die;
}

// JSON response.
header('Content-Type: application/json');  // Not necessarily JSON !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!.
header('X-Experience-API-Version: ' . $response->headers->xapi_version);
echo json_encode($response->content);
