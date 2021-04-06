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
 * Unit tests: utils.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests: utils.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait utils {

    /**
     * Test case.
     *
     * @var advanced_testcase $testcase
     */
    public $testcase;

    /**
     * Data generator.
     *
     * @var stdClass $generator
     */
    public $generator;

    /**
     * The user who triggers the events.
     *
     * @var stdClass $user
     */
    public $user;

    /**
     * The course in which the events occur.
     *
     * @var stdClass $course
     */
    public $course;

    /**
     * The course category in which the events occur.
     *
     * @var stdClass $category
     */
    public $category;

    /**
     * The course on which the events occur.
     *
     * @var stdClass $module
     */
    public $module;


    /**
     * Constructor.
     * 
     * @param advanced_testcase $testcase Test case.
     * @param stdClass $generator Data generator.
     * @param stdClass $user The user who triggers the events.
     */
    public function __construct($testcase, $generator, $user) {
        $this->testcase = $testcase;
        $this->generator = $generator;
        $this->user = $user;
    }

    /**
     * Define a course.
     */
    protected function set_course() {
        if (isset($this->course)) return;
        $sink = $this->testcase->redirectEvents();
        $this->course = $this->generator->create_course(['enablecompletion' => true]);
        $this->generator->enrol_user($this->user->id, $this->course->id);
        $sink->close();
    }

    /**
     * Define a course category.
     */
    protected function set_category() {
        if (isset($this->category)) return;
        $sink = $this->testcase->redirectEvents();
        $this->category = $this->generator->create_category();
        $sink->close();
    }

    /**
     * Define a module.
     */
    protected function set_module($module) {
        $sink = $this->testcase->redirectEvents();
        $this->module = $this->generator->create_module(
            $module, 
            ['course' => $this->course->id], 
            ['completion' => COMPLETION_TRACKING_AUTOMATIC]
        );
        $sink->close();
    }

    /**
     * Create a forum discussion.
     */
    protected function create_forum_discussion() {
        $sink = $this->testcase->redirectEvents();
        $discussion = $this->generator->get_plugin_generator('mod_forum')->create_discussion([
            'course' => $this->course->id,
            'forum' => $this->module->id,
            'userid' => $this->user->id,
        ]);
        $sink->close();
        return $discussion;
    }

    /**
     * Create a forum post.
     */
    protected function create_forum_post($discussion) {
        $sink = $this->testcase->redirectEvents();
        $post = $this->generator->get_plugin_generator('mod_forum')->create_post([
            'discussion' => $discussion->id,
            'userid' => $this->user->id,
        ]);
        $sink->close();
        return $post;
    }

    /**
     * Get course completion.
     */
    protected function get_course_completion() {
        $sink = $this->testcase->redirectEvents();

        // Get existing completion.
        global $DB;
        $data = $DB->get_record('course_completions', ['course' => $this->course->id, 'userid' => $this->user->id]);

        // Create one.
        if (!$data) {
            $completion = new completion_completion([
                'course' => $this->course->id,
                'userid' => $this->user->id,
                'timeenrolled' => time(),
                'timestarted' => time(),
                'reaggregate' => time(),
            ]);
            $completion->insert();
            $data = $completion->get_record_data();
        }
        $sink->close();
        return $data;
    }


    /**
     * Get module completion.
     */
    protected function get_module_completion($status) {
        global $DB;
        $sink = $this->testcase->redirectEvents();
        $cinfo = new completion_info($this->course);
        $activities = $cinfo->get_activities();
        $completion = $cinfo->get_data($activities[$this->module->cmid], false, $this->user->id);
        $completion->completionstate = $status;
        $completion->timemodified = time();
        $completion->id = $DB->insert_record('course_modules_completion', $completion);
        $sink->close();
        return $completion;
    }

    /**
     * Get module grade.
     */
    protected function get_module_grade($module, $type, $raw, $min = null, $max = null, $pass = null) {
        $sink = $this->testcase->redirectEvents();

        // Define grade item.
        $gradeitem = \grade_item::fetch([
            'itemtype' => 'mod',
            'itemmodule' => $module,
            'iteminstance' => $this->module->id,
            'courseid' => $this->course->id,
        ]);
        if (!$gradeitem) {
            $gradeitem = new \grade_item([
                'itemtype' => 'mod',
                'itemmodule' => $module,
                'iteminstance' => $this->module->id,
                'courseid' => $this->course->id,
            ]);
            $gradeitem->id = $gradeitem->insert();
        }
        $gradeitem->gradetype = $type;
        if (isset($min)) {
            $gradeitem->grademin = $min;
        }
        if (isset($max)) {
            $gradeitem->grademax = $max;
        }
        if (isset($pass)) {
            $gradeitem->gradepass = $pass;
        }
        $gradeitem->update();

        // Define grade.
        $grade = \grade_grade::fetch([
            'itemid' => $gradeitem->id,
            'userid' => $this->user->id
        ]);
        if (!$grade) {
            $grade = new \grade_grade([
                'itemid' => $gradeitem->id,
                'userid' => $this->user->id,
                'rawgrade' => $raw,
            ]);
            $grade->id = $grade->insert('mod/' . $module);
        } else {
            $grade->rawgrade = $raw;
            $grade->update('mod/' . $module);
        }

        $sink->close();
        return $grade;
    }

}
