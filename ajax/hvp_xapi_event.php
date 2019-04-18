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
 * AJAX script used to catch H5P xAPI events from JS.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../../../../config.php');
require_login();

// Get the statement string.
$statement = required_param('statement', PARAM_RAW);

// Chech JSON string.
$statement = json_decode($statement);
if (!$statement) {
    print_error('event_hvp_xapi_error_json', 'logstore_trax');
}

// Trigger the event.
if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/answered') {

    // Answering event.
    $inside = isset($statement->context->contextActivities->parent) && !empty($statement->context->contextActivities->parent);
    if ($inside) {

        // Answering a question in a question set.
        \logstore_trax\event\hvp_question_answered::create_statement($statement)->trigger();
    } else {

        // Answering a single question activity.
        \logstore_trax\event\hvp_module_answered::create_statement($statement)->trigger();
    }

} else if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/completed') {

    // Completion event.
    \logstore_trax\event\hvp_module_completed::create_statement($statement)->trigger();

} else {

    // Unsupported event.
    print_error('event_hvp_xapi_error_unsupported', 'logstore_trax');
}



