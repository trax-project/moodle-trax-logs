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
     * Config.
     *
     * @var stdClass $config
     */
    protected $config;

    /**
     * Batch size.
     *
     * @var int $batchsize
     */
    protected $batchsize = 1;


    /**
     * Constructor.
     *
     * @param stdClass $config Config
     * @return void
     */
    public function __construct(\stdClass $config) {
        $this->config = $config;
    }

    /**
     * Get the new events to process.
     *
     * @return array
     */
    public function get_events_to_process() {
        global $DB;
        $where = [];
        $params = [];

        // First logs.
        if (!empty($this->config->firstlogs)) {
            $where[] = 'timecreated >= ?';
            $params[] = strtotime(str_replace('/', '-', $this->config->firstlogs));
        }

        // Log status
        $whereStatus = [
            'error IS NULL',
            '(error = 1 AND attempts < ?)',
            'newattempt = 1',
        ];
        $where[] = '(' . implode(' OR ', $whereStatus) . ')';
        $params[] = $this->config->attempts;

        // Components
        $param1 = $this->sql_array(config::selected_core_events($this->config));
        $param2 = $this->sql_array(config::selected_moodle_components($this->config));
        $param3 = $this->sql_array(config::selected_additional_events($this->config));
        $whereComponent = [
            "(component = 'core' AND eventname IN " . $param1 . ')',
            '(component IN ' . $param2 . ')',
            "(component = 'logstore_trax' AND eventname IN " . $param3 . ')',
        ];

        // Additional components
        if (config::other_components_selected($this->config)) {
            $param4 = $this->sql_array(config::selected_moodle_components($this->config));
            $whereComponent[] = "(
                component <> 'core' 
                AND component <> 'logstore_trax'
                AND component NOT IN ' . $param4 . '
            )";
        }

        // All components
        $where[] = '(' . implode(' OR ', $whereComponent) . ')';

        // Final request
        $where = implode(' AND ', $where);
        $sql = "
            SELECT {logstore_standard_log}.*, error, attempts, newattempt, {logstore_trax_logs}.id AS xid
            FROM {logstore_standard_log}
            LEFT JOIN {logstore_trax_logs} ON {logstore_standard_log}.id = {logstore_trax_logs}.mid
            WHERE " . $where . "
            ORDER BY id
        ";
        return $DB->get_records_sql($sql, $params, 0, $this->batchsize);
    }

    /**
     * Get the N last logs.
     *
     * @param int $number Number of logs to return.
     * @return array
     */
    public function get_last_logs(int $number) {
        global $DB;
        $sql = "
            SELECT *
            FROM {logstore_trax_logs}
            ORDER BY id DESC
        ";
        return $DB->get_records_sql($sql, null, 0, $number);
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
     * Log an event.
     *
     * @param stdClass $event event
     * @error int $error error code
     * @return void
     */
    protected function log_event(\stdClass $event, int $error) {
        global $DB;
        if (isset($event->xid) && $event->xid) {

            // Existing log.
            $DB->update_record('logstore_trax_logs', (object)[
                'id' => $event->xid,
                'mid' => $event->mid,
                'error' => $error,
                'attempts' => $event->attempts + 1
            ]);

        } else if (isset($event->id) && $event->id) {

            // New log with a Moodle event.
            $DB->insert_record('logstore_trax_logs', (object)[
                'mid' => $event->id,
                'error' => $error,
            ]);
        }
    }

    /**
     * Convert array to SQL array.
     *
     * @param array $array array
     * @return string
     */
    protected function sql_array(array $array) {
        $array = array_map(function ($item) {
            return "'" . $item . "'";
        }, $array);
        return '(' . implode(', ', $array) . ')';
    }



}
