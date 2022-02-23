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
use logstore_trax\src\config;

/**
 * Statements emitter.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emitter {

    /**
     * Logs.
     *
     * @var logs $logs
     */
    protected $logs;


    /**
     * Constructor.
     *
     * @param logs $logs logs service
     * @return void
     */
    public function __construct(logs $logs) {
        $this->logs = $logs;
    }

    /**
     * Send an array of Statements + Events.
     *
     * @param array $items Array of items containing a Statement and a Moodle event.
     * @return void
     */
    public function send(array $items) {

        // Split the items in 2 groups: one per target.
        $targetedItems = [
            config::TARGET_MAIN => [],
            config::TARGET_SECONDARY => []
        ];
        foreach ($items as $item) {
            $targetedItems[$this->logs->target($item->event)][] = $item;
        }
        
        // Send them separatly.
        $this->sendToTarget($targetedItems[config::TARGET_MAIN], config::TARGET_MAIN);
        $this->sendToTarget($targetedItems[config::TARGET_SECONDARY], config::TARGET_SECONDARY);
    }

    /**
     * Send an array of Statements + Events.
     *
     * @param array $items Array of items containing a Statement and a Moodle event.
     * @param int $target
     * @return void
     */
    public function sendToTarget(array $items, int $target) {
        $config = get_config('logstore_trax');
        $client = new client($target);
        for ($i = 0; 1; $i++) {

            // Get a batch
            $batch = array_slice($items, $i * $config->xapi_batch_size, $config->xapi_batch_size, true);
            if (empty($batch)) break;

            // Get the batch statements
            $statements = array_map(function ($item) {
                return $item->statements;
            }, $batch);
            $statements = call_user_func_array('array_merge', $statements);
            $statements = array_values($statements);
            
            // Get the batch events
            $events = array_map(function ($item) {
                return $item->event;
            }, $batch);

            // Post the statements
            $resp = $client->statements()->post($statements);

            // Manage success and errors
            foreach ($events as $event) {
                if ($resp->code == 200) {
                    $this->logs->log_success($event);
                } else {
                    $this->logs->log_lrs_error($event);
                }
            }
        }
    }
}
