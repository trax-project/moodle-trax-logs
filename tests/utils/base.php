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

require_once(__DIR__ . '/settings.php');
require_once(__DIR__ . '/events.php');

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

    use settings;

    /**
     * Trax Logs controller.
     *
     * @var controller $controller
     */
    protected $controller;

    /**
     * Testing events.
     *
     * @var events $events
     */
    protected $events;


    /**
     * Prepare test session.
     * 
     * @param array $config Config.
     */
    protected function prepare_session($config = []) {

        // Config (always first).
        $this->set_config($config);

        // Prepare testing context.
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Create a user.
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Utilities.
        $this->controller = new trax_controller();
        $this->controller->logs->delete_trax_logs();
        $this->controller->logs->delete_moodle_logs();
        $this->events = new events($this, $this->getDataGenerator(), $user);
    }

    /**
     * Set default config.
     * 
     * @param array $config Config.
     */
    protected function set_config($config = []) {

        // Enable logstores witout buffers.
        set_config('enabled_stores', 'logstore_standard,logstore_trax', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('buffersize', 0, 'logstore_trax');

        // LRS endpoint.
        $value = isset($config['lrs_endpoint']) ? $config['lrs_endpoint'] : $this->lrsendpoint;
        set_config('lrs_endpoint', $value, 'logstore_trax');

        // LRS username.
        $value = isset($config['lrs_username']) ? $config['lrs_username'] : $this->lrsusername;
        set_config('lrs_username', $value, 'logstore_trax');

        // LRS password.
        $value = isset($config['lrs_password']) ? $config['lrs_password'] : $this->lrspassword;
        set_config('lrs_password', $value, 'logstore_trax');

        // Platform IRI.
        $value = isset($config['platform_iri']) ? $config['platform_iri'] : $this->platformiri;
        set_config('platform_iri', $value, 'logstore_trax');

        // actors_identification.
        $value = isset($config['actors_identification']) ? $config['actors_identification'] : 0;
        set_config('actors_identification', $value, 'logstore_trax');

        // xis_anonymization.
        $value = isset($config['xis_anonymization']) ? $config['xis_anonymization'] : 1;
        set_config('xis_anonymization', $value, 'logstore_trax');

        // First logs to sync.
        // Time 00:00:00 of yesterday will be the begining of today.
        $yesterday = new DateTime('yesterday'); 
        $value = isset($config['firstlogs']) ? $config['firstlogs'] : $yesterday->format('d/m/Y');
        set_config('firstlogs', $value, 'logstore_trax');

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

        // Sync mode.
        $value = isset($config['sync_mode']) ? $config['sync_mode'] : config::ASYNC;
        set_config('sync_mode', $value, 'logstore_trax');

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
    protected function trigger($events)
    {
        if (is_array($events)) {
            foreach ($events as $event) {
                $event->trigger();
            }
        } else {
            $events->trigger();
        }
    }

    /**
     * Process events from the Moodle logstore.
     * 
     * @param bool $debug debug
     * @return array
     */
    protected function process($debug = false)
    {
        $this->controller->process_logstore($debug);
        return $this->controller->logs->get_trax_logs();
    }

    /**
     * Trigger a simple event.
     * 
     * @param bool $process Process the events.
     * @return mixed
     */
    protected function trigger_simple_event($process = true) {
        return $this->trigger_event('user_loggedin', $process);
    }

    /**
     * Trigger a named event.
     * 
     * @param string $name Name of the events.
     * @param bool $process Process the events.
     * @return mixed
     */
    protected function trigger_event($name, $process = true) {
        $event = $this->events->$name();
        $this->trigger($event);
        if ($process) {
            return $this->process();
        }
    }

}
