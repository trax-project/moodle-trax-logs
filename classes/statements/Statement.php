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

namespace logstore_trax\statements;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\Actors;
use logstore_trax\Verbs;
use logstore_trax\Activities;
use logstore_trax\Util;

abstract class Statement {

    /**
     * Actors index.
     * 
     * @var Actors $actors
     */
    protected $actors;

    /**
     * Verbs index.
     * 
     * @var Verbs $verbs
     */
    protected $verbs;

    /**
     * Activities index.
     * 
     * @var Activities $activities
     */
    protected $activities;

    /**
     * Moodle event data.
     * 
     * @var array $event
     */
    protected $event;


    /**
     * Constructs a new statement.
     *
     * @param stdClass $event Moodle event data
     * @param Actors $actors Actors index
     * @param Verbs $verbs Verbs index
     * @param Activities $activites Activities index
     */
    public function __construct($event, Actors $actors, Verbs $verbs, Activities $activities) {
        $this->event = $event;
        $this->actors = $actors;
        $this->verbs = $verbs;
        $this->activities = $activities;
    }

    /**
     * Get the Statement value.
     * 
     * @return array
     */
    public function get() {
        return $this->statement();
    }

    /**
     * Build the Statement.
     * 
     * @return array
     */
    abstract protected function statement();

    /**
     * Build the base Statement.
     * 
     * @return array
     */
    protected function baseStatement($activityType) {
        return [
            'context' => $this->baseContext($activityType),
            'timestamp' => date('c', $this->event->timecreated),
        ];
    }

    /**
     * Build the context.
     * 
     * @return array
     */
    protected function baseContext($activityType) {

        // Categories
        $categories = $this->activities->getCategories($activityType);
        $categories[] = $this->activities->get('profile');

        // Context
        return [
            'platform' => 'Moodle',
            'contextActivities' => [
                'grouping' => [
                    $this->activities->get('system', 0, false)
                ],
                'category' => $categories,
            ],
        ];
    }

}
