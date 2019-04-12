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

namespace logstore_trax;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../vendor/autoload.php');

class Controller {

    /**
     * Statements repository.
     * 
     * @var Statements $statements
     */
    protected $statements;

    /**
     * Actors index.
     * 
     * @var Actors $actors
     */
    protected $actors;

    /**
     * Verbs index.
     * 
     * @var Verbs $actors
     */
    protected $verbs;

    /**
     * Activities index.
     * 
     * @var Activities $activities
     */
    protected $activities;

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

        // APIs
        $config = (object)['platform_iri' => get_config('logstore_trax', 'platform_iri')];
        $this->actors = new Actors($config);
        $this->verbs = new Verbs($config);
        $this->activities = new Activities($config);
        $this->statements = new Statements($this->actors, $this->verbs, $this->activities);

        // HTTP Client
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
        $statements = $this->statements->getFromEvents($events);
        $this->client->statements()->post($statements);
    }

    /**
     * Get an existing actor, given a Moodle ID and an actor type.
     * 
     * @param string $type Type of actor
     * @param int $mid Moodle ID of the actor
     * @return array
     */
    public function actor(string $type, int $mid = 0)
    {
        return $this->actors->getExisting($type, $mid, false);
    }

    /**
     * Get an existing activity, given a Moodle ID and an activity type.
     * 
     * @param string $type Type of activity
     * @param int $mid Moodle ID of the activity
     * @return array
     */
    public function activity(string $type, int $mid = 0)
    {
        return $this->activities->getExisting($type, $mid, false);
    }


}
