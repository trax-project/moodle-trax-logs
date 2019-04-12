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
 * This file can be used as a simple client to test the get_activities external service.
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
    ['type' => 'system', 'id' => 0],            // To get the platform instance. The ID is always 0
    ['type' => 'course', 'id' => 2],            // To get a course. The ID is visible in the course URL (e.g. http://moodle35.test/course/view.php?id=2)
    ['type' => 'lti', 'id' => 1]                // To get an LTI activity. The ID is not visible in the URL (not the same ID!). Check your DB "lti" table :(
]];


/**
 * API call: DO NOT CHANGE
 */
header('Content-Type: text/plain');
$functionname = 'logstore_trax_get_activities';
$serverurl = $domainname . '/webservice/rest/server.php'. '?moodlewsrestformat=json&wsfunction='.$functionname . '&wstoken=' . $token;
require_once('./curl.php');
$curl = new curl;
$items = $curl->post($serverurl, $params);
print_r($items);

