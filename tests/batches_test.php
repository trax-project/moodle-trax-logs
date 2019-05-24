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
 * Unit tests: batches.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \logstore_trax\src\config;

require_once(__DIR__ . '/utils/base.php');

/**
 * Unit tests: batches.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class batches_test extends base {

    /**
     * Test the sync process.
     */
    public function test_default_sync() {

        // Prepare session.
        $this->prepare_session();

        // Send the batches.
        $this->send_batches();
    }

    /**
     * Test the sync process.
     */
    public function test_one_by_one_sync() {

        // Prepare session.
        $this->prepare_session([
            'xapi_batch_size' => 1
        ]);

        // Send the batches.
        $this->send_batches();
    }

    /**
     * Test the sync process.
     */
    public function test_one_batch_sync() {

        // Prepare session.
        $this->prepare_session([
            'xapi_batch_size' => 100
        ]);

        // Send the batches.
        $this->send_batches();
    }

    /**
     * Send the batches.
     */
    protected function send_batches() {

        // Create a set of events (>100 items).
        $events = $this->events->all_events();
        $events = array_merge($events, $this->events->all_events());
        $events = array_merge($events, $this->events->all_events());
        $events = array_merge($events, $this->events->all_events());
        $events = array_merge($events, $this->events->all_events());

        // Trigger events.
        $this->trigger($events);

        // Process logs.
        $traxlogs = $this->process();

        // Check logs.
        $this->assertTrue(count($traxlogs) == 100);
        foreach ($traxlogs as $log) {
            $this->assertTrue($log->error == 0);
        }
    }


}
