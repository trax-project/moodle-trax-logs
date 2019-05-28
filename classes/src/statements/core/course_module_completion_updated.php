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
class course_module_completion_updated extends base_statement {

    use module_context;

    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {

        // Get completion.
        global $DB;
        $completion = $DB->get_record('course_modules_completion', ['id' => $this->event->objectid], '*', MUST_EXIST);
        $cm = $DB->get_record('course_modules', ['id' => $completion->coursemoduleid], '*', MUST_EXIST);
        $module = $DB->get_record('modules', ['id' => $cm->module], '*', MUST_EXIST);

        // Check that the completion is automated.
        if ($cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
            return false;
        }

        // Check the completion status.
        if (!in_array($completion->completionstate, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS, COMPLETION_COMPLETE_FAIL])) {
            return false;
        }

        // Define the verb.
        $passed = null;
        switch ($completion->completionstate) {
            case COMPLETION_COMPLETE_PASS:
                $passed = true;
                break;
            case COMPLETION_COMPLETE_FAIL:
                $passed = false;
                break;
        }

        // Define the result.
        $result = [
            'completion' => true
        ];
        if (!is_null($passed)) {
            $result['success'] = $passed;
        }

        // Build the statement.
        return array_replace($this->base($module->name), [
            'actor' => $this->actors->get('user', $this->event->userid),
            'verb' => $this->verbs->get('completed'),
            'object' => $this->activities->get($module->name, $cm->instance, true, 'module'),
            'result' => $result
        ]);
    }

}
