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
 * Event provider abstract class.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\virtual_events;

defined('MOODLE_INTERNAL') || die();

/**
 * Event provider abstract class.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class event_provider {

    /**
     * Get events to process.
     *
     * @return array
     */
    public abstract function get_events();

    /**
     * Get events given an array of records.
     *
     * @param string $eventname
     * @param array $records
     * @return array
     */
    protected function events(string $eventname, array $records) {
        return array_map(function ($record) use ($eventname) {
            return $this->event($eventname, $record);
        }, $records);
    }

    /**
     * Built an event.
     *
     * @param string $eventname
     * @param stdClass $record
     * @return array
     */
    protected function event(string $eventname, \stdClass $record) {
        return [
            'eventname' => "\\logstore_trax\\event\\$eventname",
            'contextlevel' => CONTEXT_SYSTEM,
            'virtual' => true,
            'other' => json_encode($record),
            'timecreated' => time(),
            'userid' => 0,
        ];
    }

}
