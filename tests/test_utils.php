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
 * Utilities for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/test_events.php');

use \logstore_trax\src\controller as trax_controller;
use \logstore_trax\src\config;

/**
 * Utilities for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait test_utils {

    /**
     * Trax Logs controller.
     *
     * @var controller $controller
     */
    protected $controller;

    /**
     * Moodle standard store.
     *
     * @var \tool_log\log\writer $store
     */
    protected $store;

    /**
     * Testing events.
     *
     * @var test_events $events
     */
    protected $events;


    /**
     * Prepare test session.
     */
    protected function prepare_session() {

        // Config (always first).
        $this->set_default_config();

        // Prepare testing context.
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Create a user.
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Enable standard logstore.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        $this->store = $stores['logstore_standard'];

        // Utilities.
        $this->controller = new trax_controller();
        $this->events = new test_events($this->getDataGenerator(), $user);
    }

    /**
     * Set default config.
     */
    protected function set_default_config() {

        // Enable logging plugin and configure it.
        set_config('lrs_endpoint', $this->lrsendpoint, 'logstore_trax');
        set_config('lrs_username', $this->lrsusername, 'logstore_trax');
        set_config('lrs_password', $this->lrspassword, 'logstore_trax');
        set_config('platform_iri', $this->platformiri, 'logstore_trax');

        // Default settings
        set_config('anonymization', 1, 'logstore_trax');
        set_config('xis_provide_names', 0, 'logstore_trax');
        $coreevents = implode(',', array_keys(array_filter(config::default_core_events())));
        set_config('core_events', $coreevents, 'logstore_trax');
        $moodlecomp = implode(',', array_keys(array_filter(config::default_moodle_components())));
        set_config('moodle_components', $moodlecomp, 'logstore_trax');
        $addcomp = implode(',', array_keys(array_filter(config::default_additional_components())));
        set_config('additional_components', $addcomp, 'logstore_trax');
        set_config('firstlogs', date('d/m/Y'), 'logstore_trax');
        set_config('attempts', 1, 'logstore_trax');
        set_config('dbbatchsize', 100, 'logstore_trax');
        set_config('xapibatchsize', 10, 'logstore_trax');
    }

    /**
     * Trigger an array of events.
     * 
     * @param mixed $events The events to trigger.
     * @return void
     */
    public function trigger($events)
    {
        if (is_array($events)) {
            foreach ($events as $event) {
                $event->trigger();
            }
        } else {
            $events->trigger();
        }
        $this->store->flush();
    }

    /**
     * Process events from the Moodle logstore.
     * 
     * @return array
     */
    public function process()
    {
        $this->controller->logs->delete_trax_logs();
        $this->controller->process_logstore();
        return $this->controller->logs->get_trax_logs();
    }

}
