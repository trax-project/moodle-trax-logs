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
 * Abstract class to implement an xAPI statement.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\services\actors;
use logstore_trax\src\services\verbs;
use logstore_trax\src\services\activities;
use logstore_trax\src\util;

abstract class statement {

    /**
     * Actors service.
     * 
     * @var actors $actors
     */
    protected $actors;

    /**
     * Verbs service.
     * 
     * @var verbs $verbs
     */
    protected $verbs;

    /**
     * Activities service.
     * 
     * @var activities $activities
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
     * @param actors $actors Actors service
     * @param verbs $verbs Verbs service
     * @param activities $activites Activities service
     */
    public function __construct($event, actors $actors, verbs $verbs, activities $activities) {
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
    protected function baseStatement($activityType, $withSystem = true) {
        return [
            'context' => $this->baseContext($activityType, $withSystem),
            'timestamp' => date('c', $this->event->timecreated),
        ];
    }

    /**
     * Build the context.
     * 
     * @return array
     */
    protected function baseContext($activityType, $withSystem = true) {

        // Categories
        $categories = $this->activities->getCategories($activityType);
        $categories[] = $this->activities->get('profile');

        // Base context
        $res = [
            'platform' => 'Moodle',
            'contextActivities' => [
                'category' => $categories,
            ],
        ];

        // System grouping
        if ($withSystem) {
            $res['contextActivities']['grouping'] = [
                $this->activities->get('system', 0, false)
            ];
        }

        return $res;
    }

}
