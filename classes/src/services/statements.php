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
        return array_filter(array_map(function ($event) {
            if (is_array($event)) $event = (object)$event;
            return $this->get_from_event($event);
        }, $events));
    }

    /**
     * Get a Statement + Event given an event.
     *
     * @param \stdClass $event Moodle event data
     * @return mixed
     */
    public function get_from_event(\stdClass $event) {
        $parts = explode('\\', $event->eventname);
        $plugin = $parts[1];
        $name = end($parts);

        // First, search in the plugin folder.
        $class = '\\'.$plugin.'\\xapi\\statements\\'.$name;

        // Then, search in Trax Logs, plugin subfolder.
        if (!class_exists($class)) {
            $class = '\\logstore_trax\\src\\statements\\'.$plugin.'\\'.$name;
        }

        // Finally, search in Trax Logs, core subfolder.
        if (!class_exists($class)) {
            $class = '\\logstore_trax\\src\\statements\\core\\'.$name;
        }

        // No class, do nothing.
        if (!class_exists($class)) {
            return;
        }

        try {

            // Get the statement and return the result object.
            $statement = (new $class($event, $this->actors, $this->verbs, $this->activities))->get();
            return (object)['statement' => $statement, 'event' => $event];

        } catch (\moodle_exception $e) {

            // Log the error.
            $this->logs->log_internal_error($event);
        }
    }


}
