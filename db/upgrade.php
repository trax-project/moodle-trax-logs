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
        upgrade_plugin_savepoint(true, 2018050801, 'logstore', 'trax');
    }

    // Type column changed from INT to CHAR.
    if ($oldversion < 2018050804) {

        // Drop actors index.
        $table = new xmldb_table('logstore_trax_actors');
        $index = new xmldb_index('mid-type', XMLDB_INDEX_UNIQUE, array('mid', 'type'));
        $dbman->drop_index($table, $index);	

         // Drop activities index.
        $table = new xmldb_table('logstore_trax_activities');
        $index = new xmldb_index('mid-type', XMLDB_INDEX_UNIQUE, array('mid', 'type'));
        $dbman->drop_index($table, $index);	

        	
        // Change actors type column.
        $table = new xmldb_table('logstore_trax_actors');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, 'mid');
        $dbman->change_field_type($table, $field);

         // Change activities type column.
        $table = new xmldb_table('logstore_trax_activities');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, 'mid');
        $dbman->change_field_type($table, $field);


        // Update actors records.
        $records = $DB->get_records('logstore_trax_actors');
        foreach ($records as $record) {
            $record->type = 'user';
            $DB->update_record('logstore_trax_actors', $record);
        }

        // Update activities records.
        $records = $DB->get_records('logstore_trax_activities');
        $types = [
            '0' => 'profile',
            '1' => 'system',
            '3' => 'course',
            '101' => 'assign',
            '102' => 'book',
            '103' => 'chat',
            '104' => 'choice',
            '105' => 'data',
            '106' => 'feedback',
            '107' => 'folder',
            '108' => 'forum',
            '109' => 'glossary',
            '110' => 'imscp',
            '111' => 'lesson',
            '112' => 'lti',
            '113' => 'page',
            '114' => 'quiz',
            '115' => 'resource',
            '116' => 'scorm',
            '117' => 'survey',
            '118' => 'url',
            '119' => 'wiki',
            '120' => 'workshop',
        ];
        foreach ($records as $record) {
            if (!isset($types[$record->type])) continue;
            $record->type = $types[$record->type];
            $DB->update_record('logstore_trax_activities', $record);
        }
        	
        // Savepoint.	
        upgrade_plugin_savepoint(true, 2018050804, 'logstore', 'trax');
    }

    // Add logs table.
    if ($oldversion < 2018050805) {

        // Define the table.
        $table = new xmldb_table('logstore_trax_logs');

        // Add fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('mid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED);
        $table->add_field('error', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('attempts', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('newattempt', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Create the table.
        $dbman->create_table($table);

        // Savepoint.	
        upgrade_plugin_savepoint(true, 2018050805, 'logstore', 'trax');
    }

    // Add some indexes.
    if ($oldversion < 2018050806) {

        // Actors mid-type index.
        $table = new xmldb_table('logstore_trax_actors');
        $index = new xmldb_index('mid-type', XMLDB_INDEX_UNIQUE, array('mid', 'type'));
        $dbman->add_index($table, $index);

        // Actors uuid index (unique).
        $index = new xmldb_index('uuid', XMLDB_INDEX_UNIQUE, array('uuid'));
        $dbman->add_index($table, $index);

        // Activities mid-type index.
        $table = new xmldb_table('logstore_trax_activities');
        $index = new xmldb_index('mid-type', XMLDB_INDEX_UNIQUE, array('mid', 'type'));
        $dbman->add_index($table, $index);

        // Activities uuid index (not unique).
        $index = new xmldb_index('uuid', XMLDB_INDEX_NOTUNIQUE, array('uuid'));
        $dbman->add_index($table, $index);

        // Logs mid index.
        $table = new xmldb_table('logstore_trax_logs');
        $index = new xmldb_index('mid', XMLDB_INDEX_UNIQUE, array('mid'));
        $dbman->add_index($table, $index);

        // Savepoint.
        upgrade_plugin_savepoint(true, 2018050806, 'logstore', 'trax');
    }

    // Add some indexes.
    if ($oldversion < 2018050810) {
        $table = new xmldb_table('logstore_trax_logs');

        // Error index (not unique).
        $index = new xmldb_index('error', XMLDB_INDEX_NOTUNIQUE, array('error'));
        $dbman->add_index($table, $index);

        // Attempts index (not unique).
        $index = new xmldb_index('attempts', XMLDB_INDEX_NOTUNIQUE, array('attempts'));
        $dbman->add_index($table, $index);

        // Newattempt index (not unique).
        $index = new xmldb_index('newattempt', XMLDB_INDEX_NOTUNIQUE, array('newattempt'));
        $dbman->add_index($table, $index);

        // Savepoint.
        upgrade_plugin_savepoint(true, 2018050810, 'logstore', 'trax');
    }

    // Add email column in actors table.
    if ($oldversion < 2018050812) {

        // Add field.
        $table = new xmldb_table('logstore_trax_actors');
        $field = new xmldb_field('email', XMLDB_TYPE_CHAR, '100', null, false, false, null, 'mid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add index (unique).
        $index = new xmldb_index('email', XMLDB_INDEX_NOTUNIQUE, array('email'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Update actors records.
        $records = $DB->get_records('logstore_trax_actors');
        foreach ($records as $record) {
            if ($user = $DB->get_record('user', ['id' => $record->mid])) {
                $record->email = $user->email;
                $DB->update_record('logstore_trax_actors', $record);
            }
        }

        // Savepoint.	
        upgrade_plugin_savepoint(true, 2018050812, 'logstore', 'trax');
    }

    // Add status table.
    if ($oldversion < 2018050818) {

        // Define the table.
        $table = new xmldb_table('logstore_trax_status');

        // Add fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('event', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL);
        $table->add_field('objecttable', XMLDB_TYPE_CHAR, '50');
        $table->add_field('objectid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED);
        $table->add_field('data', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL);

        // Adding keys to table.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Create the table.
        $dbman->create_table($table);

        // Savepoint.	
        upgrade_plugin_savepoint(true, 2018050818, 'logstore', 'trax');
    }

    return true;
}
