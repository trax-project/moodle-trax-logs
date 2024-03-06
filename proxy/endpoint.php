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

$api = required_param('api', PARAM_RAW);

// The API may contain the first param. Extract it.
$parts = explode('?', $api);
$api = $parts[0];
if (count($parts) == 2) {
    list($prop, $val) = explode('=', $parts[1]);
    // We assign it to the global $_GET variable because the Moodle required_param will get it.
    $_GET[$prop] = $val;
}

// The API may start with a slash: remove it.
if (substr($api, 0, 1) == '/') {
    $api = substr($api, 1);
}

// Require the right API.
switch ($api) {
    case 'statements':
        require_once(__DIR__ . '/statements.php');
        break;
    case 'activities/state':
        require_once(__DIR__ . '/states.php');
        break;
    case 'activities/profile':
        require_once(__DIR__ . '/activity_profiles.php');
        break;
    case 'activities':
        require_once(__DIR__ . '/activities.php');
        break;
    case 'agents/profile':
        require_once(__DIR__ . '/agent_profiles.php');
        break;
    case 'agents':
        require_once(__DIR__ . '/agents.php');
        break;
    case 'about':
        require_once(__DIR__ . '/about.php');
        break;
    default:
        http_response_code(404);
        die;
}



