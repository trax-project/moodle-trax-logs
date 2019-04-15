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

use moodle_exception;
use logstore_trax\src\util;

/**
 * Abstract class used to implement an index of xAPI resources (e.g. actors, activities).
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class index {

    /**
     * Config.
     *
     * @var stdClass $config
     */
    protected $config;

    /**
     * DB table.
     *
     * @var string $table
     */
    protected $table;


    /**
     * Constructor.
     *
     * @param stdClass $config Config
     * @return void
     */
    public function __construct($config) {
        $this->config = $config;
        if (substr($this->config->platform_iri, -1) == '/') {
            $this->config->platform_iri = substr($this->config->platform_iri, 0, -1);
        }
        $this->types = json_decode(json_encode($this->types));
    }

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
     * @return stdClass
     */
    protected function get_or_create_db_entry(int $mid, string $type) {
        global $DB;
        $entry = $this->get_db_entry($mid, $type);
        if (!$entry) {
            $entry = (object)[
                'mid' => $mid,
                'type' => $this->types->$type->db,
                'uuid' => util::uuid(),
            ];
            $entry->id = $DB->insert_record($this->table, $entry);
        }
        return $entry;
    }

    /**
     * Get an entry from the DB, and rise an exception if the entry does not exist.
     *
     * @param int $mid Moodle ID of the item
     * @param string $type Type of item
     * @return stdClass
     */
    protected function get_db_entry_or_fail(int $mid, string $type) {
        $entry = $this->get_db_entry($mid, $type);
        if (!$entry) {
            throw new moodle_exception('activity_entry_not_found', 'logstore_trax');
        }
        return $entry;
    }

    /**
     * Get an entry from the DB.
     *
     * @param int $mid Moodle ID of the item
     * @param string $type Type of item
     * @return stdClass
     */
    protected function get_db_entry(int $mid, string $type) {
        global $DB;
        return $DB->get_record($this->table, [
            'mid' => $mid,
            'type' => $this->types->$type->db,
        ]);
    }

}
