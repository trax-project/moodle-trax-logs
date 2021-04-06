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
class cohort_defined extends base_statement {

    /**
     * Platform.
     *
     * @var string $platform
     */
    protected $platform = '';


    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {
        global $DB;

        // Build the xAPI cohort.
        $cohort = $this->actors->get_cohort($this->eventother->id, true);

        // Check if it has changed.
        $last = $DB->get_record('logstore_trax_status', [
            'event' => 'cohort_defined',
            'objecttable' => 'cohort',
            'objectid' => $this->eventother->id
        ]);
        $encoded = json_encode($cohort);
        if ($last && $last->data == $encoded) {
            // Return -1 which is a specific code to say "don't log this!"
            return -1;
        }

        // Record the current value.
        if ($last) {
            $last->data = $encoded;
            $DB->update_record('logstore_trax_status', $last);
        } else {
            $last = (object)[
                'event' => 'cohort_defined',
                'objecttable' => 'cohort',
                'objectid' => $this->eventother->id,
                'data' => $encoded
            ];
            $DB->insert_record('logstore_trax_status', $last);
        }

        // Return the statement.
        return array_replace($this->base('system'), [
            'actor' => $this->actors->get_system(),
            'verb' => $this->verbs->get('defined'),
            'object' => $cohort,
        ]);
    }
}
