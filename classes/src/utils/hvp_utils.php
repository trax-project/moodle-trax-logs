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
 * Util functions for H5P events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Util functions for H5P events.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hvp_utils {

    /**
     * Get course module ID from module IRI.
     *
     * @param string $moduleiri Module IRI
     * @return string
     */
    public static function module_cmid($moduleiri) {
        $parts = explode('mod/hvp/view.php?id=', $moduleiri);
        if (count($parts) < 2 || !$cmid = intval($parts[1])) {
            print_error('event_hvp_xapi_error_iri', 'logstore_trax');
        }
        return $cmid;
    }

}
