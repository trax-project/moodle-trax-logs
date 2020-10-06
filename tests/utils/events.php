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
 * Unit tests: testing events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/eventsets.php');
require_once(__DIR__ . '/utils.php');

/**
 * Unit tests: testing events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events {

    use utils, eventsets;


    /**
     * Get user_login_failed event.
     * 
     * @return \core\event\user_login_failed
     */
    public function user_login_failed() {
        return \core\event\user_login_failed::create([
            'other' => ['username' => $this->user->username, 'reason' => 1],
        ]);
    }

    /**
     * Get user_loggedin event.
     * 
     * @return \core\event\user_loggedin
     */
    public function user_loggedin() {
        return \core\event\user_loggedin::create([
            'objectid' => $this->user->id,
            'other' => ['username' => $this->user->username],
        ]);
    }

    /**
     * Get user_loggedout event.
     * 
     * @return \core\event\user_loggedout
     */
    public function user_loggedout() {
        return \core\event\user_loggedout::create([
            'objectid' => $this->user->id,
            'other' => ['username' => $this->user->username],
        ]);
    }

    /**
     * Get course_viewed event.
     * 
     * @return \core\event\course_viewed
     */
    public function course_viewed() {
        $this->set_course();
        return \core\event\course_viewed::create([
            'context' => context_course::instance($this->course->id),
        ]);
    }

    /**
     * Get course_category_viewed event.
     * 
     * @return \core\event\course_category_viewed
     */
    public function course_category_viewed() {
        $this->set_category();
        return \core\event\course_category_viewed::create([
            'objectid' => $this->category->id,
            'context' => context_system::instance(),
        ]);
    }

    /**
     * Get course_module_viewed event.
     * 
     * @param string $module Type of module.
     * @return course_module_viewed
     */
    public function course_module_viewed($module) {
        $this->set_course();
        $this->set_module($module);

        $class = '\mod_' . $module . '\event\course_module_viewed';
        $params = [
            'objectid' => $this->module->id,
            'context' => context_module::instance($this->module->cmid),
        ];
        switch ($module) {
            case 'feedback':
                $params['other'] = ['anonymous' => FEEDBACK_ANONYMOUS_YES];
                break;
            case 'survey':
                $params['other'] = ['viewed' => 'What was viewed'];
                break;
        }
        return $class::create($params);
    }

    /**
     * Get course_completed event.
     * 
     * @return \core\event\course_completed
     */
    public function course_completed() {
        $this->set_course();
        $completion = $this->get_course_completion();
        return \core\event\course_completed::create_from_completion($completion);
    }

    /**
     * Get course_module_completion_updated event.
     * 
     * @return \core\event\course_module_completion_updated
     */
    public function course_module_completion_updated($module, $status) {
        $this->set_course();
        $this->set_module($module);
        $completion = $this->get_module_completion($status);
        return \core\event\course_module_completion_updated::create(array(
            'objectid' => $completion->id,
            'context' => context_module::instance($this->module->cmid),
            'relateduserid' => $this->user->id,
            'other' => array(
                'relateduserid' => $this->user->id,
                'completionstate' => $status
            )
        ));
    }

    /**
     * Get user_graded event.
     * 
     * @return \core\event\user_graded
     */
    public function user_graded($module, $type, $raw, $min = null, $max = null, $pass = null) {
        $this->set_course();
        $this->set_module($module);
        $grade = $this->get_module_grade($module, $type, $raw, $min, $max, $pass);
        return \core\event\user_graded::create_from_grade($grade);
    }

    /**
     * Get forum discussion_viewed event.
     *
     * @return \mod_forum\event\discussion_viewed
     */
    public function forum_discussion_viewed() {
        $this->set_course();
        $this->set_module('forum');
        $discussion = $this->create_forum_discussion();

        return \mod_forum\event\discussion_viewed::create([
            'context' => context_module::instance($this->module->cmid),
            'objectid' => $discussion->id,
        ]);
    }

    /**
     * Get forum discussion_created event.
     *
     * @return \mod_forum\event\discussion_created
     */
    public function forum_discussion_created() {
        $this->set_course();
        $this->set_module('forum');
        $discussion = $this->create_forum_discussion();

        return \mod_forum\event\discussion_created::create([
            'context' => context_module::instance($this->module->cmid),
            'objectid' => $discussion->id,
            'other' => array(
                'forumid' => $this->module->id,
            )
        ]);
    }

    /**
     * Get forum post_created event.
     *
     * @return \mod_forum\event\post_created
     */
    public function forum_post_created() {
        $this->set_course();
        $this->set_module('forum');
        $discussion = $this->create_forum_discussion();
        $post = $this->create_forum_post($discussion);

        return \mod_forum\event\post_created::create([
            'context' => context_module::instance($this->module->cmid),
            'objectid' => $post->id,
            'other' => array(
                'discussionid' => $discussion->id,
                'forumid' => $this->module->id,
                'forumtype' => $this->module->type,
            )
        ]);
    }
}
