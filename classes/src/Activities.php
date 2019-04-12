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

class Activities extends Index {

    use ActivityTypes;

    /**
     * DB table.
     * 
     * @var string $table
     */
    protected $table = 'logstore_trax_activities';


    /**
     * Check if an activity type is supported.
     * 
     * @param string $type Type of activity
     * @return bool
     */
    public function supported(string $type)
    {
        return isset($this->types->$type);
    }

    /**
     * Get an activity, given a Moodle ID and an activity type.
     * 
     * @param string $type Type of activity
     * @param int $mid Moodle ID of the activity
     * @param bool $full Give the full definition of the activity?
     * @param string $model Model to be used, when there is no class mathcing with the type (ex. module)
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @param stdClass $entry DB entry
     * @return array
     */
    public function get(string $type, int $mid = 0, bool $full = true, string $model = 'activity', string $plugin = null, $entry = null) {
        if (!isset($entry)) $entry = $this->getOrCreateDbEntry($mid, $type);

        // Check if it is a known module
        if (isset($this->types->$type) && isset($this->types->$type->level) && $this->types->$type->level == 'http://vocab.xapi.fr/categories/learning-unit') {
            $model = 'module';
        }

        // Search in the plugin folder
        if (isset($plugin))
            $class = '\\'.$plugin.'\\xapi\\activities\\'.ucfirst($model);

        // Search in Trax Logs, based on the $type
        if (!isset($plugin) || !class_exists($class))
            $class = '\\logstore_trax\\activities\\'.str_replace('_', '', ucwords($type, '_'));
        
        // Finally, search in Trax Logs, based on $model
        if (!class_exists($class))
            $class = '\\logstore_trax\\activities\\'.ucfirst($model);
                    
        return (new $class($this->config, $this->types))->get($type, $mid, $entry->uuid, $full);
    }

    /**
     * Get an existing activity, given a Moodle ID and an activity type.
     * 
     * @param string $type Type of activity
     * @param int $mid Moodle ID of the activity
     * @param bool $full Give the full definition of the activity?
     * @param string $model Model to be used, when there is no class mathcing with the type (ex. module)
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    public function getExisting(string $type, int $mid = 0, bool $full = true, string $model = 'activity', string $plugin = null)
    {
        $entry = $this->getDbEntryOrFail($mid, $type);
        return $this->get($type, $mid, $full, $model, $plugin, $entry);
    }

    /**
     * Get category context activities, given an activity type.
     * 
     * @param string $type Type of activity
     * @return array
     */
    public function getCategories(string $type) {
        $res = [];
        if (!isset($this->types->$type)) return $res;

        // Level
        if (isset($this->types->$type->level)) {
            $res[] = [
                'id' => $this->types->$type->level,
                'definition' => ['type' => 'http://vocab.xapi.fr/activities/granularity-level'],
            ];
        }
        return $res;
    }

}
