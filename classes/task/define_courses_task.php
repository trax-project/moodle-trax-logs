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
 * Define courses task.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\task;

use logstore_trax\src\controller as trax_controller;
use logstore_trax\src\config;

/**
 * Define courses task.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class define_courses_task extends \core\task\scheduled_task
{

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name()
    {
        return get_string('define_courses_task', 'logstore_trax');
    }

    /**
     * Execute the task.
     */
    public function execute()
    {
        if (config::is_scheduled('define_courses')) {
            (new trax_controller())->process_virtual_events('course_defined'); 
        }
    }
}

