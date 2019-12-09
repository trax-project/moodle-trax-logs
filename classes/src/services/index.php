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
 * Abstract class used to implement an index of xAPI resources (e.g. actors, activities).
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\services;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\utils;

/**
 * Abstract class used to implement an index of xAPI resources (e.g. actors, activities).
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class index {

    /**
     * DB table.
     *
     * @var string $table
     */
    protected $table;


    /**
     * Get an activity, given a Moodle ID and an activity type.
     *
     * @param string $type Type of activity
     * @param int $mid Moodle ID of the activity
     * @param bool $full Give the full definition of the activity?
     * @return array
     */
    abstract public function get(string $type, int $mid = 0, bool $full = false);

    /**
     * Get an entry from the DB, or create it if it does not exist.
     *
     * @param int $mid Moodle ID of the item
     * @param string $type Type of item
     * @param array $data More data
     * @return \stdClass
     */
    public function get_or_create_db_entry(int $mid, string $type, array $data = []) {
        global $DB;
        $entry = $this->get_db_entry($mid, $type);
        if (!$entry) {
            $entry = (object)array_merge([
                'mid' => $mid,
                'type' => $type,
                'uuid' => utils::uuid(),
            ], $data);
            $entry->id = $DB->insert_record($this->table, $entry);
        }
        return $entry;
    }

    /**
     * Update a DB entry.
     *
     * @param \stdClass $entry
     * @return void
     */
    public function update_db_entry(\stdClass $entry) {
        global $DB;
        $DB->update_record($this->table, $entry);
    }

    /**
     * Get an entry from the DB, and rise an exception if the entry does not exist.
     *
     * @param int $mid Moodle ID of the item
     * @param string $type Type of item
     * @return stdClass
     */
    public function get_db_entry_or_fail(int $mid, string $type) {
        $entry = $this->get_db_entry($mid, $type);
        if (!$entry) {
            throw new \moodle_exception('entry_not_found', 'logstore_trax');
        }
        return $entry;
    }

    /**
     * Get an entry from the DB, and rise an exception if the entry does not exist.
     *
     * @param string $uuid UUID of actor
     * @return stdClass
     */
    public function get_db_entry_by_uuid_or_fail(string $uuid) {
        $entry = $this->get_db_entry_by_uuid($uuid);
        if (!$entry) {
            throw new \moodle_exception('entry_not_found', 'logstore_trax');
        }
        return $entry;
    }

    /**
     * Get an entry from the DB, given a Moodle ID and type.
     *
     * @param int $mid Moodle ID of the item
     * @param string $type Type of item
     * @return stdClass
     */
    public function get_db_entry(int $mid, string $type) {
        global $DB;
        return $DB->get_record($this->table, [
            'mid' => $mid,
            'type' => $type,
        ]);
    }

    /**
     * Get an entry from the DB, given a UUID.
     *
     * @param string $uuid UUID of actor
     * @return stdClass
     */
    public function get_db_entry_by_uuid(string $uuid) {
        global $DB;
        return $DB->get_record($this->table, [
            'uuid' => $uuid,
        ]);
    }

    /**
     * Get the platform IRI.
     *
     * @return string
     */
    public function platform_iri() {
        $platformiri = get_config('logstore_trax', 'platform_iri');
        if (substr($platformiri, -1) == '/') {
            $platformiri = substr($platformiri, 0, -1);
        }
        return $platformiri;
    }

}
