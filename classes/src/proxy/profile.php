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
 * Proxy profile.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\proxy;

use logstore_trax\src\services\actors;
use logstore_trax\src\services\verbs;
use logstore_trax\src\services\activities;
use logstore_trax\src\statements\consumer_statement;

defined('MOODLE_INTERNAL') || die();

/**
 * Proxy profile.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class profile {

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
     * The activity type.
     *
     * @var string $activitytype
     */
    protected $activitytype;

    /**
     * The activity.
     *
     * @var stdClass $activity
     */
    protected $activity;

    /**
     * The course module.
     *
     * @var stdClass $cm
     */
    protected $cm;

    /**
     * The course.
     *
     * @var stdClass $course
     */
    protected $course;

    /**
     * The module context.
     *
     * @var context_module $context
     */
    protected $context;

    /**
     * The user id.
     *
     * @var int $userid
     */
    protected $userid;


    /**
     * Construct.
     *
     * @param string $activitytype Module type
     * @param actors $actors Actors service
     * @param verbs $verbs Verbs service
     * @param activities $activities Activities service
     */
    public function __construct(string $activitytype, actors $actors, verbs $verbs, activities $activities) {
        $this->activitytype = $activitytype;
        $this->actors = $actors;
        $this->verbs = $verbs;
        $this->activities = $activities;
    }

    /**
     * Get transformed statements.
     *
     * @param stdClass|array $data Input data
     * @param int $userid User ID
     * @param stdClass $course
     * @param stdClass $cm
     * @param stdClass $activity
     * @param context_module $contextmodule
     * @return array
     */
    public function get($data, $userid, $course, $cm, $activity, $context) {
        $this->userid = $userid;
        $this->activity = $activity;
        $this->cm = $cm;
        $this->course = $course;
        $this->context = $context;

        // Transform data.
        if (is_array($data)) {
            foreach ($data as &$statement) {
                $this->_transform($statement);
            }
        } else {
            $this->_transform($data);
        }

        return json_decode(json_encode($data), true);
    }

    /**
     * Transform a statement (all).
     *
     * @param \stdClass $statement Statement to transform
     * @return void
     */
    private function _transform(&$statement) {

        // Force the actor.
        $statement->actor = $this->actors->get('user', $this->userid);

        // Remove verb display.
        if (isset($statement->verb->display)) {
            unset($statement->verb->display);
        }

        // Set context->platform.
        $statement->context->platform = 'Moodle';

        // Add grouping activities.
        $statement->context->contextActivities->grouping = [
            $this->activities->get('system', 0, false),
            $this->activities->get('course', $this->course->id, false),
        ];

        // LTI client.
        if ($consumer = $this->consumer($this->userid)) {
            $statement->context->contextActivities->grouping[] = $consumer;
        }
        
        // Add VLE profile.
        $statement->context->contextActivities->category[] = [
            'id' => 'http://vocab.xapi.fr/categories/vle-profile',
            'definition' => ['type' => 'http://adlnet.gov/expapi/activities/profile'],
        ];
        
        // Add Moodle module profile.
        $statement->context->contextActivities->category[] = [
            'id' => 'http://vocab.xapi.fr/categories/moodle/' . $this->activitytype,
            'definition' => ['type' => 'http://adlnet.gov/expapi/activities/profile'],
        ];
        
        // Add granularity level category.
        $statement->context->contextActivities->category[] = [
            'id' => 'http://vocab.xapi.fr/categories/inside-learning-unit',
            'definition' => ['type' => 'http://vocab.xapi.fr/activities/granularity-level'],
        ];
        
        // Keep the object format.
        $statement = json_decode(json_encode($statement));

        // Transform hook.
        $this->transform($statement);
    }

    /**
     * Transform a statement (hook).
     *
     * @param \stdClass $statement Statement to transform
     * @return void
     */
    protected abstract function transform(&$statement);

}
