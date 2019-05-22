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
 * Unit tests for external services.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/test_config.php');
require_once(__DIR__ . '/test_utils.php');

/**
 * Unit tests for external services.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_test extends test_config {

    use test_utils;

    /**
     * Get actors and activities used by a statement.
     */
    public function test_get_actor_and_activity() {

        // Generate data.
        $user = $this->prepare_session();
        $course = $this->getDataGenerator()->create_course();
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Send a Statement.
        \mod_lti\event\course_module_viewed::create([
            'objectid' => $lti->id,
            'context' => context_module::instance($lti->cmid),
        ])->trigger();

        // User without name.
        $actor = $this->controller->actors->get_existing('user', $user->id, false);
        $this->assertTrue($actor && isset($actor['account']) && isset($actor['account']['name']));
        $this->assertTrue(!isset($actor['name']));

        // User with name.
        $actor = $this->controller->actors->get_existing('user', $user->id, true);
        $this->assertTrue($actor && isset($actor['account']) && isset($actor['account']['name']));
        $this->assertTrue(isset($actor['name']));
        $this->assertTrue($actor['name'] == $user->firstname . ' ' . $user->lastname);

        // System.
        $activity = $this->controller->activities->get_existing('system', 0, false);
        $this->assertTrue($activity && isset($activity['id']));

        // Course.
        $activity = $this->controller->activities->get_existing('course', $course->id, false);
        $this->assertTrue($activity && isset($activity['id']));

        // LTI module.
        $activity = $this->controller->activities->get_existing('lti', $lti->id, false);
        $this->assertTrue($activity && isset($activity['id']));

        // Non existing module.
        try {
            $activity = $this->controller->activities->get_existing('lti', 65416871984164, false);
            $this->assertTrue(false);
        } catch (\moodle_exception $e) {
            $this->assertTrue(true);
        }
    }

}
