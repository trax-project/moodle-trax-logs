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
 * Unit tests: async test.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \logstore_trax\src\config;

require_once(__DIR__ . '/utils/base.php');

/**
 * Unit tests: async test.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class async_test extends base {

    /**
     * Test LRS error.
     */
    public function test_lrs_error() {


        // SET THE INITIAL CONTEXT ----------------------------------------------------------------------------------.

        // Settings.
        $this->prepare_session([
            'attempts' => 1,
            'core_events' => 'authentication'
        ]);

        // First section (past logs)
        // 3 events which are not processed.
        $time1 = time();
        $this->trigger_simple_event(false);
        $this->trigger_simple_event(false);
        $this->trigger_simple_event(false);
        sleep(1);

        // Time.
        $time2 = time();
        $date = date('d/m/Y H:i:s', $time2);
        set_config('firstlogs', $date, 'logstore_trax');

        // Second section (processed logs)
        // 3 events successful processed.
        // 1 event with an LRS error (error 1).
        // 1 unselected event (error 4).
        $this->trigger_simple_event();
        $this->trigger_simple_event();
        $this->trigger_simple_event();
        set_config('lrs_endpoint', 'http://fakeurl.test', 'logstore_trax');
        $this->trigger_simple_event();
        set_config('lrs_endpoint', $this->lrsendpoint, 'logstore_trax');
        $this->trigger_event('course_viewed');
        sleep(1);

        // Last section (new logs)
        // 2 events which are not processed.
        $time3 = time();
        $this->trigger_simple_event(false);
        $this->trigger_simple_event(false);

        // Check initial situation.
        $traxlogs = $this->controller->logs->get_trax_logs();
        $this->assertTrue(count($traxlogs) == 5);
        foreach($traxlogs as $log) {
            switch ($log->id) {
                case 1:
                    $this->assertTrue($log->mid == 4);
                    $this->assertTrue($log->error == 0);
                    break;
                case 2:
                    $this->assertTrue($log->mid == 5);
                    $this->assertTrue($log->error == 0);
                    break;
                case 3:
                    $this->assertTrue($log->mid == 6);
                    $this->assertTrue($log->error == 0);
                    break;
                case 4:
                    $this->assertTrue($log->mid == 7);
                    $this->assertTrue($log->error == 1);
                    break;
                case 5:
                    $this->assertTrue($log->mid == 8);
                    $this->assertTrue($log->error == 4);
                    break;
            }
        }


        // NEW ROUND ----------------------------------------------------------------------------------.

        // Settings.
        set_config('firstlogs', (new DateTime('yesterday'))->format('d/m/Y'), 'logstore_trax');
        set_config('attempts', 2, 'logstore_trax');
        set_config('core_events', 'authentication,navigation', 'logstore_trax');

        // Process.
        $traxlogs = $this->process();
        $this->assertTrue(count($traxlogs) == 10);
        foreach ($traxlogs as $log) {
            switch ($log->id) {
                case 1:
                    $this->assertTrue($log->mid == 4);
                    $this->assertTrue($log->error == 0);
                    break;
                case 2:
                    $this->assertTrue($log->mid == 5);
                    $this->assertTrue($log->error == 0);
                    break;
                case 3:
                    $this->assertTrue($log->mid == 6);
                    $this->assertTrue($log->error == 0);
                    break;
                case 4:
                    $this->assertTrue($log->mid == 7);
                    $this->assertTrue($log->error == 0);
                    break;
                case 5:
                    $this->assertTrue($log->mid == 8);
                    $this->assertTrue($log->error == 0);
                    break;
                case 6:
                    $this->assertTrue($log->mid == 9);
                    $this->assertTrue($log->error == 0);
                    break;
                case 7:
                    $this->assertTrue($log->mid == 10);
                    $this->assertTrue($log->error == 0);
                    break;
                case 8:
                    $this->assertTrue($log->mid == 3);
                    $this->assertTrue($log->error == 0);
                    break;
                case 9:
                    $this->assertTrue($log->mid == 2);
                    $this->assertTrue($log->error == 0);
                    break;
                case 10:
                    $this->assertTrue($log->mid == 1);
                    $this->assertTrue($log->error == 0);
                    break;
            }
        }
    }

}
