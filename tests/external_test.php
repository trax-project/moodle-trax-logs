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
 * Unit tests: external services.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/utils/base.php');

/**
 * Unit tests: external services.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_test extends base {

    /**
     * Get actors and activities used by a statement.
     */
    public function test_get_actor_and_activity() {

        // Prepare session.
        $this->prepare_session();

        // Trigger events.
        $event = $this->events->course_module_viewed('lti');
        $this->trigger($event);

        // Process logs.
        $traxlogs = $this->process();

        // Get user without name.
        $actor = $this->controller->actors->get_existing('user', $this->events->user->id, false);
        $this->assertTrue($actor && isset($actor['account']) && isset($actor['account']['name']) && !isset($actor['name']));
        $this->assertTrue($actor['account']['name'] != $this->events->user->username);

        // Get user with name when it is not allowed.
        $actor = $this->controller->actors->get_existing('user', $this->events->user->id, true);
        $this->assertTrue($actor && isset($actor['account']) && isset($actor['account']['name']) && !isset($actor['name']));
        $this->assertTrue($actor['account']['name'] != $this->events->user->username);

        // Get user with name when it is allowed.
        set_config('xis_anonymization', 0, 'logstore_trax');
        $actor = $this->controller->actors->get_existing('user', $this->events->user->id, true);
        $this->assertTrue($actor && isset($actor['name']) && isset($actor['account']) && isset($actor['account']['name']));
        $this->assertTrue($actor['name'] == $this->events->user->firstname . ' ' . $this->events->user->lastname);
        $this->assertTrue($actor['account']['name'] == $this->events->user->username);

        // Get system without name.
        $system = $this->controller->activities->get_existing('system', 0, false);
        $this->assertTrue($system && isset($system['id']));
        $this->assertTrue(!isset($system['definition']['name']));

        // Get system with name.
        $system = $this->controller->activities->get_existing('system', 0, true);
        $this->assertTrue($system && isset($system['id']));
        $this->assertTrue(isset($system['definition']['name']));

        // Get course.
        $course = $this->controller->activities->get_existing('course', $this->events->course->id, false);
        $this->assertTrue($course && isset($course['id']));

        // Get existing module.
        $module = $this->controller->activities->get_existing('lti', $this->events->module->id, false);
        $this->assertTrue($module && isset($module['id']));

        // Get non existing module.
        try {
            $module = $this->controller->activities->get_existing('lti', 65416871984164, false);
            $this->assertTrue(false);
        } catch (\moodle_exception $e) {
            $this->assertTrue(true);
        }
    }

}
