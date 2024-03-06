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

/**
 * Check the user and return ots ID.
 *
 * @return int
 */
function protectedUserId() {
    global $TOKEN_USERID, $USER;

    // $TOKEN_USERID may be defined by the TRAX Launch proxy.
    // If not, we use the current Moodle session to authentify the user.
    if (!isset($TOKEN_USERID)) {
        require_once('../../../../../../config.php');
        require_login();
        return $USER->id;
    } else {
        return $TOKEN_USERID;
    }
}

/**
 * Return the request params (only for GET requests).
 *
 * @return array
 */
function requestParams() {
    global $_GET;

    $params = $_GET;
    unset($params['api']);
    unset($params['token']);
    unset($params['objectid']);
    unset($params['objecttable']);
    unset($params['objecttype']);
    return $params;
}

/**
 * Return the request content and related content type.
 *
 * @return array
 */
function requestDataAndType() {
    $data = file_get_contents('php://input');

    $contenttype = isset($_SERVER['CONTENT_TYPE'])
        ? explode(';', $_SERVER['CONTENT_TYPE'], 2)[0]
        : (isset($_SERVER['HTTP_CONTENT_TYPE'])
            ? explode(';', $_SERVER['HTTP_CONTENT_TYPE'], 2)[0]
            : 'application/json'
        );

    if ($contenttype == 'application/json') {
        $decoded = json_decode($data, true);
        if ($decoded) {
            $data = $decoded;
        } else {
            $contenttype = 'application/octet-stream';
        }
    }

    return [$data, $contenttype];
}

/**
 * Return the objectid, objecttable and objecttype from a content.
 * 
 * There are 2 ways to get this data.
 *
 * The recommended way is to pass it in the params of the proxy URL.
 * E.g. $endpoint = $CFG->wwwroot . "/mod/traxlaunch/proxy/endpoint.php?objectid=$activity->id&objecttable=traxlaunch&objecttype=mod&token=$record->token&api=";
 * 
 * The old way is to use the former endpoint format with a trick in the statement.
 * E.g. $endpoint = $CFG->wwwroot . '/mod/traxlaunch/proxy/' . $record->token . '/';
 * 
 * The actor of the statements MUST contain the `objectid`, `objecttable` and `objecttype`.
 * We accept 2 formats for this actor: email (currently used by TRAX Launch) and account (may be required for CMI5 contents).
 * 
 * Structure:
 *      {"mbox": "mailto:objectid@objecttable.objecttype"}
 *      {"account": {"name":"objectid", "homePage":"http://objecttable.objecttype"}}
 *
 * @param mixed $data
 * @return array
 */
function objectInfo(mixed $data) {
    $objectid = optional_param('objectid', 0, PARAM_INT);
    $objecttable = optional_param('objecttable', '', PARAM_RAW);
    $objecttype = optional_param('objecttype', '', PARAM_RAW);

    if (!empty($objectid) && !empty($objecttable) && !empty($objecttype)) {
        return [$objectid, $objecttable, $objecttype];
    }

    // 2nd chance: we look into the statements.
    $statement = is_array($data) ? $data[0] : $data;

    if (isset($statement->actor->account)) {
        // Account format.
        $objectid = $statement->actor->account->name;
        $rest = substr($statement->actor->account->homePage, 7);
        list($objecttable, $objecttype) = explode('.', $rest);
    } elseif (isset($statement->actor->mbox)) {
        // Mbox format.
        $mbox = substr($statement->actor->mbox, 7);
        list($objectid, $rest) = explode('@', $mbox);
        list($objecttable, $objecttype) = explode('.', $rest);
    } else {
        return [false, false, false];
    }
    
    return [$objectid, $objecttable, $objecttype];
}


