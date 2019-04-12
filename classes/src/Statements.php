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
 * Trax Logs for Moodle.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax;

defined('MOODLE_INTERNAL') || die();

class Statements {

    /**
     * Actors index.
     * 
     * @var Actors $actors
     */
    protected $actors;

    /**
     * Verbs index.
     * 
     * @var Verbs $actors
     */
    protected $verbs;

    /**
     * Activities index.
     * 
     * @var Activities $activities
     */
    protected $activities;


    /**
     * Constructs a new statement map.
     *
     * @param stdClass $config xAPI configuration
     */
    public function __construct($actors, $verbs, $activities) {
        $this->actors = $actors;
        $this->verbs = $verbs;
        $this->activities = $activities;
    }

    /**
     * Get an array of Statements given an array of events.
     *
     * @param array $events Moodle events data
     * @return array
     */
    public function getFromEvents(array $events)
    {
        return array_filter(array_map(function ($event) {
            return $this->getFromEvent($event);
        }, $events));
    }

    /**
     * Get a Statement given an event.
     *
     * @param array $event Moodle event data
     * @return array|null
     */
    public function getFromEvent(array $event) {
        $event = (object)$event;
        $parts = explode('\\', $event->eventname);
        $plugin = $parts[1];
        $name = end($parts);
        $name = str_replace('_', '', ucwords($name, '_'));

        // First, search in the plugin folder
        $class = '\\'.$plugin.'\\xapi\\statements\\'.$name;
        
        // Then, search in Trax Logs, plugin subfolder
        if (!class_exists($class))
            $class = '\\logstore_trax\\statements\\'.$plugin.'\\'.$name;
        
        // Finally, search in Trax Logs, core subfolder
        if (!class_exists($class))
            $class = '\\logstore_trax\\statements\\core\\'.$name;
        
        if (!class_exists($class)) return;
        return (new $class($event, $this->actors, $this->verbs, $this->activities))->get();
    }


}
