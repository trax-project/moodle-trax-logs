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
 * Proxy video profile.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\proxy;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\proxy\profile;

/**
 * Proxy video profile.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class video extends profile {

    /**
     * Transform a statement (hook).
     *
     * @param \stdClass $statement Statement to transform
     * @return void
     */
    protected function transform(&$statement) {

        // Remove object description.
        if (isset($statement->object->definition->description)) {
            unset($statement->object->definition->description);
        }

        // Profile context activity.
        foreach ($statement->context->contextActivities->category as &$category) {
            if ($category->id == 'https://w3id.org/xapi/video') {
                $category->objectType = 'Activity';
                $category->definition = [
                    'type' => 'http://adlnet.gov/expapi/activities/profile'
                ];
            }
        }
    }

}
