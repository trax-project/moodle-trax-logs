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
 * Trax Logs controller.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\services\statements;
use logstore_trax\src\services\actors;
use logstore_trax\src\services\verbs;
use logstore_trax\src\services\activities;
use logstore_trax\src\services\logs;

require_once(__DIR__ . '/../../vendor/autoload.php');

/**
 * Trax Logs controller.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controller {

    /**
     * Statements service.
     *
     * @var statements $statements
     */
    public $statements;

    /**
     * Actors service.
     *
     * @var actors $actors
     */
    public $actors;

    /**
     * Verbs service.
     *
     * @var verbs $verbs
     */
    public $verbs;

    /**
     * Activities service.
     *
     * @var activities $activities
     */
    public $activities;

    /**
     * Logs.
     *
     * @var logs $logs
     */
    public $logs;


    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->logs = new logs();
        $this->actors = new actors();
        $this->verbs = new verbs();
        $this->activities = new activities();
        $this->statements = new statements($this->actors, $this->verbs, $this->activities, $this->logs);
    }

    /**
     * Process events from Moodle logstore.
     *
     * @return void
     */
    public function process_logstore() {
        $events = $this->logs->get_events_to_process();
        $this->process_events($events);
    }

    /**
     * Process an array of events.
     *
     * @param array $events Moodle events to process.
     * @return void
     */
    public function process_events(array $events) {
        $statements = $this->statements->get_from_events($events);
        (new emitter($this->logs))->send($statements);
    }

    /**
     * Return an HTTP client to communicate with the LRS.
     *
     * @return \logstore_trax\src\client
     */
    public function client() {
        return new client();
    }

}
