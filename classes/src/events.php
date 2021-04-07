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
 * Define the supported events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src;

defined('MOODLE_INTERNAL') || die();

/**
 * Define the supported events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events {

    /**
     * Core events.
     *
     * @return array
     */
    public static function core() {
        return [
            'management' => [
                '\core\event\user_enrolment_created',
            ],
            'authentication' => [
                '\core\event\user_loggedin',
                '\core\event\user_loggedout',
            ],
            'navigation' => [
                '\core\event\course_viewed',
                '\core\event\course_category_viewed',
            ],
            'completion' => [
                '\core\event\course_completed',
                '\core\event\course_module_completion_updated',
            ],
            'grading' => [
                '\core\event\user_graded',
            ],
        ];
    }

    /**
     * Moodle components events.
     *
     * @return array
     */
    public static function moodle_components() {
        return [
            'mod_assign' => [
            ],
            'mod_book' => [
                '\mod_book\event\course_module_viewed',
            ],
            'mod_chat' => [
                '\mod_chat\event\course_module_viewed',
            ],
            'mod_choice' => [
                '\mod_choice\event\course_module_viewed',
            ],
            'mod_data' => [
                '\mod_data\event\course_module_viewed',
            ],
            'mod_feedback' => [
                '\mod_feedback\event\course_module_viewed',
            ],
            'mod_folder' => [
                '\mod_folder\event\course_module_viewed',
            ],
            'mod_forum' => [
                '\mod_forum\event\course_module_viewed',
                '\mod_forum\event\discussion_viewed',
                '\mod_forum\event\discussion_created',
                '\mod_forum\event\post_created',
            ],
            'mod_glossary' => [
                '\mod_glossary\event\course_module_viewed',
            ],
            'mod_imscp' => [
                '\mod_imscp\event\course_module_viewed',
            ],
            'mod_lesson' => [
                '\mod_lesson\event\course_module_viewed',
            ],
            'mod_lti' => [
                '\mod_lti\event\course_module_viewed',
            ],
            'mod_page' => [
                '\mod_page\event\course_module_viewed',
            ],
            'mod_quiz' => [
                '\mod_quiz\event\course_module_viewed',
            ],
            'mod_resource' => [
                '\mod_resource\event\course_module_viewed',
            ],
            'mod_scorm' => [
                '\mod_scorm\event\course_module_viewed',
                '\mod_scorm\event\sco_launched',
            ],
            'mod_survey' => [
                '\mod_survey\event\course_module_viewed',
            ],
            'mod_url' => [
                '\mod_url\event\course_module_viewed',
            ],
            'mod_wiki' => [
                '\mod_wiki\event\course_module_viewed',
            ],
            'mod_workshop' => [
                '\mod_workshop\event\course_module_viewed',
            ],
            'mod_h5pactivity' => [
                '\mod_h5pactivity\event\course_module_viewed',
                '\logstore_trax\event\hvp_question_answered',
                '\logstore_trax\event\hvp_quiz_completed',
                '\logstore_trax\event\hvp_summary_answered',
                '\logstore_trax\event\hvp_course_presentation_completed',
                '\logstore_trax\event\hvp_course_presentation_progressed',
            ],
        ];
    }

    /**
     * Additional components events.
     *
     * @return array
     */
    public static function additional_components() {
        return [
        ];
    }
    
    /**
     * Scheduled statements.
     *
     * @return array
     */
    public static function scheduled_statements() {
        return [
            'define_groups' => [
                '\logstore_trax\event\cohort_defined',
            ],
            'define_courses' => [
                '\logstore_trax\event\course_defined',
            ],
        ];
    }
    

}
