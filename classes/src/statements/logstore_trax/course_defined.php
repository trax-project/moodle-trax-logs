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
 * xAPI transformation of a TRAX event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements\logstore_trax;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\statements\base_statement;

/**
 * xAPI transformation of a TRAX event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_defined extends base_statement {

    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {
        $course = $this->activities->get('course', $this->eventother->id);

        // Add the course structure.
        $this->add_course_structure($course, $this->eventother); 
        if (empty($course['definition']['extensions']['http://vocab.xapi.fr/extensions/course-structure'])) {
            return false;
        }

        // Build statement only when the course structure is not empty.
        return array_replace($this->base('course'), [
            'actor' => $this->actors->get_system(), 
            'verb' => $this->verbs->get('defined'),
            'object' => $course,
        ]);
    }

    /**
     * Add course structure.
     *
     * @param array $xapi_course xAPI course
     * @param stdClass $course Course record
     * @return void
     */
    protected function add_course_structure(array &$xapi_course, \stdClass $course) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');
        $sections = [];
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();
        $numsections = course_get_format($course)->get_last_section_number();

        foreach ($modinfo->get_section_info_all() as $position => $sectioninfo) {

            // orphaned section.
            if ($position > $numsections) {
                break;
            }

            // Hidden section.
            if (!$sectioninfo->uservisible) {
                continue;
            }

            // Empty section.
            if (empty($modinfo->sections[$position])) {
                continue;
            }

            // Section activity.
            $xapi_section = $this->activities->get('course_section', $sectioninfo->id, true, 'course_section', 'course-section');
    
            // Children activities.
            $xapi_activities = array_filter(array_map(function ($cmid) use ($modinfo) {

                $cminfo = $modinfo->cms[$cmid];
                if (!$cminfo->visible) {
                    return false;
                }
                $child_activity = $this->activities->get($cminfo->modname, $cminfo->instance, true, 'module', $cminfo->modname, 'mod_' . $cminfo->modname);
                return ['activity' => $child_activity];

            }, $modinfo->sections[$position]));

            // Add section.
            $sections[] = [
                'activity' => $xapi_section,
                'children' => $xapi_activities,
            ];
        }
        $xapi_course['definition']['extensions']['http://vocab.xapi.fr/extensions/course-structure'] = $sections;
    }

}
