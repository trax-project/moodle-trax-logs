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

namespace logstore_trax\src\statements\mod_hvp;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\statements\mod_hvp\hvp_utils;
use logstore_trax\src\utils\module_context;
use logstore_trax\src\statements\core\course_module_completion_updated as core_course_module_completion_updated;

/**
 * xAPI transformation of a Moodle event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_completion_updated extends core_course_module_completion_updated {

    use module_context, hvp_utils;

    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {

        // Get data.
        list($completion, $module, $object) = $this->get_completion_data();
        if (!$completion) return false;
        list($verb, $result) = $this->get_verb_result($completion);

        // Get the vocab type.
        $vocabtype = $this->module_vocab_type($object);

        // Build the statement.
        return array_replace($this->base('hvp', true, $vocabtype), [
            'actor' => $this->actors->get('user', $this->event->userid),
            'verb' => $verb,
            'object' => $this->activities->get('hvp', $object->id, true, 'module', $vocabtype),
            'result' => $result
        ]);
    }

}
