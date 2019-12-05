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
        global $DB;

        if ($this->eventother->enrol == 'cohort') {
            $user_enrolment = $DB->get_record('user_enrolments', ['id' => $this->event->objectid], '*', MUST_EXIST);
            $enrol = $DB->get_record('enrol', ['id' => $user_enrolment->enrolid], '*', MUST_EXIST);
            return $this->actors->get_cohort($enrol->customint1);
        } 
        
        if ($this->eventother->enrol == 'meta') {

            // Check meta links.
            $enrols = $DB->get_records('enrol', ['enrol' => 'meta', 'courseid' => $this->event->courseid], 'customint1');
            foreach ($enrols as $enrol) {

                // Check cohorts on the meta course.
                $meta_enrols = $DB->get_records('enrol', ['enrol' => 'cohort', 'courseid' => $enrol->customint1], 'id');
                foreach ($meta_enrols as $meta_enrol) {

                    // Check cohort registration.
                    $user_enrolment = $DB->get_record('user_enrolments', ['enrolid' => $meta_enrol->id, 'userid' => $this->event->relateduserid]);
                    if ($user_enrolment) {
                        return $this->actors->get_cohort($meta_enrol->customint1);
                    }
                }
            }
        }

        return false;
    }

}
