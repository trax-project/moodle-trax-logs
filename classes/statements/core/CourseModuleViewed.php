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
 * Trax Logs for Moodle.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\statements\core;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\statements\Statement;

class CourseModuleViewed extends Statement {

    /**
     * Build the Statement.
     * 
     * @return array
     */
    protected function statement() {
        return array_replace($this->baseStatement($this->event->objecttable), [
            'actor' => $this->actors->get('user', $this->event->userid),
            'verb' => $this->verbs->get('navigated-in'),
            'object' => $this->activities->get($this->event->objecttable, $this->event->objectid, true, 'module'),
        ]);
    }

    /**
     * Build the context.
     * 
     * @return array
     */
    protected function baseContext($activityType, $withSystem = true) {
        $context = parent::baseContext($activityType, $withSystem);
        $course = $this->activities->get('course', $this->event->courseid, false);
        $context['contextActivities']['parent'] = array($course);
        return $context;
    }

}
