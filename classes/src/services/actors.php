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
 * Actors service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\services;

defined('MOODLE_INTERNAL') || die();

/**
 * Actors service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class actors extends index {

    /**
     * Types of actors.
     *
     * @var array $types
     */
    protected $types = [
        'user' => ['object_type' => 'Agent'],
    ];

    /**
     * DB table.
     *
     * @var string $table
     */
    protected $table = 'logstore_trax_actors';


    /**
     * Get an actor, given a Moodle ID and an actor type.
     *
     * @param string $type Type of actor (user, cohort...)
     * @param int $mid Moodle ID of the activity
     * @param bool $full Give the full definition of the item?
     * @param stdClass $entry DB entry
     * @return array
     */
    public function get(string $type, int $mid = 0, bool $full = false, $entry = null) {
        if (!isset($entry)) {
            $entry = $this->get_or_create_db_entry($mid, $type);
        }
        return [
            'objectType' => $this->types->$type->object_type,
            'account' => [
                'homePage' => $this->config->platform_iri,
                'name' => $entry->uuid,
            ],
        ];
    }

    /**
     * Get an actor, given a Moodle ID and an actor type.
     *
     * @param string $type Type of actor (user, cohort...)
     * @param int $mid Moodle ID of the activity
     * @param bool $full Give the full definition of the item?
     * @return array
     */
    public function get_existing(string $type, int $mid = 0, bool $full = false) {
        $entry = $this->get_db_entry_or_fail($mid, $type);
        return $this->get($type, $mid, $full, $entry);
    }


}
