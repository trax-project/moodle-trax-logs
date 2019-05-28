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
use logstore_trax\src\utils;

/**
 * Abstract class to implement an xAPI statement.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_statement {

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
     * Moodle event other data.
     *
     * @var array $eventother
     */
    protected $eventother;


    /**
     * Constructor.
     *
     * @param stdClass $event Moodle event data
     * @param actors $actors Actors service
     * @param verbs $verbs Verbs service
     * @param activities $activities Activities service
     * @return void
     */
    public function __construct($event, actors $actors, verbs $verbs, activities $activities) {
        $this->event = $event;
        $this->eventother = (object)unserialize($this->event->other);
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
     * @param string $activitytype Type of activity
     * @param bool $withsystem Include the system activity in the context?
     * @param string $vocabtype Type of activity
     * @return array
     */
    protected function base($activitytype, $withsystem = true, $vocabtype = null) {
        if (!isset($vocabtype)) {
            $vocabtype = $activitytype;
        }
        return [
            'context' => $this->base_context($activitytype, $withsystem, $vocabtype),
            'timestamp' => date('c', $this->event->timecreated),
        ];
    }

    /**
     * Build the context.
     *
     * @param string $activitytype Type of activity
     * @param bool $withsystem Include the system activity in the context?
     * @param string $vocabtype Type of activity
     * @return array
     */
    protected function base_context($activitytype, $withsystem, $vocabtype) {

        // Categories.
        $categories = $this->activities->get_categories($vocabtype);
        $categories[] = $this->activities->get('profile');

        // Base context.
        $res = [
            'platform' => 'Moodle',
            'contextActivities' => [
                'category' => $categories,
            ],
        ];

        // System grouping.
        if ($withsystem) {
            $res['contextActivities']['grouping'] = [
                $this->activities->get('system', 0, false)
            ];
        }

        return $res;
    }

}
