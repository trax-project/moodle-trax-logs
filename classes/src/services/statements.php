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
 * Statements service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\services;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\config;

/**
 * Statements service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class statements {

    /**
     * Actors service.
     *
     * @var actors $actors
     */
    protected $actors;

    /**
     * Verbs service.
     *
     * @var verbs $actors
     */
    protected $verbs;

    /**
     * Activities service.
     *
     * @var activities $activities
     */
    protected $activities;

    /**
     * Logs.
     *
     * @var logs $logs
     */
    protected $logs;


    /**
     * Constructs a new statement map.
     *
     * @param actors $actors Actors service
     * @param verbs $verbs Verbs service
     * @param activities $activities Activities service
     * @param logs $logs logs service
     */
    public function __construct(actors $actors, verbs $verbs, activities $activities, logs $logs) {
        $this->actors = $actors;
        $this->verbs = $verbs;
        $this->activities = $activities;
        $this->logs = $logs;
    }

    /**
     * Get an array of Statement + Event given an array of events.
     *
     * @param array $events Moodle events data
     * @return array
     */
    public function get_from_events(array $events) {
        return array_values(array_filter(array_map(function ($event) {
            if (is_array($event)) $event = (object)$event;
            return $this->get_from_event($event);
        }, $events)));
    }

    /**
     * Get a Statement + Event given an event.
     *
     * @param \stdClass $event Moodle event data
     * @return mixed
     */
    public function get_from_event(\stdClass $event) {

        $class = null;
        $parts = explode('\\', $event->eventname);
        $plugin = $parts[1];
        $name = end($parts);

        // Special case for LRS proxy.
        if ($plugin == 'logstore_trax' && $name == 'proxy_statements_post') {

            // Check if the statement must be resent.
            $dateobj = \DateTime::createFromFormat('d/m/Y', get_config('logstore_trax', 'resend_livelogs_until'));
            $resenduntil = strtotime($dateobj->format('d-m-Y'));

            // Don't send the statement, just log it.
            if ($event->timecreated > $resenduntil + (60 * 60 * 24)) {
                if (!$eventother = json_decode($event->other)) {
                    $eventother = (object)unserialize($event->other);
                }
                if ($eventother->error) {
                    $this->logs->log_lrs_error($event);
                } else {
                    $this->logs->log_success($event);
                }
                return;
            }
    
            // Resend the statement.
            $class = '\\logstore_trax\\src\\statements\\proxy\\statements_post';
        }

        // Check if this event is selected.
        if (!$class || !class_exists($class)) {
            $selectedEvents = config::selected_events(get_config('logstore_trax'));
            $knownEvents = config::known_events();
            $otherEvents = config::other_events(get_config('logstore_trax'));
            $otherEvent = $otherEvents && $event->contextlevel == CONTEXT_MODULE && !in_array($event->eventname, $knownEvents);

            $continue = in_array($event->eventname, $selectedEvents) || $otherEvent;
            if (!$continue) {
                $this->logs->log_unselected($event);
                return;
            }

            // Next, search in the plugin folder.
            $class = '\\'.$plugin.'\\xapi\\statements\\'.$name;
        }

        // Then, search in Trax Logs, plugin subfolder.
        if (!class_exists($class) && $plugin != 'core') {
            $class = '\\logstore_trax\\src\\statements\\'.$plugin.'\\'.$name;
        }

        // Search core events statement class.
        if (!class_exists($class) && $plugin == 'core') {
            $method = 'get_' . $name . '_statement_class';
            if (method_exists($this, $method)) {
                $class = $this->$method($event);
            }
        }

        // Finally, search in Trax Logs, core subfolder.
        if (!$class || !class_exists($class)) {
            $class = '\\logstore_trax\\src\\statements\\core\\'.$name;
        }

        // No class, log as unsupported.
        if (!class_exists($class)) {
            $this->logs->log_unsupported($event);
            return;
        }

        try {

            // Get the statement and return the result object.
            $statement = (new $class($event, $this->actors, $this->verbs, $this->activities))->get();
            if (!$statement) {

                // No error, but refused to process the event. Log as unsupported.
                $this->logs->log_unsupported($event);
                return;

            } elseif ($statement == -1) {

                // This event should not be logged.
                return;
            }

            // We got it, but we want to be sure to have a log for this event,
            // so we log an internal error by default.
            $this->logs->log_internal_error($event);

            // Always return an array of statements.
            $statements = isset($statement['actor']) ? [$statement] : $statement;

            // Apply custom changes.
            $statements = $this->customize_statements($statements, $event);

            return (object)['statements' => $statements, 'event' => $event];

        } catch (\moodle_exception $e) {

            // Log the error.
            $this->logs->log_internal_error($event);
        }
    }

    /**
     * Customize statements with the TRAX local plugin when it exists.
     *
     * @param array $statements
     * @param stdClass $event Moodle event data
     * @return array $statements
     */
    protected function customize_statements(array $statements, $event) {
        global $CFG;
        $file = $CFG->dirroot.'/local/trax/lib.php';
        if (file_exists($file)) {
            $function = 'local_trax_customize_statement';
            require_once($file);
            if (function_exists($function)) {
                return array_map(function ($statement) use ($function, $event) {
                    return $function($statement, $event);
                }, $statements);
            }
        }
        return $statements;
    }

    /**
     * Get user_graded Statement class.
     *
     * @param \stdClass $event Moodle event data
     * @return string
     */
    protected function get_user_graded_statement_class(\stdClass $event) {
        global $DB;
        $sql = "
            SELECT {grade_items}.itemtype, {grade_items}.itemmodule
            FROM {grade_grades}
            INNER JOIN {grade_items} ON {grade_items}.id = {grade_grades}.itemid
            WHERE {grade_grades}.id = ?
        ";
        $params = [$event->objectid];
        $record = $DB->get_record_sql($sql, $params);
        if (!$record) {
            return false;
        }
        $plugin = $record->itemtype . '_' . $record->itemmodule;

        // First, search in the plugin folder.
        $class = '\\' . $plugin . '\\xapi\\statements\\user_graded';

        // Then, search in Trax Logs, plugin subfolder.
        if (!class_exists($class)) {
            $class = '\\logstore_trax\\src\\statements\\' . $plugin . '\\user_graded';
        }
        return $class;
    }

    /**
     * Get course_module_completion_updated Statement class.
     *
     * @param \stdClass $event Moodle event data
     * @return string
     */
    protected function get_course_module_completion_updated_statement_class(\stdClass $event) {
        global $DB;
        $sql = "
            SELECT {modules}.name
            FROM {course_modules_completion}
            INNER JOIN {course_modules} ON {course_modules}.id = {course_modules_completion}.coursemoduleid
            INNER JOIN {modules} ON {modules}.id = {course_modules}.module
            WHERE {course_modules_completion}.id = ?
        ";
        $params = [$event->objectid];
        $record = $DB->get_record_sql($sql, $params);
        if (!$record) {
            return false;
        }
        $plugin = 'mod_' . $record->name;

        // First, search in the plugin folder.
        $class = '\\' . $plugin . '\\xapi\\statements\\course_module_completion_updated';

        // Then, search in Trax Logs, plugin subfolder.
        if (!class_exists($class)) {
            $class = '\\logstore_trax\\src\\statements\\' . $plugin . '\\course_module_completion_updated';
        }
        return $class;

    }
}
