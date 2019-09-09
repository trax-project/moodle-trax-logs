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
 * Unit tests: errors management.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \logstore_trax\src\config;

require_once(__DIR__ . '/utils/base.php');

/**
 * Unit tests: errors management.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class errors_test extends base {

    /**
     * Test LRS error.
     */
    public function test_lrs_error() {

        // Prepare session.
        $this->prepare_session([
            'lrs_endpoint' => 'http://fakeurl.test',
            'attempts' => 2,
        ]);

        // FIRST ATTEMPT.
        
        // Trigger event and get logs.
        $traxlogs = $this->trigger_simple_event();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $this->assertTrue(reset($traxlogs)->error == 1);


        // SECOND ATTEMPT.
        
        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $log = reset($traxlogs);
        $this->assertTrue($log->error == 1 && $log->attempts == 2);


        // THIRD ATTEMPT.

        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $log = reset($traxlogs);
        $this->assertTrue($log->error == 1 && $log->attempts == 2);


        // FOURTH ATTEMPT.

        // Force a new attempts.
        global $DB;
        $log->newattempt = 1;
        $DB->update_record('logstore_trax_logs', $log);

        // New attempts.
        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $log = reset($traxlogs);
        $this->assertTrue($log->error == 1 && $log->attempts == 3 && $log->newattempt == 0);


        // FIFTH ATTEMPT.

        set_config('lrs_endpoint', $this->lrsendpoint, 'logstore_trax');
        set_config('attempts', 5, 'logstore_trax');
        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $log = reset($traxlogs);
        $this->assertTrue($log->error == 0 && $log->attempts == 4);


        // SIXTH ATTEMPT.

        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $log = reset($traxlogs);
        $this->assertTrue($log->error == 0 && $log->attempts == 4);
    }

    /**
     * Test internal error: user.
     */
    public function test_internal_error() {

        // Prepare session.
        $this->prepare_session([
            'attempts' => 2,
            'actors_identification' => 1,
        ]);

        // FIRST ATTEMPT.

        // Trigger an event without processing it.
        $traxlogs = $this->trigger_simple_event(false);

        // Delete the user.
        global $DB;
        $DB->delete_records('user', ['id' => $this->events->user->id]);

        // Process.
        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $this->assertTrue(reset($traxlogs)->error == 2);


        // SECOND ATTEMPT.

        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $log = reset($traxlogs);
        $this->assertTrue($log->error == 2 && $log->attempts == 1);


        // THIRD ATTEMPT.

        // Force a new attempts.
        global $DB;
        $log->newattempt = 1;
        $DB->update_record('logstore_trax_logs', $log);

        // New attempts.
        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $log = reset($traxlogs);
        $this->assertTrue($log->error == 2 && $log->attempts == 2 && $log->newattempt == 0);
    }

    /**
     * Test internal error: course.
     */
    public function test_internal_error_on_course() {

        // Prepare session.
        $this->prepare_session();

        // Trigger an event without processing it.
        $this->events->course_viewed()->trigger();

        // Delete the course.
        global $DB;
        $DB->delete_records('course', ['id' => $this->events->course->id]);

        // Process.
        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $this->assertTrue(reset($traxlogs)->error == 2);
    }

    /**
     * Test internal error: module.
     */
    public function test_internal_error_on_module() {

        // Prepare session.
        $this->prepare_session();

        // Trigger an event without processing it.
        $this->events->course_module_viewed('lti')->trigger();

        // Delete the module.
        global $DB;
        $DB->delete_records('lti', ['id' => $this->events->module->id]);

        // Process.
        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $this->assertTrue(reset($traxlogs)->error == 2);
    }


}
