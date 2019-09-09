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
 * Trait to implement an external service consumer (LTI).
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait to implement an external service consumer (LTI).
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait consumer_statement {

    /**
     * Get the consumer.
     *
     * @param int $userid
     * @return array|false
     */
    protected function consumer(int $userid) {
        global $DB;
        if ($user = $DB->get_record('user', ['id' => $userid])) {
            if ($user->auth == 'lti') {
                if ($enrol = $DB->get_record('enrol_lti_users', ['userid' => $user->id])) {

                    return [
                        'objectType' => 'Activity',
                        'id' => $enrol->serviceurl,
                        'definition' => [
                            'type' => 'http://vocab.xapi.fr/activities/consumer',
                            'extensions' => [
                                'http://vocab.xapi.fr/extensions/standard' => 'lti',
                            ],
                        ],
                    ];
                }
            }
        }

        return false;
    }

}
