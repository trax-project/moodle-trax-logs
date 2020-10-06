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

    use module_role;

    /**
     * Build the context.
     *
     * @param string $activitytype Type of activity
     * @param bool $withsystem Include the system activity in the context?
     * @param string $vocabtype Type of activity
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    protected function base_context($activitytype, $withsystem, $vocabtype, $plugin = null) {
        $context = parent::base_context($activitytype, $withsystem, $vocabtype, $plugin);

        // Add module in parent context.
        $module = $this->activities->get($activitytype, $this->event->objectid, false, 'module', $vocabtype, $plugin);
        $context['contextActivities']['parent'] = array($module);

        // Add course in grouping context.
        $course = $this->activities->get('course', $this->event->courseid, false);
        $context['contextActivities']['grouping'][] = $course;

        // Change granularity level to "inside-learning-unit".       
        foreach ($context['contextActivities']['category'] as &$category) {
            if ($category['definition']['type'] == 'http://vocab.xapi.fr/activities/granularity-level') {
                $category['id'] = 'http://vocab.xapi.fr/categories/inside-learning-unit';
                break;
            }
        }
        // Moodle module profile.
        $context['contextActivities']['category'][] = [
            'id' => 'http://vocab.xapi.fr/categories/moodle/' . $activitytype,
            'definition' => ['type' => 'http://adlnet.gov/expapi/activities/profile'],
        ];

        // Define the user role.
        if ($role = $this->module_role()) {
            if (!isset($context['extensions'])) {
                $context['extensions'] = [];
            }
            $context['extensions']['http://vocab.xapi.fr/extensions/user-role'] = $role;
        }

        return $context;
    }

}
