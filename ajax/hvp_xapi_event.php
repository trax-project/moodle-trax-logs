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

// Define the object level.
$hasparent = isset($statement->context->contextActivities->parent) && !empty($statement->context->contextActivities->parent);
if ($hasparent) {
    $parentid = $statement->context->contextActivities->parent[0]->id;
    $parts = explode('subContentId=', $parentid);
    $moduleurl = $parts[0];
    $cmid = explode('mod/hvp/view.php?id=', $moduleurl)[1];
    if (count($parts) == 1) {

        // The parent is the Moodle H5P activity.
        $level = 2;

    } else {

        // The parent is an intermediate object under the Moodle H5P activity.
        $level = 3;
        $cmid = substr($cmid, 0, -1);   // Remove the & or ? char.
        $parentuuid = $parts[1];
    }

} else {

    // No parent, directly the Moodle H5P activity.
    $level = 1;
    $moduleurl = $statement->object->id;
    $cmid = explode('mod/hvp/view.php?id=', $moduleurl)[1];
}

// Get the module H5P type.
$cm = get_coursemodule_from_id('hvp', $cmid, 0, false, MUST_EXIST);
$hvp = $DB->get_record('hvp', ['id' => $cm->instance], '*', MUST_EXIST);
$library = $DB->get_record('hvp_libraries', array('id' => $hvp->main_library_id), '*', MUST_EXIST);
$moduletype = $library->machine_name;

// Get the object H5P type.
$objecttype = 'Unknown';
if (isset($statement->context->contextActivities->category)) {
    $catid = $statement->context->contextActivities->category[0]->id;
    $cat = array_pop(explode('/', $catid));
    $objecttype = explode('-', $cat)[0];
}

// Trigger the event.
if ($level == 1) {
    switch ($moduletype) {

        // Quiz.
        case 'H5P.SingleChoiceSet':
        case 'H5P.QuestionSet':
            if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/completed') {
                \logstore_trax\event\hvp_quiz_completed::create_statement($statement)->trigger();
            }
            break;

        // Interactive video.
        case 'H5P.InteractiveVideo':
            // Nothing. Should be the video completed.
            break;

        // Single question.
        case 'H5P.DragQuestion':
        case 'H5P.Blanks':
        case 'H5P.MarkTheWords':
        case 'H5P.DragText':
        case 'H5P.TrueFalse':
        case 'H5P.MultiChoice':
            if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/answered') {
                \logstore_trax\event\hvp_single_question_answered::create_statement($statement)->trigger();
            }
            break;
    }
} else if ($level == 2) {
    switch ($moduletype) {

        // Quiz.
        case 'H5P.SingleChoiceSet':
        case 'H5P.QuestionSet':
            if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/answered') {
                \logstore_trax\event\hvp_quiz_question_answered::create_statement($statement)->trigger();
            }
            break;

        // Interactive video.
        case 'H5P.InteractiveVideo':
            if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/answered') {
                if ($objecttype == 'H5P.Summary') {
                    \logstore_trax\event\hvp_video_summary_answered::create_statement($statement)->trigger();
                } else {
                    \logstore_trax\event\hvp_video_question_answered::create_statement($statement)->trigger();
                }
            }
            break;
    }
} else {
    switch ($moduletype) {

        // Quiz.
        case 'H5P.InteractiveVideo':
            if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/answered') {
                \logstore_trax\event\hvp_video_summary_question_answered::create_statement($statement)->trigger();
            }
            break;
    }
}

