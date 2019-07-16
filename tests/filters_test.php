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
 * Unit tests: filtering test.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \logstore_trax\src\config;

require_once(__DIR__ . '/utils/base.php');

/**
 * Unit tests: filtering test.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filters_test extends base {

    /**
     * Test core events filters.
     */
    public function test_core_events() {

        // Prepare session.
        $this->prepare_session([
            'core_events' => ''
        ]);

        // Trigger events.
        $events = $this->events->core_events();
        $this->trigger($events);

        // Process logs.
        $traxlogs = $this->process();

        // Check logs.
        $this->assertTrue(count($traxlogs) == count($events));
        foreach ($traxlogs as $log) {
            $this->assertTrue($log->error == 4);
        }
    }

    /**
     * Test moodle events filters.
     */
    public function test_moodle_components_events() {

        // Prepare session.
        $this->prepare_session([
            'moodle_components' => ''
        ]);

        // Trigger events.
        $events = $this->events->moodle_components_events();
        $this->trigger($events);

        // Process logs.
        $traxlogs = $this->process();

        // Check logs.
        $this->assertTrue(count($traxlogs) == count($events));
        foreach ($traxlogs as $log) {
            $this->assertTrue($log->error == 4);
        }
    }

}
