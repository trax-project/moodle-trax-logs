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
 * Trait to build an inside module context.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait to build an inside module context.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait inside_module_context {

    /**
     * Build the module context.
     *
     * @param string $activitytype Type of activity
     * @param bool $withsystem Include the system activity in the context?
     * @return array
     */
    protected function base_context($activitytype, $withsystem = true) {
        $context = parent::base_context($activitytype, $withsystem);

        // Add module in parent context.
        $module = $this->activities->get($activitytype, $this->event->objectid, false, 'module');
        $context['contextActivities']['parent'] = array($module);

        // Add course in grouping context.
        $course = $this->activities->get('course', $this->event->courseid, false);
        $context['contextActivities']['grouping'][] = $course;

        // Change granularity level to "inside-learning-unit"
        foreach ($context['contextActivities']['category'] as &$category) {
            if ($category['definition']['type'] == 'http://vocab.xapi.fr/activities/granularity-level') {
                $category['id'] = 'http://vocab.xapi.fr/categories/inside-learning-unit';
                break;
            }
        }
        return $context;
    }

}
