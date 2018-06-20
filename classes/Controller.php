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

namespace logstore_trax;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../vendor/autoload.php');

use logstore_trax\Statements;
use logstore_trax\Client;

class Controller {

    /**
     * Statements repository.
     * 
     * @var Statements $statements
     */
    protected $statements;

    /**
     * LRS client.
     * 
     * @var Client $client
     */
    protected $client;


    /**
     * Constructs a new controller.
     */
    public function __construct() {
        $this->statements = new Statements((object)[
            'platform_iri' => get_config('logstore_trax', 'platform_iri'),
        ]);
        $this->client = new Client((object)[
            'endpoint' => get_config('logstore_trax', 'lrs_endpoint'),
            'username' => get_config('logstore_trax', 'lrs_username'),
            'password' => get_config('logstore_trax', 'lrs_password'),
        ]);
    }

    /**
     * Process an array of events data.
     *
     * @param array $events Moodle events data
     */
    public function process_events(array $events) {
        $statements = $this->statements($events);
        $this->client->statements()->post($statements);
    }

    /**
     * Transform Moodle events data into Statements array.
     *
     * @param array $events Moodle events data
     * @return array
     */
    protected function statements(array $events) {
        return array_filter(array_map(function($event) {
            return $this->statements->get($event);
        }, $events));
    }


}
