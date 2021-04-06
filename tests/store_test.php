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
 * Unit tests: synchronization process.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \logstore_trax\src\config;

require_once(__DIR__ . '/utils/base.php');

/**
 * Unit tests: synchronization process.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends base {

    /**
     * Test the sync process.
     */
    public function test_sync_process() {

        // Prepare session.
        $this->prepare_session([
            'sync_mode' => config::SYNC
        ]);

        // Trigger events.
        $event = $this->events->user_loggedin();
        $this->trigger($event);

        // Check Trax logs.
        $traxlogs = $this->controller->logs->get_trax_logs();

        // Search 1 successed operation. Others may fail (e.g. user_created).
        $found = false;
        foreach ($traxlogs as $log) {
            if ($log->error == 0) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        // Clean logs (don't keep sync logs).
        $this->controller->logs->clean();

        // Check that logs are clean.
        $traxlogs = $this->controller->logs->get_trax_logs();
        $this->assertTrue(count($traxlogs) == 0);
    }

    /**
     * Test the async process.
     */
    public function test_async_process() {

        // Prepare session.
        $this->prepare_session();

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

}
