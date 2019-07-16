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
 * Unit tests: supported events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \logstore_trax\src\config;

global $CFG;
require_once(__DIR__ . '/../../../../../../config.php');
require_once(__DIR__ . '/utils/base.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/gradelib.php');

/**
 * Unit tests: supported events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends base {

    /**
     * Test all supported events.
     */
    public function test_all_events() {

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
