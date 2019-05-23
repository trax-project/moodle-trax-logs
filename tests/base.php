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

require_once(__DIR__ . '/lrs_config.php');
require_once(__DIR__ . '/events_generator.php');

use \logstore_trax\src\controller as trax_controller;
use \logstore_trax\src\config;

/**
 * Utilities for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base extends advanced_testcase {

    use lrs_config;

    /**
     * Logstores.
     *
     * @var \tool_log\log\writer $stores
     */
    protected $stores;

    /**
     * Trax Logs controller.
     *
     * @var controller $controller
     */
    protected $controller;

    /**
     * Testing events.
     *
     * @var events_generator $events
     */
    protected $events;


    /**
     * Prepare test session.
     * 
     * @param array $config Config.
     */
    protected function prepare_session($config = []) {

        // Config (always first).
        $this->set_default_config($config);

        // Prepare testing context.
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Create a user.
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Enable standard logstore.
        set_config('enabled_stores', 'logstore_standard,logstore_trax', 'tool_log');
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        $this->stores = $stores['logstore_standard'];

        // Utilities.
        $this->controller = new trax_controller();
        $this->controller->logs->delete_trax_logs();
        $this->controller->logs->delete_moodle_logs();
        $this->events = new events_generator($this->getDataGenerator(), $user);
    }

    /**
     * Set default config.
     * 
     * @param array $config Config.
     */
    protected function set_default_config($config = []) {

        // LRS settings.
        set_config('lrs_endpoint', $this->lrsendpoint, 'logstore_trax');
        set_config('lrs_username', $this->lrsusername, 'logstore_trax');
        set_config('lrs_password', $this->lrspassword, 'logstore_trax');

        // Other settings.

        // Anonymization.
        $value = isset($config['anonymization']) ? $config['anonymization'] : 1;
        set_config('anonymization', $value, 'logstore_trax');

        // xis_provide_names.
        $value = isset($config['xis_provide_names']) ? $config['xis_provide_names'] : 0;
        set_config('xis_provide_names', $value, 'logstore_trax');

        // Sync mode.
        $value = isset($config['sync_mode']) ? $config['sync_mode'] : config::ASYNC;
        set_config('sync_mode', $value, 'logstore_trax');

        // Core events.
        $coreevents = implode(',', array_keys(array_filter(config::default_core_events())));
        $value = isset($config['core_events']) ? $config['core_events'] : $coreevents;
        set_config('core_events', $value, 'logstore_trax');

        // Moodle components.
        $moodlecomp = implode(',', array_keys(array_filter(config::default_moodle_components())));
        $value = isset($config['moodle_components']) ? $config['moodle_components'] : $moodlecomp;
        set_config('moodle_components', $value, 'logstore_trax');

        // Additional components.
        $addcomp = implode(',', array_keys(array_filter(config::default_additional_components())));
        $value = isset($config['additional_components']) ? $config['additional_components'] : $addcomp;
        set_config('additional_components', $value, 'logstore_trax');

        // First logs to sync.
        $value = isset($config['firstlogs']) ? $config['firstlogs'] : date('d/m/Y');
        set_config('firstlogs', $value, 'logstore_trax');

        // Number of attempts.
        $value = isset($config['attempts']) ? $config['attempts'] : 1;
        set_config('attempts', $value, 'logstore_trax');

        // Database batch size.
        $value = isset($config['db_batch_size']) ? $config['db_batch_size'] : 100;
        set_config('db_batch_size', $value, 'logstore_trax');

        // xAPI batch size.
        $value = isset($config['xapi_batch_size']) ? $config['xapi_batch_size'] : 10;
        set_config('xapi_batch_size', $value, 'logstore_trax');
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
        $this->stores->flush();
    }

    /**
     * Process events from the Moodle logstore.
     * 
     * @return array
     */
    public function process()
    {
        $this->controller->process_logstore();
        return $this->controller->logs->get_trax_logs();
    }

}
