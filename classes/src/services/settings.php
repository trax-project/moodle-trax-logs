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
 * Logs service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\services;

defined('MOODLE_INTERNAL') || die();

/**
 * Logs service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings {

    /**
     * @var stdClass $config
     */
    protected $config;
    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->config = get_config('logstore_trax');
    }

    /**
     * Record a new setting.
     *
     * @param string $objecttable
     * @param integer $objectid
     * @param integer $target
     * @return void
     */
    public function add_setting($objecttable, $objectid, $target) {
        global $DB;
        $DB->insert_record('logstore_trax_settings', (object)[
            'objecttable' => $objecttable,
            'objectid' => $objectid,
            'target' => $target,
            'timecreated' => time(),
        ]);
    }

    /**
     * Get the last setting of a given context.
     *
     * @param string $objecttable
     * @param integer $objectid
     * @return object|false
     */
    public function get_last_setting($objecttable, $objectid) {
        global $DB;
        $settings = $DB->get_records('logstore_trax_settings', [
            'objecttable' => $objecttable,
            'objectid' => $objectid,
        ], 'timecreated');
        return end($settings);
    }

    /**
     * Get the valid setting of a given context at a given time.
     *
     * @param string $objecttable
     * @param integer $objectid
     * @return object|false
     */
    public function get_setting_at($objecttable, $objectid, $time) {
        global $DB;
        $settings = $DB->get_records('logstore_trax_settings', [
            'objecttable' => $objecttable,
            'objectid' => $objectid,
        ], 'timecreated');

        $validSetting = false;
        foreach ($settings as $setting) {
            if ($time > $setting->timecreated) {
                $validSetting = $setting;
            } else {
                return $validSetting;
            }
        }
        return $validSetting;
    }
}
