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
        $config = get_config('logstore_trax');
        $named = !config::anonymous() || ($full && !$config->xis_anonymization);
        if (!$named && $type == 'user') {

            // Anonymized user.
            return $this->get_anonymized_user($mid, $entry);

        } else {

            // Not anonymized.
            $method = 'get_' . $type;
            return $this->$method($mid);
        }
    }

    /**
     * Get an anonymized user, given a Moodle ID.
     *
     * @param int $mid Moodle ID of the cohort
     * @param stdClass $entry DB entry
     * @return array
     */
    public function get_anonymized_user(int $mid = 0, $entry = null) {
        global $DB;
        if (!isset($entry)) {
            $user = $DB->get_record('user', ['id' => $mid], '*', MUST_EXIST);
            $entry = $this->get_or_create_db_entry($mid, 'user', ['email' => $user->email]);
            if (!isset($entry->email) || $entry->email != $user->email) {
                $entry->email = $user->email;
                $this->update_db_entry($entry);
            }
        }
        return [
            'objectType' => 'Agent',
            'account' => [
                'name' => $entry->uuid,
                'homePage' => $this->platform_iri(),
            ]
        ];
    }

    /**
     * Get a reverse user, given an xAPI data.
     *
     * @param array $actor
     * @return object|false
     */
    public function get_reverse_user(array $actor) {
        global $DB;
        if (config::anonymous()) {
            $uuid = $actor['account']['name'];
            $actor = $DB->get_record('logstore_trax_actors', ['uuid' => $uuid]);
            if (!$actor) {
                return false;
            }
            $user = $DB->get_record('user', ['id' => $actor->mid]);
        } elseif (config::mbox()) {
            $email = substr($actor['mbox'], 7);
            $actor = $DB->get_record('logstore_trax_actors', ['email' => $email]);
            if (!$actor) {
                return false;
            }
            $user = $DB->get_record('user', ['id' => $actor->mid]);
        } else {
            $username = $actor['account']['name'];
            $user = $DB->get_record('user', ['username' => $username]);
        }
        if (!$user) {
            return false;
        }
        return $user;
    }

    /**
     * Get a user, given a Moodle ID.
     *
     * @param int $mid Moodle ID of the cohort
     * @return array
     */
    public function get_user(int $mid = 0) {
        global $DB;
        $user = $DB->get_record('user', ['id' => $mid], '*', MUST_EXIST);
        $name = $user->firstname . ' ' . $user->lastname;
        if (config::mbox()) {

            // Mbox.
            return [
                'objectType' => 'Agent',
                'name' => $name,
                'mbox' => 'mailto:' . $user->email,
            ];

        } else {

            // Account with username.
            return [
                'objectType' => 'Agent',
                'name' => $name,
                'account' => [
                    'name' => $user->username,
                    'homePage' => $this->platform_iri(),
                ]
            ];
        }
    }

    /**
     * Get a cohort, given a Moodle ID.
     *
     * @param int $mid Moodle ID of the cohort
     * @param bool $with_members Include members?
     * @return array
     */
    public function get_cohort(int $mid = 0, bool $with_members = false) {
        global $DB;

        // Group base.
        $cohort = $DB->get_record('cohort', ['id' => $mid], 'name', MUST_EXIST);
        $entry = $this->get_or_create_db_entry($mid, 'cohort');
        $group = [
            'objectType' => 'Group',
            'name' => $cohort->name,
            'account' => [
                'name' => $entry->uuid,
                'homePage' => $this->platform_iri(),
            ]
        ];

        // Group members.
        if ($with_members) {
            $members = $DB->get_records('cohort_members', ['cohortid' => $mid], 'userid');
            $group['member'] = array_values(array_filter(array_map(function ($member) {
                try {
                    return $this->get('user', $member->userid);
                } catch (\moodle_exception $e) {
                    return;
                }
            }, $members)));
        }
        return $group;
    }

    /**
     * Get a system actor.
     *
     * @return array
     */
    public function get_system() {
        return [
            'objectType' => 'Agent',
            'account' => [
                'name' => 'system',
                'homePage' => $this->platform_iri(),
            ]
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

        // Not anonymous.
        // Users are not in the logstore_trax_actors table.
        // We get them from the users table.
        if (!config::anonymous()) {
            $user = $DB->get_record('user', ['email' => $email]);
            if (!$user) {
                return [];
            }
            return [
                $this->get_user($user->id)
            ];
        }

        // Anonymous.
        // We get them from the logstore_trax_actors table.
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
