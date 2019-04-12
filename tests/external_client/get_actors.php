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
 * This file can be used as a simple client to test the get_actors external service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Test settings: NEED TO BE CHANGED
 */
$domainname = 'http://moodle35.test';           // Moodle root URL
$token = '9437ea259fc079a1ec66bc46c43c2df2';    // Generated in Moodle: Admin > Plugins > Web Services > Manage Tokens
$params = ['items' => [
    ['type' => 'user', 'id' => 2]               // To get a user. The ID is visible in the user URL (http://moodle35.test/user/view.php?id=2)
]];


/**
 * API call: DO NOT CHANGE
 */
header('Content-Type: text/plain');
$functionname = 'logstore_trax_get_actors';
$serverurl = $domainname . '/webservice/rest/server.php'. '?moodlewsrestformat=json&wsfunction='.$functionname . '&wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$items = $curl->post($serverurl, $params);
print_r($items);
