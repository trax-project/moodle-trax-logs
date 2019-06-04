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
class hvp_event extends \core\event\base {

    /**
     * Supported H5P types.
     *
     * @var array
     */
    protected static $supported = [

        // Questions.
        'H5P.DragQuestion',
        'H5P.Blanks',
        'H5P.MarkTheWords',
        'H5P.DragText',
        'H5P.TrueFalse',
        'H5P.MultiChoice',

        // Quiz.
        'H5P.SingleChoiceSet',
        'H5P.QuestionSet',
    ];


    /**
     * Create instance of event.
     *
     * @param \stdClass $statement
     * @return hvp_event
     */
    public static function create_statement(\stdClass $statement) {
        global $DB;

        // Check if the event is supported.
        $h5ptype = self::hvp_type($statement);

        // Get the course module ID.
        $parts = explode('mod/hvp/view.php?id=', self::get_module_iri($statement));
        if (count($parts) < 2 || !$cmid = intval($parts[1])) {
            print_error('event_hvp_xapi_error_iri', 'logstore_trax');
        }

        // Prepare data.
        $cm = $DB->get_record('course_modules', array('id' => $cmid), 'id,instance', MUST_EXIST);
        $data = array(
            'objectid' => $cm->instance,
            'context' => \context_module::instance($cmid),
            'other' => ['statement' => json_encode($statement), 'hvptype' => $h5ptype]
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
        return new \moodle_url('/mod/hvp/view.php', array(
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
        $this->data['objecttable'] = 'hvp';
    }

    /**
     * Check if the H5P library type is supported and return it.
     *
     * @param \stdClass $statement
     * @return string
     */
    protected static function hvp_type(\stdClass $statement) {
        if (!isset($statement->context->contextActivities->category)) {

            // H5P.SingleChoiceSet has currently no context category and seems to be the only one.
            return 'H5P.SingleChoiceSet';

        } else {

            // Check category.
            $category = $statement->context->contextActivities->category[0]->id;
            foreach (self::$supported as $type) {
                if (strpos($category, $type) !== false) {
                    return $type;
                }
            }
        }
        print_error('event_hvp_xapi_error_unsupported', 'logstore_trax');
    }

    /**
     * Get the H5P module IRI from the Statement.
     *
     * @param \stdClass $statement
     * @return string
     */
    protected static function get_module_iri(\stdClass $statement) {
        return $statement->object->id;
    }

}
