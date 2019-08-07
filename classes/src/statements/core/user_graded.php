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
 * xAPI transformation of a Moodle event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements\core;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/gradelib.php');

use logstore_trax\src\statements\base_statement;
use logstore_trax\src\utils\module_context;

/**
 * xAPI transformation of a Moodle event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_graded extends base_statement {

    use module_context;

    /**
     * Plugin.
     *
     * @var string $plugin
     */
    protected $plugin;

    /**
     * Activity type.
     *
     * @var string $activitytype
     */
    protected $activitytype;


    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {

        // Get data.
        list($grade, $gradeitem, $object) = $this->get_grade_data();
        if (!$grade) return false;

        // Init.
        $this->init($object);

        // Build the statement.
        return array_replace($this->base($gradeitem->itemmodule, true, $this->activitytype, $this->plugin), [
            'actor' => $this->actors->get('user', $this->event->userid),
            'verb' => $this->verbs->get('graded'),
            'object' => $this->activities->get($gradeitem->itemmodule, $gradeitem->iteminstance, true, 'module', $this->activitytype, $this->plugin),
            'result' => $this->get_result($grade, $gradeitem)
        ]);
    }

    /**
     * Init.
     *
     * @param \stdClass $object object
     * @return void
     */
    protected function init(\stdClass $object) {
    }

    /**
     * Get grade data.
     *
     * @return array
     */
    protected function get_grade_data() {
        global $DB;

        // Get the grade item.
        $gradeitem = $DB->get_record('grade_items', ['id' => $this->eventother->itemid], '*', MUST_EXIST);

        // Check that it is an activity grade.
        if ($gradeitem->itemtype != 'mod') {
            return [false, false, false];
        }

        // Check that it is a value or scale grade.
        if (!in_array($gradeitem->gradetype, [GRADE_TYPE_SCALE, GRADE_TYPE_VALUE])) {
            return [false, false, false];
        }

        // Get grade.
        $grade = $DB->get_record('grade_grades_history', [
            'itemid' => $this->eventother->itemid,
            'userid' => $this->event->userid,
            'timemodified' => $this->event->timecreated,
            'source' => 'mod/' . $gradeitem->itemmodule,
        ], '*', MUST_EXIST);

        // Check that there is a raw grade.
        if (!isset($grade->rawgrade)) {
            return [false, false, false];
        }

        // Get the object.
        $object = $DB->get_record($gradeitem->itemmodule, ['id' => $gradeitem->iteminstance], '*', MUST_EXIST);

        return [$grade, $gradeitem, $object];
    }

    /**
     * Get verb and result.
     *
     * @param \stdClass $grade Grade
     * @param \stdClass $gradeitem Grade item
     * @return array
     */
    protected function get_result(\stdClass $grade, \stdClass $gradeitem) {

        // Define scoring values.
        $raw = floatval($grade->rawgrade);
        $min = floatval($gradeitem->grademin);
        $max = floatval($gradeitem->grademax);

        // Define the success.
        $passed = null;
        if (isset($gradeitem->gradepass) && floatval($gradeitem->gradepass) > 0) {
            $passed = $raw >= floatval($gradeitem->gradepass);
        }

        // Define the result.
        $scaled = ($raw - $min) / ($max - $min);
        $result = [
            'score' => [
                'raw' => round($raw, 2),
                'min' => round($min, 2),
                'max' => round($max, 2),
                'scaled' => round($scaled, 2)
            ]
        ];
        if (!is_null($passed)) {
            $result['success'] = $passed;
        }

        return $result;
    }


}
