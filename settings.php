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

use logstore_trax\src\config;

if ($hassiteconfig) {

    // -------------------- LRS settings --------------------.

    $settings->add(new admin_setting_heading(
        'lrs',
        get_string('lrs_settings', 'logstore_trax'),
        get_string('lrs_settings_help', 'logstore_trax')
    ));

    // Endpoint.
    $settings->add(new admin_setting_configtext(
        'logstore_trax/lrs_endpoint',
        new lang_string('lrs_endpoint', 'logstore_trax'),
        new lang_string('lrs_endpoint_help', 'logstore_trax'),
        'http://my.lrs/endpoint',
        PARAM_URL
    ));

    // Username.
    $settings->add(new admin_setting_configtext(
        'logstore_trax/lrs_username',
        new lang_string('lrs_username', 'logstore_trax'),
        new lang_string('lrs_username_help', 'logstore_trax'),
        '',
        PARAM_TEXT
    ));

    // Password.
    $settings->add(new admin_setting_configtext(
        'logstore_trax/lrs_password',
        new lang_string('lrs_password', 'logstore_trax'),
        new lang_string('lrs_password_help', 'logstore_trax'),
        '',
        PARAM_TEXT
    ));


     // -------------------- XAPI identification settings --------------------.

    $settings->add(new admin_setting_heading(
        'identification',
        get_string('xapi_identification_settings', 'logstore_trax'),
        get_string('xapi_identification_settings_help', 'logstore_trax')
    ));

    // Platform IRI.
    $settings->add(new admin_setting_configtext(
        'logstore_trax/platform_iri',
        new lang_string('platform_iri', 'logstore_trax'),
        new lang_string('platform_iri_help', 'logstore_trax'),
        'http://my.lms',
        PARAM_URL
    ));

    // Actors identification.
    $settings->add(new admin_setting_configselect(
        'logstore_trax/actors_identification', 
        get_string('actors_identification', 'logstore_trax'), 
        get_string('actors_identification_help', 'logstore_trax'),
        config::ID_ANONYMOUS,
        config::actors_identification_modes()
    ));

    // Actors anonymization in XIS.
    $settings->add(new admin_setting_configcheckbox(
        'logstore_trax/xis_anonymization',
        get_string('xis_anonymization', 'logstore_trax'),
        get_string('xis_anonymization_help', 'logstore_trax'),
        1
    ));


    // -------------------- Logged Events --------------------.

    $settings->add(new admin_setting_heading(
        'events',
        get_string('logged_events', 'logstore_trax'),
        get_string('logged_events_help', 'logstore_trax')
    ));

    // First log.
    $settings->add(new admin_setting_configtext(
        'logstore_trax/firstlogs',
        get_string('firstlogs', 'logstore_trax'),
        get_string('firstlogs_help', 'logstore_trax'),
        date('d/m/Y'),
        "/^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/",
        10
    ));

    // Moodle core events.
    $settings->add(new admin_setting_configmulticheckbox(
        'logstore_trax/core_events',
        get_string('core_events', 'logstore_trax'),
        get_string('core_events_help', 'logstore_trax'),
        config::default_core_events(),
        config::loggable_core_events()
    ));

    // Moodle components.
    $settings->add(new admin_setting_configmulticheckbox(
        'logstore_trax/moodle_components',
        get_string('moodle_components', 'logstore_trax'),
        get_string('moodle_components_help', 'logstore_trax'),
        config::default_moodle_components(),
        config::loggable_moodle_components()
    ));

    // Additional components.
    $settings->add(new admin_setting_configmulticheckbox(
        'logstore_trax/additional_components',
        get_string('additional_components', 'logstore_trax'),
        get_string('additional_components_help', 'logstore_trax'),
        config::default_additional_components(),
        config::loggable_additional_components()
    ));

    // CRON-based statements.
    $settings->add(new admin_setting_configmulticheckbox(
        'logstore_trax/scheduled_statements',
        get_string('scheduled_statements', 'logstore_trax'),
        get_string('scheduled_statements_help', 'logstore_trax'),
        config::default_scheduled_statements(),
        config::loggable_scheduled_statements()
    ));

    // Live logs (proxy).
    $settings->add(new admin_setting_configtext(
        'logstore_trax/resend_livelogs_until',
        get_string('resend_livelogs_until', 'logstore_trax'),
        get_string('resend_livelogs_until_help', 'logstore_trax'),
        date('d/m/Y', time() - (60 * 60 * 24)),
        "/^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/",
        10
    ));


     // -------------------- Data transportation settings --------------------.

    $settings->add(new admin_setting_heading(
        'transfer',
        get_string('data_transfert_settings', 'logstore_trax'),
        get_string('data_transfert_settings_help', 'logstore_trax')
    ));

    // Sync mode
    $settings->add(new admin_setting_configselect(
        'logstore_trax/sync_mode', 
        get_string('sync_mode', 'logstore_trax'), 
        get_string('sync_mode_help', 'logstore_trax'),
        config::SYNC,
        config::sync_modes()
    ));

    // Failed attempts.
    $settings->add(new admin_setting_configtext(
        'logstore_trax/attempts',
        get_string('attempts', 'logstore_trax'),
        get_string('attempts_help', 'logstore_trax'),
        1,
        PARAM_INT
    ));

    // DB batch size.
    $settings->add(new admin_setting_configtext(
        'logstore_trax/db_batch_size',
        new lang_string('db_batch_size', 'logstore_trax'),
        new lang_string('db_batch_size_help', 'logstore_trax'),
        100,
        PARAM_INT
    ));

    // xAPI batch size.
    $settings->add(new admin_setting_configtext(
        'logstore_trax/xapi_batch_size',
        new lang_string('xapi_batch_size', 'logstore_trax'),
        new lang_string('xapi_batch_size_help', 'logstore_trax'),
        10,
        PARAM_INT
    ));
}

