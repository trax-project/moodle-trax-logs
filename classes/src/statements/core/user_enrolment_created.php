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

/**
 * xAPI transformation of a Moodle event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_enrolment_created extends base_statement {

    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {
        return array_replace($this->base('course'), [
            'actor' => $this->actors->get('user', $this->event->relateduserid),
            'verb' => $this->verbs->get('registered'),
            'object' => $this->activities->get('course', $this->event->courseid)
        ]);
    }

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

        // Define the user role.
        if ($role = $this->role()) {
            $context['extensions']['http://vocab.xapi.fr/extensions/user-role'] = $role;
        }
        // Define the cohort.
        if ($cohort = $this->cohort()) {
            $context['team'] = $cohort;
        }
        return $context;
    }

    /**
     * Get the user role.
     *
     * @return string|false
     */
    protected function role() {
        $moodle_context = \context_course::instance($this->event->courseid);
        $moodle_roles = get_user_roles($moodle_context, $this->event->relateduserid);
        if (empty($moodle_roles)) {
            return false;
        }
        $roles = array_map(function ($moodle_role) {
            return $moodle_role->shortname;
        }, $moodle_roles);

        return implode(',', $roles);
    }

    /**
     * Get the user cohort.
     *
     * @return string|false
     */
    protected function cohort() {
        switch ($this->eventother->enrol) {
            case 'cohort':
                return $this->cohort_cohort_enrol();
            case 'meta':
                return $this->cohort_meta_enrol();
            default:
                return false;
        }
    }

    /**
     * Get the user cohort.
     *
     * @return string|false
     */
    protected function cohort_cohort_enrol() {
        global $DB;
        $user_enrolment = $DB->get_record('user_enrolments', ['id' => $this->event->objectid], '*', MUST_EXIST);
        $enrol = $DB->get_record('enrol', ['id' => $user_enrolment->enrolid], '*', MUST_EXIST);
        return $this->actors->get_cohort($enrol->customint1);
    }

    /**
     * Get the user cohort.
     *
     * @return string|false
     */
    protected function cohort_meta_enrol() {
        global $DB;
        $sql = "
            SELECT meta_enrol.customint1
            FROM {enrol} AS course_enrol
            INNER JOIN {enrol} AS meta_enrol ON meta_enrol.courseid = course_enrol.customint1
            INNER JOIN {user_enrolments} ON {user_enrolments}.enrolid = meta_enrol.id
            WHERE course_enrol.enrol = 'meta' AND meta_enrol.enrol = 'cohort' AND course_enrol.courseid = ? AND {user_enrolments}.userid = ?
        ";
        $params = [$this->event->courseid, $this->event->relateduserid];
        $record = $DB->get_record_sql($sql, $params);
        if (!$record) {
            return false;
        }
        return $this->actors->get_cohort($record->customint1);
    }

}
