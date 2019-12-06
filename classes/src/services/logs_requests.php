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
 * Logs sync requests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\services;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\config;

/**
 * Logs sync requests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait logs_requests {

    /**
     * Get events to retry.
     *
     * @param array $batch batch
     * @return void
     */
    protected function get_retry_events(array &$batch) {

        // Full batch.
        $batchsize = $this->config->db_batch_size - count($batch);
        if (!$batchsize) return;

        // Get logs.
        global $DB;
        $sql = "
            SELECT {logstore_standard_log}.*, error, attempts, newattempt, {logstore_trax_logs}.id AS xid
            FROM {logstore_trax_logs}
            JOIN {logstore_standard_log} ON {logstore_standard_log}.id = {logstore_trax_logs}.mid
            WHERE (error = 1 AND attempts < ?) 
            OR newattempt = 1
            ORDER BY mid
        ";
        $params = [$this->config->attempts];
        $logs = $DB->get_records_sql($sql, $params, 0, $batchsize);
        $batch = array_merge($batch, $logs);
    }

    /**
     * Get the new events to process.
     *
     * @param array $batch batch
     * @return void
     */
    protected function get_new_events(array &$batch) {

        // Full batch.
        $batchsize = $this->config->db_batch_size - count($batch);
        if (!$batchsize) return;

        // Get last processed log.
        $log = $this->get_last_processed_log();
        if ($log) {
            $where = "{logstore_standard_log}.id > ?";
            $params = [$log->id];
        } else {
            $where = "timecreated >= ?";
            $fromDate = $this->firslogs_to_time();
            $params = [$fromDate];
        }
        
        // Get logs.
        global $DB;
        $sql = "
            SELECT {logstore_standard_log}.*, error, attempts, newattempt, {logstore_trax_logs}.id AS xid
            FROM {logstore_standard_log}
            LEFT JOIN {logstore_trax_logs} ON {logstore_standard_log}.id = {logstore_trax_logs}.mid
            WHERE " . $where . "
            ORDER BY {logstore_standard_log}.id
        ";
        $logs = $DB->get_records_sql($sql, $params, 0, $batchsize);
        $batch = array_merge($batch, $logs);
    }

    /**
     * Get the past events to process.
     *
     * @param array $batch batch
     * @return void
     */
    protected function get_past_events(array &$batch) {

        // Full batch.
        $batchsize = $this->config->db_batch_size - count($batch);
        if (!$batchsize) return;

        // Get first processed log.
        $log = $this->get_first_processed_log();
        if (!$log) return;
        $fromDate = $this->firslogs_to_time();
        $params = [$fromDate, $log->id];

        // Get logs.
        global $DB;
        $sql = "
            SELECT {logstore_standard_log}.*, error, attempts, newattempt, {logstore_trax_logs}.id AS xid
            FROM {logstore_standard_log}
            LEFT JOIN {logstore_trax_logs} ON {logstore_standard_log}.id = {logstore_trax_logs}.mid
            WHERE timecreated >= ? 
            AND {logstore_standard_log}.id < ?
            ORDER BY {logstore_standard_log}.id DESC
        ";
        $logs = $DB->get_records_sql($sql, $params, 0, $batchsize);
        $batch = array_merge($batch, $logs);
    }

    /**
     * Get new selection to process.
     *
     * @param array $batch batch
     * @return void
     */
    protected function get_newly_selected_events(array &$batch) {

        // Full batch.
        $batchsize = $this->config->db_batch_size - count($batch);
        if (!$batchsize) return;

        // Get logs.
        global $DB;
        $selection = $this->sql_array(config::selected_events($this->config));
        $sql = "
            SELECT {logstore_standard_log}.*, error, attempts, newattempt, {logstore_trax_logs}.id AS xid
            FROM {logstore_trax_logs}
            JOIN {logstore_standard_log} ON {logstore_standard_log}.id = {logstore_trax_logs}.mid
            WHERE error = 4
            AND eventname IN " . $selection . "
            ORDER BY mid
        ";
        $params = [];
        $logs = $DB->get_records_sql($sql, $params, 0, $batchsize);
        $batch = array_merge($batch, $logs);
    }

    /**
     * Get the first processed log.
     *
     * @return stdClass|false
     */
    protected function get_first_processed_log() {
        global $DB;
        $sql = "
            SELECT {logstore_standard_log}.*, error, attempts, newattempt, {logstore_trax_logs}.id AS xid
            FROM {logstore_trax_logs}
            JOIN {logstore_standard_log} ON {logstore_standard_log}.id = {logstore_trax_logs}.mid
            ORDER BY mid
        ";
        $firsts = $DB->get_records_sql($sql, [], 0, 1);
        if (count($firsts) == 0) return false;
        return reset($firsts);
    }

    /**
     * Get the last processed log.
     *
     * @return stdClass|false
     */
    protected function get_last_processed_log() {
        global $DB;
        $sql = "
            SELECT {logstore_standard_log}.*, error, attempts, newattempt, {logstore_trax_logs}.id AS xid
            FROM {logstore_trax_logs}
            JOIN {logstore_standard_log} ON {logstore_standard_log}.id = {logstore_trax_logs}.mid
            ORDER BY mid DESC
        ";
        $lasts = $DB->get_records_sql($sql, [], 0, 1);
        if (count($lasts) == 0) return false;
        return reset($lasts);
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

    /**
     * Convert firslogs to time.
     *
     * @return string
     */
    protected function firslogs_to_time() {

        // Used with max precision when testing.
        $dateobj = \DateTime::createFromFormat('d/m/Y H:i:s', $this->config->firstlogs);
        if ($dateobj) {
            return strtotime($dateobj->format('d-m-Y H:i:s'));
        }

        // Used with daily precision in real life.
        $dateobj = \DateTime::createFromFormat('d/m/Y', $this->config->firstlogs);
        return strtotime($dateobj->format('d-m-Y'));
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

            // Update existing log.
            $DB->update_record('logstore_trax_logs', (object)[
                'id' => $event->xid,
                'mid' => $event->id,
                'error' => $error,
                'attempts' => $event->attempts + 1,
                'newattempt' => 0
            ]);

        } else if (isset($event->id) && $event->id) {

            // New log from a Moodle log store.

            // May already exist (already recorded for security reason).
            $log = $DB->get_record('logstore_trax_logs', ['mid' => $event->id]);
            if ($log) {
                $log->error = $error;
                $DB->update_record('logstore_trax_logs', $log);

            } else {

                // Recording for the first time.
                $DB->insert_record('logstore_trax_logs', (object)[
                    'mid' => $event->id,
                    'error' => $error,
                ]);
            }

        } else if (!isset($event->virtual)) {

            // Sync mode.
            $DB->insert_record('logstore_trax_logs', (object)[
                'error' => $error,
            ]);
        }
    }


}
