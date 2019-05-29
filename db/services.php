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

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'logstore_trax_get_actors' => array(
        'classname'     => 'logstore_trax_external',
        'methodname'    => 'get_actors',
        'description'   => 'Get xAPI actors.',
        'type'          => 'read'
    ),
    'logstore_trax_get_activities' => array(
        'classname' => 'logstore_trax_external',
        'methodname' => 'get_activities',
        'description' => 'Get xAPI activities.',
        'type' => 'read'
    )
);
