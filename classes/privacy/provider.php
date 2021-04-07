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
 * Data privacy implementation.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\privacy;

defined('MOODLE_INTERNAL') || die();

use context;
use context_system;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\metadata\provider as metadata_provider;
use tool_log\local\privacy\logstore_provider;
use core_privacy\local\request\core_userlist_provider;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Data privacy implementation.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements metadata_provider, logstore_provider, core_userlist_provider
{

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        // Internal data.
        $collection->add_database_table('logstore_trax_actors', [
            'mid' => 'privacy:metadata:actors:mid',
            'uuid' => 'privacy:metadata:actors:uuid',
        ], 'privacy:metadata:actors');

        // External data (LRS).
        $collection->add_external_location_link('logstore_trax_actors', [
            'uuid' => 'privacy:metadata:lrs:uuid',
        ], 'privacy:metadata:lrs');

        return $collection;
    }

    /**
     * Add contexts that contain user information for the specified user.
     *
     * @param contextlist $contextlist The contextlist to add the contexts to.
     * @param int $userid The user to find the contexts for.
     * @return void
     */
    public static function add_contexts_for_userid(contextlist $contextlist, $userid) {
        $contextlist->add_system_context();
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        return;
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;
        if (! $context instanceof context_system) {
            return;
        }
        $DB->delete_records('logstore_trax_actors', []);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $contexts = $contextlist->get_contexts();
        foreach ($contexts as $context) {
            if ($context instanceof context_system) {
                $DB->delete_records('logstore_trax_actors', ['mid' => $contextlist->get_user()->id]);
                return;
            }
        }
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        if (!$context instanceof context_system) {
            return;
        }

        $sql = "SELECT DISTINCT actors.mid
                  FROM {logstore_trax_actors} actors";

        $userlist->add_from_sql('mid', $sql, []);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
 
        $context = $userlist->get_context();
        if (!$context instanceof context_system) {
            return;
        }
     
        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $sql = "mid {$userinsql}";
     
        $DB->delete_records_select('logstore_trax_actors', $sql, $userinparams);
    }
}
