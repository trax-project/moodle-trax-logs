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
 * Unit tests: generate Moodle events and transform them into xAPI statements.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/test_config.php');

/**
 * Unit tests: generate Moodle events and transform them into xAPI statements.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends test_config {
        
    /**
     * A collection of events.
     */
    public function test_access_events() {
        $user = $this->prepare_session();
        $course = $this->getDataGenerator()->create_course();
        
        \core\event\user_loggedin::create([
            'objectid' => $user->id,
            'other' => ['username' => $user->username],
        ])->trigger();
        
        \core\event\course_viewed::create([
            'context' => context_course::instance($course->id), 
        ])->trigger();

        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        \mod_book\event\course_module_viewed::create([
            'objectid' => $book->id,
            'context' => context_module::instance($book->cmid), 
        ])->trigger();

        $chat = $this->getDataGenerator()->create_module('chat', array('course' => $course->id));
        \mod_chat\event\course_module_viewed::create([
            'objectid' => $chat->id,
            'context' => context_module::instance($chat->cmid), 
        ])->trigger();

        $choice = $this->getDataGenerator()->create_module('choice', array('course' => $course->id));
        \mod_choice\event\course_module_viewed::create([
            'objectid' => $choice->id,
            'context' => context_module::instance($choice->cmid), 
        ])->trigger();

        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id));
        \mod_data\event\course_module_viewed::create([
            'objectid' => $data->id,
            'context' => context_module::instance($data->cmid), 
        ])->trigger();

        $feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $course->id));
        \mod_feedback\event\course_module_viewed::create([
            'objectid' => $feedback->id,
            'context' => context_module::instance($feedback->cmid), 
            'other' => ['anonymous' => FEEDBACK_ANONYMOUS_YES],
        ])->trigger();

        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id));
        \mod_folder\event\course_module_viewed::create([
            'objectid' => $folder->id,
            'context' => context_module::instance($folder->cmid), 
        ])->trigger();

        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        \mod_forum\event\course_module_viewed::create([
            'objectid' => $forum->id,
            'context' => context_module::instance($forum->cmid), 
        ])->trigger();

        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));
        \mod_glossary\event\course_module_viewed::create([
            'objectid' => $glossary->id,
            'context' => context_module::instance($glossary->cmid), 
        ])->trigger();

        $imscp = $this->getDataGenerator()->create_module('imscp', array('course' => $course->id));
        \mod_imscp\event\course_module_viewed::create([
            'objectid' => $imscp->id,
            'context' => context_module::instance($imscp->cmid), 
        ])->trigger();

        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id));
        \mod_lesson\event\course_module_viewed::create([
            'objectid' => $lesson->id,
            'context' => context_module::instance($lesson->cmid), 
        ])->trigger();

        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));
        \mod_lti\event\course_module_viewed::create([
            'objectid' => $lti->id,
            'context' => context_module::instance($lti->cmid), 
        ])->trigger();

        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        \mod_page\event\course_module_viewed::create([
            'objectid' => $page->id,
            'context' => context_module::instance($page->cmid), 
        ])->trigger();

        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));
        \mod_quiz\event\course_module_viewed::create([
            'objectid' => $quiz->id,
            'context' => context_module::instance($quiz->cmid), 
        ])->trigger();

        $resource = $this->getDataGenerator()->create_module('resource', array('course' => $course->id));
        \mod_resource\event\course_module_viewed::create([
            'objectid' => $resource->id,
            'context' => context_module::instance($resource->cmid), 
        ])->trigger();

        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id));
        \mod_scorm\event\course_module_viewed::create([
            'objectid' => $scorm->id,
            'context' => context_module::instance($scorm->cmid), 
        ])->trigger();

        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));
        \mod_survey\event\course_module_viewed::create([
            'objectid' => $survey->id,
            'context' => context_module::instance($survey->cmid), 
            'other' => ['viewed' => 'What was viewed'],
        ])->trigger();

        $url = $this->getDataGenerator()->create_module('url', array('course' => $course->id));
        \mod_url\event\course_module_viewed::create([
            'objectid' => $url->id,
            'context' => context_module::instance($url->cmid), 
        ])->trigger();

        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id));
        \mod_wiki\event\course_module_viewed::create([
            'objectid' => $wiki->id,
            'context' => context_module::instance($wiki->cmid), 
        ])->trigger();

        $workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $course->id));
        \mod_workshop\event\course_module_viewed::create([
            'objectid' => $workshop->id,
            'context' => context_module::instance($workshop->cmid), 
        ])->trigger();

        \core\event\user_loggedout::create([
            'objectid' => $user->id,
            'other' => ['username' => $user->username],
        ])->trigger();
    }

}
