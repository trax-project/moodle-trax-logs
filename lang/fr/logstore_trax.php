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
 * @copyright  2018 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin

$string['pluginname'] = 'Trax Logs';
$string['pluginname_desc'] = "Un plugin de type qui transforme les logs Moodle en traces xAPI, et les envoie à votre LRS.";


// Settings

$string['lrs_endpoint'] = 'Endpoint du LRS';
$string['lrs_endpoint_help'] = "Il s'agit de l'URL utilisée pour accéder aux services xAPI de votre LRS.";

$string['lrs_username'] = "Nom d'utilisateur (Basic HTTP)";
$string['lrs_username_help'] = "Il s'agit du nom d'utilisateur du compte Basic HTTP créé dans votre LRS.";

$string['lrs_password'] = 'Mot de passe (Basic HTTP)';
$string['lrs_password_help'] = "Il s'agit du mot de passe du compte Basic HTTP créé dans votre LRS.";

$string['platform_iri'] = 'IRI de la plateforme';
$string['platform_iri_help'] = "Il s'agit d'une IRI qui identifie votre plateforme de façon définitive.";

$string['buffersize'] = 'Taille du tampon';
$string['buffersize_help'] = "Nombre d'événements qui peuvent être mis en attente afin d'être transmis en une seule requête.";


// Privacy metadata

$string['privacy:metadata:actors'] = "Table de correspondance entre identifiant d'utilisateur interne à Moodle et identifiant anonyme utilisé dans le LRS";
$string['privacy:metadata:actors:mid'] = "Identifiant d'utilisateur interne à Moodle";
$string['privacy:metadata:actors:uuid'] = "Identifiant anonyme utilisé par le LRS";

$string['privacy:metadata:lrs'] = 'Les logs générés pas les utilisateurs sont envoyés à un LRS externe qui les stocke dans sa propre base de données.';
$string['privacy:metadata:lrs:uuid'] = 'Identifiant anonyme envoyé au LRS';
