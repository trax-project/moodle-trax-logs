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
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {
        
        // Get grade.
        global $DB;
        $grade = $DB->get_record('grade_grades', ['id' => $this->event->objectid], '*', MUST_EXIST);
        $gradeitem = $DB->get_record('grade_items', ['id' => $this->eventother->itemid], '*', MUST_EXIST);

        // Check that it is an activity grade.
        if ($gradeitem->itemtype != 'mod') {
            return false;
        }

        // Check that it is a value or scale grade.
        if (!in_array($gradeitem->gradetype, [GRADE_TYPE_SCALE, GRADE_TYPE_VALUE])) {
            return false;
        }

        // Check that there is a raw grade.
        if (!isset($grade->rawgrade)) {
            return false;
        }

        // Define scoring values.
        $raw = floatval($grade->rawgrade);
        $min = floatval($gradeitem->grademin);
        $max = floatval($gradeitem->grademax);

        // Define the verb.
        $passed = null;
        if (isset($gradeitem->gradepass) && floatval($gradeitem->gradepass) > 0) {
            if ($raw >= floatval($gradeitem->gradepass)) {
                $verb = $this->verbs->get('passed');
                $passed = true;
            } else {
                $verb = $this->verbs->get('failed');
                $passed = false;
            }
        } else {
            $verb = $this->verbs->get('scored');
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

        // Build the statement.
        return array_replace($this->base($gradeitem->itemmodule), [
            'actor' => $this->actors->get('user', $this->event->userid),
            'verb' => $verb,
            'object' => $this->activities->get($gradeitem->itemmodule, $gradeitem->iteminstance, true, 'module'),
            'result' => $result
        ]);
    }

}
