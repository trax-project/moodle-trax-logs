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

// Get the object H5P type.
$objecttype = 'Unknown';
if (isset($statement->context->contextActivities->category)) {
    $catid = $statement->context->contextActivities->category[0]->id;
    $parts = explode('/', $catid);
    $cat = array_pop($parts);
    $objecttype = explode('-', $cat)[0];
}

// Trigger the event.
switch ($objecttype) {

    // Questions.
    case 'H5P.DragQuestion' :
    case 'H5P.Blanks' :
    case 'H5P.MarkTheWords' :
    case 'H5P.DragText' :
    case 'H5P.TrueFalse' :
    case 'H5P.MultiChoice':
    case 'Unknown':
        if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/answered') {
            \logstore_trax\event\hvp_question_answered::create_statement($statement)->trigger();
        }
        break;

    // Quiz.
    case 'H5P.SingleChoiceSet'  :
    case 'H5P.QuestionSet' :
        if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/completed'
            || $statement->verb->id == 'http://adlnet.gov/expapi/verbs/answered') {
            \logstore_trax\event\hvp_quiz_completed::create_statement($statement)->trigger();
        }
        break;

    // Summary.
    case 'H5P.Summary'  :
        if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/answered') {
            \logstore_trax\event\hvp_summary_answered::create_statement($statement)->trigger();
        }
        break;

    // Course Presentation.
    case 'H5P.CoursePresentation'  :
        if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/completed') {
            \logstore_trax\event\hvp_course_presentation_completed::create_statement($statement)->trigger();
        } else if ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/progressed') {
            \logstore_trax\event\hvp_course_presentation_progressed::create_statement($statement)->trigger();
        }
        break;
}

