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

if ($hassiteconfig) {

    // LRS settings.

    // Endpoint.
    $settings->add(new admin_setting_configtext('logstore_trax/lrs_endpoint',
        new lang_string('lrs_endpoint', 'logstore_trax'),
        new lang_string('lrs_endpoint_help', 'logstore_trax'),
        'http://example.com/lrs/services/xapi',
        PARAM_URL
    ));

    // Username.
    $settings->add(new admin_setting_configtext('logstore_trax/lrs_username',
        new lang_string('lrs_username', 'logstore_trax'),
        new lang_string('lrs_username_help', 'logstore_trax'),
        '',
        PARAM_TEXT
    ));

    // Password.
    $settings->add(new admin_setting_configtext('logstore_trax/lrs_password',
        new lang_string('lrs_password', 'logstore_trax'),
        new lang_string('lrs_password_help', 'logstore_trax'),
        '',
        PARAM_TEXT
    ));


     // XAPI data settings.

     // Platform IRI.
    $settings->add(new admin_setting_configtext('logstore_trax/platform_iri',
        new lang_string('platform_iri', 'logstore_trax'),
        new lang_string('platform_iri_help', 'logstore_trax'),
        'http://example.com/lms',
        PARAM_URL
    ));


     // Plugin settings.

     // Buffer size.
     $settings->add(new admin_setting_configtext('logstore_trax/buffersize',
        new lang_string('buffersize', 'logstore_trax'),
        new lang_string('buffersize_help', 'logstore_trax'),
        '50',
        PARAM_INT
     ));


}
