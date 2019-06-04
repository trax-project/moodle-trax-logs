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

use logstore_trax\src\config;

/**
 * Logs service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logs {

    /**
     * Get the new events to process.
     *
     * @return array
     */
    public function get_events_to_process() {
        global $DB;
        $config = get_config('logstore_trax');
        $where = [];
        $params = [];

        // First logs.
        if (!empty($config->firstlogs)) {
            $where[] = 'timecreated >= ?';
            $params[] = strtotime(str_replace('/', '-', $config->firstlogs));
        }

        // Log status.
        $whereStatus = [
            'error IS NULL',
            '(error = 1 AND attempts < ?)',
            'newattempt = 1',
        ];
        $where[] = '(' . implode(' OR ', $whereStatus) . ')';
        $params[] = $config->attempts;

        // Selected events.
        $param = $this->sql_array(config::selected_events($config));
        $where[] = "eventname IN " . $param;

        // Final request.
        $where = implode(' AND ', $where);
        $sql = "
            SELECT {logstore_standard_log}.*, error, attempts, newattempt, {logstore_trax_logs}.id AS xid
            FROM {logstore_standard_log}
            LEFT JOIN {logstore_trax_logs} ON {logstore_standard_log}.id = {logstore_trax_logs}.mid
            WHERE " . $where . "
            ORDER BY timecreated
        ";
        
        return $DB->get_records_sql($sql, $params, 0, $config->db_batch_size);
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
     * Log an event.
     *
     * @param stdClass $event event
     * @error int $error error code
     * @return void
     */
    protected function log_event(\stdClass $event, int $error) {

        // Never log in sync mode, except for unit tests.
        if (config::sync() && !PHPUNIT_TEST) {
            return;
        }

        global $DB;
        if (isset($event->xid) && $event->xid) {

            // Existing log.
            $DB->update_record('logstore_trax_logs', (object)[
                'id' => $event->xid,
                'mid' => $event->id,
                'error' => $error,
                'attempts' => $event->attempts + 1,
                'newattempt' => 0
            ]);

        } else if (isset($event->id) && $event->id) {

            // New log with a Moodle event.
            $DB->insert_record('logstore_trax_logs', (object)[
                'mid' => $event->id,
                'error' => $error,
            ]);

        } else {

            // Sync mode.
            $DB->insert_record('logstore_trax_logs', (object)[
                'error' => $error,
            ]);
        }
    }

    /**
     * Clean logs.
     *
     * @return void
     */
    public function clean() {
        global $DB;

        // Remove sync logs.
        $select = 'mid IS NULL';
        $DB->delete_records_select('logstore_trax_logs', $select);
    }

    /**
     * Convert array to SQL array.
     *
     * @param array $array array
     * @return string
     */
    protected function sql_array(array $array) {
        $array = array_map(function ($item) {
            $item = str_replace('\\', '\\\\', $item);
            return "'" . $item . "'";
        }, $array);
        return '(' . implode(', ', $array) . ')';
    }



}
