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

use logstore_trax\src\utils\hvp_utils;

/**
 * H5P xAPI event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hvp_module_event_triggered extends \core\event\base {

    /**
     * Create instance of event.
     *
     * @param \stdClass $statement
     * @return hvp_xapi_event_triggered
     */
    public static function create_statement(\stdClass $statement) {
        global $DB;

        // Get the course module ID.
        $iri = $statement->object->id;
        $cmid = hvp_utils::module_cmid($iri);

        // Prepare data.
        $cm = $DB->get_record('course_modules', array('id' => $cmid), 'id,instance');
        $data = array(
            'objectid' => $cm->instance,
            'context' => \context_module::instance($cmid),
            'other' => ['statement' => json_encode($statement)]
        );

        // Create Moodle event.
        $event = self::create($data);
        return $event;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name()
    {
        return get_string('event_hvp_module_triggered', 'logstore_trax');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' interacted with the H5P activity with id '$this->contextinstanceid'.";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url()
    {
        return new \moodle_url('/mod/hvp/view.php', array(
            'id' => $this->contextinstanceid
        ));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init()
    {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'hvp';
    }

}
