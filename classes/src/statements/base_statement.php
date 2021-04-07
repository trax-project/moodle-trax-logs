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

/**
 * Abstract class to implement an xAPI statement.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_statement {

    use consumer_statement;

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
     * Platform.
     *
     * @var string $platform
     */
    protected $platform = 'Moodle';

    /**
     * Moodle event data.
     *
     * @var array $event
     */
    protected $event;

    /**
     * Moodle event other data.
     *
     * @var \stdClass $eventother
     */
    protected $eventother;

    /**
     * Plugin.
     *
     * @var string $plugin
     */
    protected $plugin;

    /**
     * Activity type.
     *
     * @var string $activitytype
     */
    protected $activitytype;


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
        // Some Moodle events have a textual 'null' value on 'other'!
        if (!is_null($this->event->other) && $this->event->other != 'null') {
            if (!$this->eventother = json_decode($this->event->other)) {
                $this->eventother = (object)unserialize($this->event->other);
            }
        }
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
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    protected function base($activitytype, $withsystem = true, $vocabtype = null, $plugin = null) {
        if (!isset($vocabtype)) {
            $vocabtype = $activitytype;
        }
        return [
            'context' => $this->base_context($activitytype, $withsystem, $vocabtype, $plugin),
            'timestamp' => date('c', $this->event->timecreated),
        ];
    }

    /**
     * Build the context.
     *
     * @param string $activitytype Type of activity
     * @param bool $withsystem Include the system activity in the context?
     * @param string $vocabtype Type of activity
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    protected function base_context($activitytype, $withsystem, $vocabtype, $plugin = null) {

        // Categories.
        $categories = $this->activities->get_categories($vocabtype, $plugin);
        $categories[] = $this->activities->get('profile');

        // Base context.
        $res = [
            'contextActivities' => [
                'category' => $categories,
            ],
            'extensions' => [
                'http://vocab.xapi.fr/extensions/platform-event' => $this->event->eventname
            ]
        ];
        if (!empty($this->platform)) {
            $res['platform'] = $this->platform;
        }

        // System grouping.
        if ($withsystem) {

            // Moodle instance.
            $res['contextActivities']['grouping'] = [
                $this->activities->get('system', 0, false)
            ];

        }

        // LTI client.
        if ($consumer = $this->consumer($this->event->userid)) {

            if (!isset($res['contextActivities']['grouping'])) {
                $res['contextActivities']['grouping'] = [];
            }
            $res['contextActivities']['grouping'][] = $consumer;
        }

        return $res;
    }

}
