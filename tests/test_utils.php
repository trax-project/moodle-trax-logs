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
 * Utilities for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \logstore_trax\src\controller as trax_controller;

/**
 * Utilities for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait test_utils {

    /**
     * Trax Logs controller.
     *
     * @var controller $controller
     */
    protected $controller;

    /**
     * Moodle standard store.
     *
     * @var \tool_log\log\writer $store
     */
    protected $store;


    /**
     * Prepare test session.
     */
    protected function prepare_session() {

        // Utilities.
        $this->controller = new trax_controller();

        // Prepare testing context.
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Enable logging plugin and configure it.
        set_config('lrs_endpoint', $this->lrsendpoint, 'logstore_trax');
        set_config('lrs_username', $this->lrsusername, 'logstore_trax');
        set_config('lrs_password', $this->lrspassword, 'logstore_trax');
        set_config('platform_iri', $this->platformiri, 'logstore_trax');
        set_config('buffersize', 0, 'logstore_trax');

        // Create a user.
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Enable standard logstore.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        $this->store = $stores['logstore_standard'];

        return $user;
    }

    /**
     * Flush events.
     */
    protected function flush() {
        $this->store->flush();
    }

}
