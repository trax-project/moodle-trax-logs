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
 * Logs service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\services;

defined('MOODLE_INTERNAL') || die();

/**
 * Logs service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logs {

    use logs_requests;


    /**
     * @var stdClass $config
     */
    protected $config;

    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->config = get_config('logstore_trax');
    }

    /**
     * Get the new events to process.
     *
     * @param bool $debug debug
     * @return array
     */
    public function get_events_to_process($debug = false) {

        // Reset config (needed for tests).
        $this->config = get_config('logstore_trax');

        // Get batch.
        $batch = [];
        $this->get_retry_events($batch);
        $this->get_new_events($batch);
        $this->get_past_events($batch);
        $this->get_newly_selected_events($batch);
        return $batch;
    }

    /**
     * Get the Trax logs.
     *
     * @return array
     */
    public function get_trax_logs() {
        global $DB;
        return $DB->get_records('logstore_trax_logs');
    }

    /**
     * Get the Moodle logs.
     *
     * @return array
     */
    public function get_moodle_logs() {
        global $DB;
        return $DB->get_records('logstore_standard_log');
    }

    /**
     * Delete the Trax logs table.
     *
     * @return void
     */
    public function delete_trax_logs() {
        global $DB;
        $DB->delete_records('logstore_trax_logs');
    }

    /**
     * Delete the Moodle logs table.
     *
     * @return void
     */
    public function delete_moodle_logs() {
        global $DB;
        $DB->delete_records('logstore_standard_log');
    }

    /**
     * Log a success.
     *
     * @param stdClass $event event
     * @return void
     */
    public function log_success(\stdClass $event) {
        $this->log_event($event, 0);
    }

    /**
     * Log an LRS error.
     *
     * @param stdClass $event event
     * @return void
     */
    public function log_lrs_error(\stdClass $event) {
        $this->log_event($event, 1);
    }

    /**
     * Log an internal error.
     *
     * @param stdClass $event event
     * @return void
     */
    public function log_internal_error(\stdClass $event) {
        $this->log_event($event, 2);
    }

    /**
     * Log an unsupported event.
     *
     * @param stdClass $event event
     * @return void
     */
    public function log_unsupported(\stdClass $event) {
        $this->log_event($event, 3);
    }

    /**
     * Log an unselected event.
     *
     * @param stdClass $event event
     * @return void
     */
    public function log_unselected(\stdClass $event) {
        $this->log_event($event, 4);
    }

    /**
     * Clean logs.
     *
     * @return void
     */
    public function clean() {
        global $DB;

        // Remove synchronous logs (created during testing).
        $select = 'mid IS NULL';
        $DB->delete_records_select('logstore_trax_logs', $select);
    }


}
