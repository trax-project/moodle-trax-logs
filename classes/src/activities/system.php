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
 * xAPI transformation of the system level activity.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\activities;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\utils;

/**
 * xAPI transformation of the system level activity.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system extends activity
{

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
        $activity = $this->base_activity($type, $uuid, $full, $vocabtype, $plugin);
        if ($full) {

            // Name & description.
            global $DB;
            $course = $DB->get_record('course', array('id' => 1), '*', MUST_EXIST);
            $activity['definition']['name'] = utils::lang_string($course->fullname, $course);
            if (!empty($course->summary)) {
                $activity['definition']['description'] = utils::lang_string($course->summary, $course);
            }
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
        return $this->config->platform_iri;
    }


}
