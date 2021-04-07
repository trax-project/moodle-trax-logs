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
 * xAPI transformation of a H5P event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements\logstore_trax;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\statements\base_statement;
use logstore_trax\src\utils\module_context;
use logstore_trax\src\statements\mod_h5pactivity\hvp_utils;

/**
 * xAPI transformation of a H5P event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hvp_quiz_completed extends base_statement {

    use module_context, hvp_utils;

    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {

        // Get the H5P statement.
        $statement = json_decode($this->eventother->statement);

        // Set statement base and object.
        list($base, $object) = $this->statement_base_object($statement, 'h5pactivity-quiz');
        
        // Build the statement.
        return array_replace($base, [
            'actor' => $this->actors->get('user', $this->event->userid),
            'verb' => $this->statement_verb($statement),
            'object' => $object,
            'result' => $statement->result
        ]);

    }

}
