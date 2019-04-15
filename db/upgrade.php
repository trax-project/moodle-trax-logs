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

/**
 * Update database.
 *
 * @param int $oldversion Previous version of the plugin.
 * @return bool
 */
function xmldb_logstore_trax_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Index UUID are no longer unique.
    if ($oldversion < 2018050801) {

        // Actors index.
        $table = new xmldb_table('logstore_trax_actors');
        $index = new xmldb_index('uuid', XMLDB_INDEX_UNIQUE, array('uuid'));
        $dbman->drop_index($table, $index);

        // Activities index.
        $table = new xmldb_table('logstore_trax_activities');
        $index = new xmldb_index('uuid', XMLDB_INDEX_UNIQUE, array('uuid'));
        $dbman->drop_index($table, $index);

        // Savepoint.
        upgrade_plugin_savepoint(true, 2018050802, 'logstore', 'trax');
    }

    return true;
}
