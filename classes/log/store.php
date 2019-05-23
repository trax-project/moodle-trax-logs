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
 * Entry point of the log store plugin.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\log;

defined('MOODLE_INTERNAL') || die();

use \core\event\base as moodle_event;
use \tool_log\log\writer as log_writer;
use \tool_log\log\manager as log_manager;
use \tool_log\helper\store as helper_store;
use \tool_log\helper\buffered_writer as helper_writer;
use \logstore_trax\src\controller as trax_controller;
use logstore_trax\src\config;


/**
 * Entry point of the log store plugin.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store implements log_writer {
    use helper_store;
    use helper_writer;


    /**
     * Constructs a new store.
     *
     * @param log_manager $manager
     */
    public function __construct(log_manager $manager) {
        $this->helper_setup($manager);
    }

    /**
     * Should the event be ignored (== not logged)?
     * 
     * @param \core\event\base $event
     * @return bool
     */
    protected function is_event_ignored(\core\event\base $event) {
        return !config::sync() || (CLI_SCRIPT && !PHPUNIT_TEST) || !isloggedin();
    }

    /**
     * Bulk write a given array of events to the backend. Stores must implement this.
     *
     * @param array $evententries raw event data
     */
    protected function insert_event_entries(array $evententries) {
        (new trax_controller())->process_events($evententries);
    }

}
