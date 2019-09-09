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

use \logstore_trax\src\config;

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
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->types = json_decode(json_encode($this->types));
    }

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
        global $DB;
        $config = get_config('logstore_trax');
        $named = !config::anonymous() || ($full && !$config->xis_anonymization);

        // Base.
        $res = [
            'objectType' => $this->types->$type->object_type,
        ];
        if (!$named) {

            // Anonymized.
            if (!isset($entry)) {
                $record = $DB->get_record($type, ['id' => $mid], '*', MUST_EXIST);
                $entry = $this->get_or_create_db_entry($mid, $type, ['email' => $record->email]);
            }
            $res['account'] = [
                'name' => $entry->uuid,
                'homePage' => $this->platform_iri(),
            ];

        } else {

            // Not anonymized.
            $record = $DB->get_record($type, ['id' => $mid], '*', MUST_EXIST);
            $res['name'] = $record->firstname . ' ' . $record->lastname;

            if (config::mbox()) {

                // Mbox.
                $res['mbox'] = 'mailto:' . $record->email;
                
            } else {

                // Account with username.
                $res['account'] = [
                    'name' => $record->username,
                    'homePage' => $this->platform_iri(),
                ];
            }
        }
        return $res;
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

    /**
     * Get an actor, given an UUID.
     *
     * @param string $uuid UUID of actor
     * @param bool $full Give the full definition of the item?
     * @return array
     */
    public function get_existing_by_uuid(string $uuid, bool $full = false) {
        $entry = $this->get_db_entry_by_uuid_or_fail($uuid);
        return $this->get($entry->type, $entry->mid, $full, $entry);
    }

    /**
     * Get actors matching with a given email.
     *
     * @param string $email Actor email
     * @return array
     */
    public function get_by_email(string $email) {
        global $DB;
        $entries = $DB->get_records('logstore_trax_actors', ['email' => $email]);

        return array_values(array_map(function ($entry) {

            return [
                'objectType' => 'Agent',
                'account' => [
                    'name' => $entry->uuid,
                    'homePage' => $this->platform_iri(),
                ],
            ];

        }, $entries));
    }


}
