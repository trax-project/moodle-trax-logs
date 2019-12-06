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

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => '\logstore_trax\task\sync_task',
        'blocking' => 0,
        'minute' => '*/1',
        'hour' => '*',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => '\logstore_trax\task\define_groups_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*/1',
        'dayofweek' => '*',
        'month' => '*'
    ),
    array(
        'classname' => '\logstore_trax\task\define_courses_task',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*/1',
        'dayofweek' => '*',
        'month' => '*'
    ),
);