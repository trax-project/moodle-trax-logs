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
 * H5P xAPI event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\event;

defined('MOODLE_INTERNAL') || die();

/**
 * H5P xAPI event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class hvp_event extends \core\event\base {

    /**
     * Create instance of event.
     *
     * @param \stdClass $statement
     * @return hvp_event
     */
    public static function create_statement(\stdClass $statement) {
        global $DB;

        // Get the course module.
        $parts = explode('/', self::get_module_iri($statement));
        $contextid = intval(end($parts));
        $context = $DB->get_record('context', array('id' => $contextid), '*', MUST_EXIST);
        $cm = $DB->get_record('course_modules', array('id' => $context->instanceid), 'id,instance', MUST_EXIST);

        // Event data.
        $data = array(
            'objectid' => $cm->instance,
            'context' => \context_module::instance($cm->id),
            'other' => ['statement' => json_encode($statement)]
        );

        // Create Moodle event.
        $event = self::create($data);
        return $event;
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/h5pactivity/view.php', array(
            'id' => $this->contextinstanceid
        ));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'h5pactivity';
    }

    /**
     * Get the H5P module IRI from the Statement.
     *
     * @param \stdClass $statement
     * @return string
     */
    protected static function get_module_iri(\stdClass $statement) {

        if (isset($statement->context->contextActivities->parent) && !empty($statement->context->contextActivities->parent)) {
            
            // There is a parent.
            $parentid = $statement->context->contextActivities->parent[0]->id;
            $parts = explode('subContentId=', $parentid);
            if (count($parts) == 1) {

                // The parent is the module. Return it.
                return $parts[0];

            } else {

                // The parent is a sub-content. Remove the & or ? char.
                return substr($parts[0], 0, -1);
            }

        } else {

            // No parent. Return the object ID.
            return $statement->object->id;
        }
    }

}
