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
 * Config functions.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src;

defined('MOODLE_INTERNAL') || die();

/**
 * Config functions.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config extends events {

    
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
     * Get the core events selected by default.
     *
     * @return array
     */
    public static function default_core_events() {
        return array_map(function($component) {
            return 1;
        }, self::loggable_core_events());
    }

    /**
     * Get the core selected events.
     *
     * @param stdClass $config Config
     * @return array
     */
    public static function selected_core_events(\stdClass $config) {
        $families = array_keys(array_filter($config->core_events));
        $families = array_intersect_key($this->core, array_flip($families));
        return call_user_func_array("array_merge", $families);
    }

    /**
     * Get the loggable Moodle components.
     *
     * @return array
     */
    public static function loggable_moodle_components() {
        return [
            'mod_assign' => get_string('modulename', 'assign'),
            'mod_book' => get_string('modulename', 'book'),
            'mod_chat' => get_string('modulename', 'chat'),
            'mod_choice' => get_string('modulename', 'choice'),
            'mod_data' => get_string('modulename', 'data'),
            'mod_feedback' => get_string('modulename', 'feedback'),
            'mod_folder' => get_string('modulename', 'folder'),
            'mod_forum' => get_string('modulename', 'forum'),
            'mod_glossary' => get_string('modulename', 'glossary'),
            'mod_imscp' => get_string('modulename', 'imscp'),
            'mod_lesson' => get_string('modulename', 'lesson'),
            'mod_lti' => get_string('modulename', 'lti'),
            'mod_page' => get_string('modulename', 'page'),
            'mod_quiz' => get_string('modulename', 'quiz'),
            'mod_resource' => get_string('modulename', 'resource'),
            'mod_scorm' => get_string('modulename', 'scorm'),
            'mod_survey' => get_string('modulename', 'survey'),
            'mod_url' => get_string('modulename', 'url'),
            'mod_wiki' => get_string('modulename', 'wiki'),
            'mod_workshop' => get_string('modulename', 'workshop'),
        ];
    }

    /**
     * Get the Moodle components selected by default.
     *
     * @return array
     */
    public static function default_moodle_components() {
        return array_map(function($component) {
            return 1;
        }, self::loggable_moodle_components());
    }

    /**
     * Get the selected Moodle components.
     *
     * @param stdClass $config Config
     * @return array
     */
    public static function selected_moodle_components(\stdClass $config) {
        return array_keys(array_filter($config->moodle_components));
    }

    /**
     * Get the loggable additional components.
     *
     * @return array
     */
    public static function loggable_additional_components() {
        return [
            'mod_hvp' => get_string('hvp', 'logstore_trax'),
            'other' => get_string('other_components', 'logstore_trax'),
        ];
    }

    /**
     * Get the additional components selected by default.
     *
     * @return array
     */
    public static function default_additional_components() {
        return array_map(function($component) {
            return 1;
        }, self::loggable_additional_components());
    }

    /**
     * Get the additional selected events.
     *
     * @param stdClass $config Config
     * @return array
     */
    public static function selected_additional_events(\stdClass $config) {
        $components = array_filter($config->additional_components);
        unset($components['other']);
        $components = array_keys($components);
        $components = array_intersect_key($this->additional, array_flip($components));
        return call_user_func_array("array_merge", $components);
    }

    /**
     * Return true when the "Other components" checkbox is selected.
     *
     * @param stdClass $config Config
     * @return bool
     */
    public static function other_components_selected(\stdClass $config) {
        $additional = $config->additional_components;
        return isset($additional['other']) && $additional['other'];
    }


}
