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
 * Unit tests: plugin settings.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \logstore_trax\src\config;

require_once(__DIR__ . '/utils/base.php');

/**
 * Unit tests: plugin settings.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_test extends base {

    /**
     * Test the async process.
     */
    public function test_firstlogs() {

        // Prepare session.
        $tomorrow = new DateTime('tomorrow');
        $this->prepare_session([
            'firstlogs' => $tomorrow->format('d/m/Y')
        ]);

        // Trigger event and get logs.
        $traxlogs = $this->trigger_simple_event();

        // Check Trax logs.
        $this->assertTrue(count($traxlogs) == 0);
    }

    /**
     * Test the async process.
     */
    public function test_anonymization() {

        // Prepare session.
        $this->prepare_session();

        // Trigger event and get logs.
        $traxlogs = $this->trigger_simple_event();
        $log = reset($traxlogs);

        // Get the Moodle event.
        global $DB;
        $mlog = $DB->get_record('logstore_standard_log', ['id' => $log->mid]);

        // Transform it into a Statement.
        $mixed = $this->controller->statements->get_from_event($mlog);
        $name = $mixed->statements[0]['actor']['account']['name'];
        
        // Check anonymization.
        $this->assertTrue($name != $this->events->user->username);

        // Disable anonymization.
        set_config('actors_identification', 1, 'logstore_trax');

        // Transform it into a Statement.
        $mixed = $this->controller->statements->get_from_event($mlog);
        $name = $mixed->statements[0]['actor']['account']['name'];
        
        // Check anonymization.
        $this->assertTrue($name == $this->events->user->username);
    }

}
