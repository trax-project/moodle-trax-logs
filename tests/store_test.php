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
require_once(__DIR__ . '/test_utils.php');

/**
 * Unit tests: generate Moodle events and transform them into xAPI statements.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends test_config {

    use test_utils;

    /**
     * Test the overall process.
     */
    public function test_process() {

        // Prepare session.
        $this->prepare_session();

        // Prepare Moodle logs.
        $this->controller->logs->delete_moodle_logs();

        // Trigger events.
        $event = $this->events->user_loggedin();
        $this->trigger($event);

        // Check Moodle logs.
        $logs = $this->controller->logs->get_moodle_logs();
        $this->assertTrue(count($logs) == 1);

        // Process logs.
        $traxlogs = $this->process();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 1);

        // Check error.
        $this->assertTrue(reset($traxlogs)->error == 0);
    }

    /**
     * Test all supported events.
     */
    public function test_events() {

        // Prepare session.
        $this->prepare_session();

        // Trigger events.
        $events = $this->events->all_events();
        $this->trigger($events);

        // Process logs.
        $traxlogs = $this->process();

        // Check logs.
        $this->assertTrue(count($traxlogs) == count($events));
        foreach ($traxlogs as $log) {
            $this->assertTrue($log->error == 0);
        }
    }

}
