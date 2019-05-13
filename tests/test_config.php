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
 * Configuration for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Configuration for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_config extends advanced_testcase {

    /**
     * LRS endpoint.
     *
     * @var string $lrsendpoint
     */
    protected $lrsendpoint = 'http://trax.test/trax/ws/xapi';

    /**
     * Basic HTTP username.
     *
     * @var string $lrsusername
     */
    protected $lrsusername = 'testsuite';

    /**
     * Basic HTTP password.
     *
     * @var string $lrspassword
     */
    protected $lrspassword = 'password';

    /**
     * Moodle platform IRI.
     *
     * @var string $platformiri
     */
    protected $platformiri = 'http://moodle.test';


    /**
     * Prepare test session.
     */
    protected function prepare_session() {

        // Prepare testing context.
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Enable logging plugin and configure it.
        set_config('enabled_stores', 'logstore_trax', 'tool_log');
        set_config('lrs_endpoint', $this->lrsendpoint, 'logstore_trax');
        set_config('lrs_username', $this->lrsusername, 'logstore_trax');
        set_config('lrs_password', $this->lrspassword, 'logstore_trax');
        set_config('platform_iri', $this->platformiri, 'logstore_trax');
        set_config('buffersize', 0, 'logstore_trax');

        // Create a user.
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        return $user;
    }

}
