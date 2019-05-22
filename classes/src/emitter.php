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
 * Statements emitter.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\services\logs;

/**
 * Statements emitter.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emitter {

    /**
     * Config.
     *
     * @var stdClass $config
     */
    protected $config;

    /**
     * Logs.
     *
     * @var logs $logs
     */
    protected $logs;

    /**
     * LRS client.
     *
     * @var client $client
     */
    protected $client;


    /**
     * Constructor.
     *
     * @param stdClass $config Config
     * @param logs $logs logs service
     * @return void
     */
    public function __construct(\stdClass $config, logs $logs) {
        $this->config = $config;
        $this->logs = $logs;

        // HTTP Client.
        $this->client = new client((object)[
            'endpoint' => get_config('logstore_trax', 'lrs_endpoint'),
            'username' => get_config('logstore_trax', 'lrs_username'),
            'password' => get_config('logstore_trax', 'lrs_password'),
        ]);
    }

    /**
     * Send an array of Statements.
     *
     * @param array $items Array of items containing a Statement and a Moodle event.
     * @return void
     */
    public function send(array $items) {
        for ($i = 0; 1; $i++) {

            // Get a batch
            $batch = array_slice($items, $i * $this->config->xapibatchsize, $this->config->xapibatchsize, true);
            if (empty($batch)) break;

            // Get the batch statements
            $statements = array_values(array_map(function ($item) {
                return $item->statement;
            }, $batch));
            
            // Get the batch events
            $events = array_map(function ($item) {
                return $item->event;
            }, $batch);

            // Post the statements
            $resp = $this->client->statements()->post($statements);

            // Manage success and errors
            foreach($events as $event) {
                if ($resp->code == 200) {
                    $this->logs->log_success($event);
                } else {
                    $this->logs->log_lrs_error($event);
                }
            }
        }
    }

}
