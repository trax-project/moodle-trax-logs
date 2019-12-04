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
 * Activities service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\services;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\vocab\activity_types;

/**
 * Activities service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activities extends index {

    /**
     * Vocab: types of activity.
     *
     * @var activity_types $types
     */
    public $types;

    /**
     * DB table.
     *
     * @var string $table
     */
    protected $table = 'logstore_trax_activities';


    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->types = new activity_types();
    }

    /**
     * Get an activity, given a Moodle ID and an activity type.
     *
     * @param string $type Type of activity
     * @param int $mid Moodle ID of the activity
     * @param bool $full Give the full definition of the activity?
     * @param string $model Model to be used, when there is no class mathcing with the type (ex. module)
     * @param string $vocabtype Type to be used in vocab index if it defers from the main type
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @param stdClass $entry DB entry
     * @return array
     */
    public function get(string $type, int $mid = 0, bool $full = true,
                        string $model = 'activity', string $vocabtype = null, string $plugin = null, $entry = null) {

        // Get the DB entry.
        if (!isset($entry)) {
            $entry = $this->get_or_create_db_entry($mid, $type);
        }

        // Set vocab type.
        if (!isset($vocabtype)) {
            $vocabtype = $type;
        }

        // Check if it is a known module.
        if ($this->types->level($vocabtype, $plugin) == 'http://vocab.xapi.fr/categories/learning-unit') {
            $model = 'module';
        }

        // Search in the plugin folder.
        if (isset($plugin)) {
            $class = '\\'.$plugin.'\\xapi\\activities\\'.$model;
        }

        // Search in Trax Logs, based on the $type.
        if (!isset($plugin) || !class_exists($class)) {
            $class = '\\logstore_trax\\src\\activities\\'.$type;
        }

        // Finally, search in Trax Logs, based on $model.
        if (!class_exists($class)) {
            $class = '\\logstore_trax\\src\\activities\\'.$model;
        }

        $config = get_config('logstore_trax');
        $config->platform_iri = $this->platform_iri();
        return (new $class($config))->get($type, $mid, $entry->uuid, $full, $vocabtype, $plugin);
    }

    /**
     * Get an existing activity, given a Moodle ID and an activity type.
     *
     * @param string $type Type of activity
     * @param int $mid Moodle ID of the activity
     * @param bool $full Give the full definition of the activity?
     * @param string $model Model to be used, when there is no class mathcing with the type (ex. module)
     * @param string $vocabtype Type to be used in vocab index if it defers from the main type
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    public function get_existing(
        string $type,
        int $mid = 0,
        bool $full = true,
        string $model = 'activity',
        string $vocabtype = null,
        string $plugin = null
    ) {
        $entry = $this->get_db_entry_or_fail($mid, $type);
        return $this->get($type, $mid, $full, $model, $vocabtype, $plugin, $entry);
    }

    /**
     * Get an existing activity, given an UUID.
     *
     * @param string $uuid UUID of actor
     * @param bool $full Give the full definition of the activity?
     * @param string $model Model to be used, when there is no class mathcing with the type (ex. module)
     * @param string $vocabtype Type to be used in vocab index if it defers from the main type
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    public function get_existing_by_uuid(
        string $uuid,
        bool $full = true,
        string $model = 'activity',
        string $vocabtype = null,
        string $plugin = null
    ) {
        $entry = $this->get_db_entry_by_uuid_or_fail($uuid);
        return $this->get($entry->type, $entry->mid, $full, $model, $vocabtype, $plugin, $entry);
    }

    /**
     * Get category context activities, given an activity vocab type.
     *
     * @param string $vocabtype Type of activity
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    public function get_categories(string $vocabtype, string $plugin = null) {

        // No category.
        $res = [];
        if (!$level = $this->types->level($vocabtype, $plugin)) {
            return [];
        }

        // Level category.
        return [[
            'id' => $level,
            'definition' => ['type' => 'http://vocab.xapi.fr/activities/granularity-level'],
        ]];
    }

}
