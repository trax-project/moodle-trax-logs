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
 * xAPI transformation of a SCORM event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements\mod_scorm;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\statements\base_statement;
use logstore_trax\src\utils\inside_module_context;
use logstore_trax\src\utils as logstore_utils;

/**
 * xAPI transformation of a SCORM event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sco_launched extends base_statement {

    use inside_module_context;


    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {

        return array_replace($this->base('scorm'), [
            'actor' => $this->actors->get('user', $this->event->userid),
            'verb' => $this->verbs->get('launched'),
            'object' => $this->statement_object(),
        ]);

    }

    /**
     * Get the object.
     *
     * @return array
     */
    protected function statement_object() {
        global $DB;
        $course = $DB->get_record('course', ['id' => $this->event->courseid], '*', MUST_EXIST);
        $sco = $DB->get_record($this->event->objecttable, ['id' => $this->event->objectid], '*', MUST_EXIST);
        $module = $this->activities->get('scorm', $this->eventother->instanceid, true, 'module');

        return [
            'objectType' => 'Activity',
            'id' => $module['id'] . '/sco/' . $sco->identifier,
            'definition' => [
                'type' => $this->activities->types->type('sco'),
                'name' => logstore_utils::lang_string($sco->title, $course),
                'extensions' => [
                    'http://vocab.xapi.fr/extensions/standard' => 'scorm'
                ]
            ]
        ];
    }

}
