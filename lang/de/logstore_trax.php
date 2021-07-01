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
 * @copyright  Tim Volckmann <tim.volckmann@mastersolution.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin.
$string['pluginname'] = 'TRAX Logs';
$string['pluginname_desc'] = 'Wandelt Moodle-Logs in xAPI-Satements um und schickt diese an ein LRS.';

// Settings.
$string['lrs_settings'] = 'LRS Einstellungen';
$string['lrs_settings_help'] = 'Die folgenden Angaben finden Sie in Ihrem LRS. Unter <a href="http://traxlrs.com" target="_blank">traxlrs.com</a> finden Sie Informationen zu dem TRAX-LRS, sollten sie noch kein LRS-System besitzen. Dieses Plugin sollte jedoch mit jedem xAPI konformen LRS-System funktionieren, siehe <a href="https://adopters.adlnet.gov/products/all/0" target="_blank">xAPI compliant LRS</a>.';

$string['lrs_endpoint'] = 'LRS URL';
$string['lrs_endpoint_help'] = 'Diese URL wird verwendet um die xAPI-Funktionen aufzurufen.';

$string['lrs_username'] = 'LRS Benutzername (Basic HTTP)';
$string['lrs_username_help'] = 'Benutzername der HTTP Basic Authentifizierung, wie er im LRS angelegt wurde.';

$string['lrs_password'] = 'LRS Passwort (Basic HTTP)';
$string['lrs_password_help'] = 'Passwort der HTTP Basic Authentifizierung, wie er im LRS angelegt wurde.';

$string['xapi_identification_settings'] = 'xAPI Identifizierung';
$string['xapi_identification_settings_help'] = 'In diesem Bereich kann eingestellt werden, wie Benutzer
    in den xAPI-Statements identifiziert werden sollen. Bei den Einstellungen ist die Privatsphäre der Benutzer zu berücksichtigen,
    siehe <a href="https://gdpr.org/" target="_blank">Datenschutz-Grundverordnung (DSGVO)</a>.';

$string['platform_iri'] = 'Platform IRI';
$string['platform_iri_help'] = 'Eine IRI ist ein frei wählbarer, eindeutiger Identifier, der mit dem Moodle verbunden ist und sich niemals ändert.';

$string['actors_identification'] = 'Aktor-Identifikation';
$string['actors_identification_help'] = 'Identifikationsmethode der Aktoren in den xAPI-Statements. Wenn "Anonym" ausgewählt ist, wird eine UUID auf Basis des Account-Name und Account-Schema erstellt. Um die Konformität zur Datenschutzgrundverordnung (DSGVO) zu gewährleisten, sollten die Option "Anonym" verwendet werden.';
$string['anonymous'] = 'Anonym (Account mit UUID)';
$string['account_username'] = 'Account (Benutzername)';
$string['mbox'] = 'mbox (eMail-Adresse)';

$string['xis_anonymization'] = 'Web-Service Anonymisierung';
$string['xis_anonymization_help'] = 'Benutzer in den xAPI Identifizierung-Diensten anonymisieren. Wenn diese Option aktiviert ist, werden die Benutzer durch eine erzeugte UUID identifiziert. Der Benutzername wird nicht mit zurückgegeben.';

$string['logged_events'] = 'Ereignis-Log';
$string['logged_events_help'] = 'In diesem Bereich kann festgelegt werden, welche Ereignisse an das LRS geschickt werden sollen.';

$string['firstlogs'] = 'Ereignisse seit dem';
$string['firstlogs_help'] = 'Format: DD/MM/YYYY. Gibt an, ab welchem Datum die Ereignisse an das LRS gesendet werden sollen, wenn asynchrone Synchronisierungsmodus verwendet wird.';

$string['core_events'] = 'Moodle-Core Events';
$string['core_events_help'] = 'Wählen Sie die Ereignisse aus, die an das LRS geschickt werden sollen.';
$string['management'] = 'Verwaltung';
$string['authentication'] = 'Authentifizierung';
$string['navigation'] = 'Navigation';
$string['completion'] = 'Abschlüsse';
$string['grading'] = 'Bewertungen';

$string['moodle_components'] = 'Moodle-Komponenten';
$string['moodle_components_help'] = 'Wählen Sie aus, welche Ereignisse der Moodle-Komponenten an das LRS geschickt werden sollen.';

$string['additional_components'] = 'Zusätzliche Komponenten';
$string['additional_components_help'] = 'Wählen Sie zusätzliche Komponenten aus, die Sie verfolgen möchten.';
$string['mod_h5pactivity'] = 'H5P';
$string['other_components'] = 'Andere Komponenten';

$string['scheduled_statements'] = 'Geplante Vorgänge (CRON)';
$string['scheduled_statements_help'] = 'Wählen Sie die Vorgänge, die regelmäßig gesendet werden sollen.';

$string['define_groups'] = 'Gruppendefinitionen';
$string['define_courses'] = 'Kursdefinitionen';

$string['resend_livelogs_until'] = 'Live-Logs neu senden bis';
$string['resend_livelogs_until_help'] = 'Format: DD/MM/YYYY.
    Live-Logs bis zu diesem Datum werden neu gesendet.';

$string['data_transfert_settings'] = 'Datenübertragung';
$string['data_transfert_settings_help'] = 'In diesem Bereich können Sie einstellen, wie die Daten an das LRS übertragen werden sollen. Vor dem Produktivbetrieb sollten Sie die Einstellungen in einer separaten Umgebung testen.';

$string['sync_mode'] = 'Synchronisierung';
$string['sync_mode_help'] = 'Im asynchronen Modus werden die Ereignisse von einem CRON an das LRS gesendet, was zu entsprechender Verzögerung führen kann. Im synchronen Modus werden die Events in Echtzeit an das LRS übertragen. Im Produktivbetrieb sollte jedoch der asynchrone Modus verwendet werden.';
$string['sync'] = 'Synchron (Testbetrieb)';
$string['async'] = 'Asynchron (Produktivbetrieb)';

$string['attempts'] = 'Versuche';
$string['attempts_help'] = 'Gibt an, wie oft im Fehlerfall versucht werden soll, die Daten an das LRS zu schicken.';

$string['db_batch_size'] = 'Anzahl Log-Einträge';
$string['db_batch_size_help'] = 'Gibt an, wie viele Logeinträge mit jedem CRON-Durchlauf an das LRS geschickt werden sollen.';

$string['xapi_batch_size'] = 'Größe der xAPI-Requests';
$string['xapi_batch_size_help'] = 'Gibt an, wie viele xAPI-Statements in einer POST-Anfrage gebündelt werden können.';

// Exceptions.
$string['invalid_entry_identification'] = 'Ungültige Identifikation des Eintrags.';
$string['entry_not_found'] = 'Eintrag nicht gefunden.';

// Privacy metadata.
$string['privacy:metadata:actors'] = 'Korrelationstabelle zwischen Moodle Benutzer-ID und der anonymen Kennung des Benutzers, die vom LRS verwendet wird.';
$string['privacy:metadata:actors:mid'] = 'Benutzer-ID im Moodle';
$string['privacy:metadata:actors:uuid'] = 'Benutzer-Kennung, die im LRS verwendet wird';
$string['privacy:metadata:lrs'] = 'Ereignisse der Benutzer, die an das LRS gesendet und in der Datenbank des LRS gespeichert wurden.';
$string['privacy:metadata:lrs:uuid'] = 'Benutzer-Kennungin, die im LRS verwendet wird';

// Events.
$string['event_hvp_question_answered'] = 'H5P Frage beantwortet';
$string['event_hvp_quiz_completed'] = 'H5P Quiz abgeschlossen';
$string['event_hvp_summary_answered'] = 'H5P Zusammenfassung beantwortet';
$string['event_hvp_course_presentation_progressed'] = 'H5P Kurse-Fortschritt';
$string['event_hvp_course_presentation_completed'] = 'H5P Kurs-Abschluss';

$string['event_hvp_single_question_answered'] = 'H5P Einzelfrage beantwortet';
$string['event_hvp_quiz_question_answered'] = 'H5P Frage im Quiz beantwortet';
$string['event_hvp_video_question_answered'] = 'H5P Frage im interaktiven Video beantwortet';
$string['event_hvp_video_summary_question_answered'] = 'H5P Frage in der Zusammenfassung des interaktiven Videos beantwortet';
$string['event_hvp_video_summary_answered'] = 'H5P Zusammenfassung des Interactive Video beantwortet';

$string['event_proxy_statements_post'] = 'xAPI Proxy-Statement(s) gesendet';

// Errors.
$string['event_hvp_xapi_error_json'] = 'H5P xAPI Event: Ungültiger JSON-String!';
$string['event_hvp_xapi_error_iri'] = 'H5P xAPI Event: Ungütltige Objekt-IRI!';
$string['event_hvp_xapi_error_unsupported'] = 'H5P xAPI Event: Event nicht unterstützt!';

// Tasks.
$string['sync_task'] = 'TRAX Logs: Logs gesendet';
$string['define_groups_task'] = 'Trax Logs: define groups';
$string['define_courses_task'] = 'Trax Logs: define courses';
