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
 * Trait to build a module context.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait to build a module context.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait module_role {

    /**
     * Get the user role in the module context.
     *
     * @return string|false
     */
    protected function module_role() {

        // Check that we are really in a module context.
        if ($this->event->contextlevel != CONTEXT_MODULE) {
            return false;
        }

        // Get the module context.
        $moodle_context = \context_module::instance($this->event->contextinstanceid);
        if (!$moodle_context) {
            return false;
        }

        // Get the user roles in this context.
        $moodle_roles = get_user_roles($moodle_context, $this->event->userid);
        if (empty($moodle_roles)) {
            return false;
        }

        // Search for the role applying at the module level.
        $role = false;
        foreach ($moodle_roles as $moodle_role) {
            if ($moodle_role->contextid == $this->event->contextid) {
                $role = $moodle_role;
                break;
            }
        }
        if (!$role) {
            return false;
        }

        return $role->shortname;
    }
}
