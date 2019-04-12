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

namespace logstore_trax\src\activities;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\vocab\activity_types;

class activity {

    use activity_types;

    /**
     * Config.
     * 
     * @var stdClass $config
     */
    protected $config;


    /**
     * Constructs a new statement.
     *
     * @param stdClass $config Config
     * @param stdClass $types Activity types
     */
    public function __construct($config) {
        $this->config = $config;
        $this->types = json_decode(json_encode($this->types));
    }

    /**
     * Get an activity, given an activity type and an UUID.
     * 
     * @param string $type Type of activity
     * @param int $mid Moodle ID of the activity
     * @param string $uuid UUID of the activity
     * @param bool $full Give the full definition of the activity?
     * @return array
     */
    public function get(string $type, int $mid = 0, string $uuid, bool $full = true) {
        return $this->baseActivity($type, $uuid);
    }

    /**
     * Get base activity.
     * 
     * @param string $type Type of activity
     * @param string $uuid UUID of the activity
     * @return array
     */
    protected function baseActivity(string $type, string $uuid = '')
    {
        return [
            'objectType' => 'Activity',
            'id' => $this->baseActivityId($type, $uuid),
            'definition' => [
                'type' => $this->types->$type->type,
            ],
        ];
    }

    /**
     * Get base activity ID.
     * 
     * @param string $uuid UUID of the activity
     * @return string
     */
    protected function baseActivityId(string $type, string $uuid = '')
    {
        if (!empty($uuid)) $uuid = '/' . $uuid;
        return $this->config->platform_iri . '/xapi/activities/' . $type . $uuid;
    }


}
