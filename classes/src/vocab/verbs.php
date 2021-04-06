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
 * Verbs vocab.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\vocab;

defined('MOODLE_INTERNAL') || die();

/**
 * Verbs vocab.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verbs extends vocab {

    /**
     * Vocab class.
     *
     * @var string $class
     */
    protected $class = 'verbs';

    /**
     * Vocab items.
     *
     * @var array $items
     */
    protected $items = [
        'logged-in' => [
            'iri' => 'https://w3id.org/xapi/adl/verbs/logged-in',
        ],
        'logged-out' => [
            'iri' => 'https://w3id.org/xapi/adl/verbs/logged-out',
        ],
        'navigated-in' => [
            'iri' => 'http://vocab.xapi.fr/verbs/navigated-in',
        ],
        'completed' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/completed',
        ],
        'passed' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/passed',
        ],
        'failed' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/failed',
        ],
        'scored' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/scored',
        ],
        'graded' => [
            'iri' => 'http://vocab.xapi.fr/verbs/graded',
        ],
        'answered' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/answered',
        ],
        'launched' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/launched',
        ],
        'initialized' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/initialized',
        ],
        'terminated' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/terminated',
        ],
        'reset' => [
            'iri' => 'http://vocab.xapi.fr/verbs/reset',
        ],
        'marked-completion' => [
            'iri' => 'http://vocab.xapi.fr/verbs/marked-completion',
        ],
        'registered' => [
            'iri' => 'http://adlnet.gov/expapi/verbs/registered',
        ],
        'defined' => [
            'iri' => 'http://id.tincanapi.com/verb/defined',
        ],
        'viewed' => [
            'iri' => 'http://id.tincanapi.com/verb/viewed',
        ],
        'created' => [
            'iri' => 'https://w3id.org/xapi/dod-isd/verbs/created',
        ],
    ];

}
