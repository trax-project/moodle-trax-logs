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
 * xAPI transformation of a base activity.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\activities;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\vocab\activity_types;

/**
 * xAPI transformation of a base activity.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity {

    /**
     * Vocab: types of activity.
     *
     * @var activity_types $types
     */
    protected $types;

    /**
     * Config.
     *
     * @var stdClass $config
     */
    protected $config;


    /**
     * Constructor.
     *
     * @param stdClass $config Config
     * @return void
     */
    public function __construct($config) {
        $this->config = $config;
        $this->types = new activity_types();
    }

    /**
     * Get an activity, given an activity type and an UUID.
     *
     * @param string $type Type of activity
     * @param int $mid Moodle ID of the activity
     * @param string $uuid UUID of the activity
     * @param bool $full Give the full definition of the activity?
     * @param string $vocabtype Type to be used in vocab index
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    public function get(string $type, int $mid, string $uuid, bool $full, string $vocabtype, string $plugin = null) {
        return $this->base_activity($type, $uuid, $full, $vocabtype, $plugin);
    }

    /**
     * Get base activity.
     *
     * @param string $type Type of activity
     * @param string $uuid UUID of the activity
     * @param bool $full Give the full definition of the activity?
     * @param string $vocabtype Type to be used in vocab index
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    protected function base_activity(string $type, string $uuid, bool $full, string $vocabtype, string $plugin = null) {
        $activity = [
            'id' => $this->base_activity_id($type, $uuid),
            'definition' => [],
        ];
        if ($type = $this->types->type($vocabtype, $plugin)) {
            $activity['definition']['type'] = $type;
        }
        if ($full) {
            $activity['objectType'] = 'Activity';
        }
        return $activity;
    }

    /**
     * Get base activity ID.
     *
     * @param string $type Type of activity
     * @param string $uuid UUID of the activity
     * @return string
     */
    protected function base_activity_id(string $type, string $uuid) {
        if (!empty($uuid)) {
            $uuid = '/' . $uuid;
        }
        return $this->config->platform_iri . '/xapi/activities/' . $type . $uuid;
    }


}
