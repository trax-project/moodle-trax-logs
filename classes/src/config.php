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
 * Some util functions.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src;

defined('MOODLE_INTERNAL') || die();

/**
 * Some util functions.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config {

    /**
     * Asynchronous mode.
     */
    const SYNCHRO_SYNC = 0;

    /**
     * Synchronous mode.
     */
    const SYNCHRO_ASYNC = 1;

    
    /**
     * Get the synchro options.
     *
     * @return array
     */
    public static function synchro_options() {
        return [
            self::SYNCHRO_SYNC => get_string('sync', 'logstore_trax'),
            self::SYNCHRO_ASYNC => get_string('async', 'logstore_trax'),
        ];
    }

    /**
     * Get the loggable components.
     *
     * @return array
     */
    public static function loggable_components() {
        return [
            'mod_assign' => get_string('modulename', 'assign'),
            'mod_book' => get_string('modulename', 'book'),
            'mod_chat' => get_string('modulename', 'chat'),
            'mod_choice' => get_string('modulename', 'choice'),
            'mod_feedback' => get_string('modulename', 'feedback'),
            'mod_forum' => get_string('modulename', 'forum'),
            'mod_lesson' => get_string('modulename', 'lesson'),
            'mod_quiz' => get_string('modulename', 'quiz'),
            'mod_scorm' => get_string('modulename', 'scorm'),
            'mod_survey' => get_string('modulename', 'survey'),
            'mod_wiki' => get_string('modulename', 'wiki'),
            'mod_workshop' => get_string('modulename', 'workshop'),
            'mod_hvp' => get_string('hvp', 'logstore_trax'),
            'others' => get_string('additional_components', 'logstore_trax'),
        ];
    }

    /**
     * Get the logged components by default.
     *
     * @return array
     */
    public static function logged_components() {
        return array_map(function($component) {
            return 1;
        }, self::loggable_components());
    }

    /**
     * Get the loggable core events.
     *
     * @return array
     */
    public static function loggable_core_events() {
        return [
            'authentication' => get_string('authentication', 'logstore_trax'),
            'navigation' => get_string('navigation', 'logstore_trax'),
            'completion' => get_string('completion', 'logstore_trax'),
            'grading' => get_string('grading', 'logstore_trax'),
        ];
    }

    /**
     * Get the logged core events by default.
     *
     * @return array
     */
    public static function logged_core_events() {
        return array_map(function($component) {
            return 1;
        }, self::loggable_core_events());
    }


}
