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
 * Unit tests: sets of events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests: sets of events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait eventsets {

    /**
     * Get all events.
     * 
     * @return array
     */
    public function all_events() {
        return array_merge(
            $this->core_events(),
            $this->moodle_components_events()
        );
    }

    /**
     * Get core events.
     * 
     * @return array
     */
    public function core_events() {
        return array_merge(
            $this->authentication_events(),
            $this->navigation_events(),
            $this->completion_events(),
            $this->grading_events()
        );
    }

    /**
     * Get authentication events.
     * 
     * @return array
     */
    public function authentication_events() {
        return [
            $this->user_loggedin(),
            $this->user_loggedout(),
        ];
    }

    /**
     * Get navigation events.
     * 
     * @return array
     */
    public function navigation_events() {
        return [
            $this->course_viewed(),
            $this->course_category_viewed(),
        ];
    }

    /**
     * Get completion events.
     * 
     * @return array
     */
    public function completion_events() {
        return [
            $this->course_completed(),
            $this->course_module_completion_updated('scorm', COMPLETION_COMPLETE),
            $this->course_module_completion_updated('scorm', COMPLETION_COMPLETE_PASS),
            $this->course_module_completion_updated('scorm', COMPLETION_COMPLETE_FAIL),
        ];
    }

    /**
     * Get grading events.
     * 
     * @return array
     */
    public function grading_events() {
        return [
            $this->user_graded('scorm', GRADE_TYPE_SCALE, 0.5),
            $this->user_graded('scorm', GRADE_TYPE_VALUE, 25, 0, 100),
            $this->user_graded('scorm', GRADE_TYPE_VALUE, 25, 0, 100, 50),
            $this->user_graded('scorm', GRADE_TYPE_VALUE, 75, 0, 100, 50),
        ];
    }

    /**
     * Get Moodle components events.
     * 
     * @return array
     */
    public function moodle_components_events() {
        $modules = [
            'book', 'chat', 'choice', 'data', 'feedback', 'folder', 'forum', 'glossary',
            'imscp', 'lesson', 'lti', 'page', 'quiz', 'resource', 'scorm', 'survey', 'url',
            'wiki', 'workshop'
        ];
        $events = [];
        foreach ($modules as $module) {
            $method = $module . '_events';
            $events = array_merge($events, $this->$method());
        }
        return $events;
    }

    /**
     * Get book events.
     * 
     * @return array
     */
    public function book_events() {
        return [
            $this->course_module_viewed('book'),
        ];
    }

    /**
     * Get chat events.
     * 
     * @return array
     */
    public function chat_events() {
        return [
            $this->course_module_viewed('chat'),
        ];
    }

    /**
     * Get choice events.
     * 
     * @return array
     */
    public function choice_events() {
        return [
            $this->course_module_viewed('choice'),
        ];
    }

    /**
     * Get data events.
     * 
     * @return array
     */
    public function data_events() {
        return [
            $this->course_module_viewed('data'),
        ];
    }

    /**
     * Get feedback events.
     * 
     * @return array
     */
    public function feedback_events() {
        return [
            $this->course_module_viewed('feedback'),
        ];
    }

    /**
     * Get folder events.
     * 
     * @return array
     */
    public function folder_events() {
        return [
            $this->course_module_viewed('folder'),
        ];
    }

    /**
     * Get forum events.
     * 
     * @return array
     */
    public function forum_events() {
        return [
            $this->course_module_viewed('forum'),
            $this->forum_discussion_viewed(),
            $this->forum_discussion_created(),
            $this->forum_post_created(),
        ];
    }

    /**
     * Get glossary events.
     * 
     * @return array
     */
    public function glossary_events() {
        return [
            $this->course_module_viewed('glossary'),
        ];
    }

    /**
     * Get imscp events.
     * 
     * @return array
     */
    public function imscp_events() {
        return [
            $this->course_module_viewed('imscp'),
        ];
    }

    /**
     * Get lesson events.
     * 
     * @return array
     */
    public function lesson_events() {
        return [
            $this->course_module_viewed('lesson'),
        ];
    }

    /**
     * Get lti events.
     * 
     * @return array
     */
    public function lti_events() {
        return [
            $this->course_module_viewed('lti'),
        ];
    }

    /**
     * Get page events.
     * 
     * @return array
     */
    public function page_events() {
        return [
            $this->course_module_viewed('page'),
        ];
    }

    /**
     * Get quiz events.
     * 
     * @return array
     */
    public function quiz_events() {
        return [
            $this->course_module_viewed('quiz'),
        ];
    }

    /**
     * Get resource events.
     * 
     * @return array
     */
    public function resource_events() {
        return [
            $this->course_module_viewed('resource'),
        ];
    }

    /**
     * Get scorm events.
     * 
     * @return array
     */
    public function scorm_events() {
        return [
            $this->course_module_viewed('scorm'),
        ];
    }

    /**
     * Get survey events.
     * 
     * @return array
     */
    public function survey_events() {
        return [
            $this->course_module_viewed('survey'),
        ];
    }

    /**
     * Get url events.
     * 
     * @return array
     */
    public function url_events() {
        return [
            $this->course_module_viewed('url'),
        ];
    }

    /**
     * Get wiki events.
     * 
     * @return array
     */
    public function wiki_events() {
        return [
            $this->course_module_viewed('wiki'),
        ];
    }

    /**
     * Get workshop events.
     * 
     * @return array
     */
    public function workshop_events() {
        return [
            $this->course_module_viewed('workshop'),
        ];
    }

}
