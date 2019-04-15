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
 * Standard functions to implement the external API (web services).
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use \logstore_trax\src\controller as trax_controller;

/**
 * Standard functions to implement the external API (web services).
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logstore_trax_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_activities_parameters() {
        return self::get_parameters('activities');
    }

    /**
     * Get the xAPI data.
     *
     * @param array $items requested items
     * @return array of items with a new xapi property on each item
     */
    public static function get_activities(array $items) {
        return self::get($items, 'activities');
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_activities_returns() {
        return self::get_returns('activities');
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_actors_parameters() {
        return self::get_parameters('actors');
    }

    /**
     * Get the xAPI data.
     *
     * @param array $items requested items
     * @return array of items with a new xapi property on each item
     */
    public static function get_actors(array $items) {
        return self::get($items, 'actors');
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_actors_returns() {
        return self::get_returns('actors');
    }

    /**
     * Returns description of method parameters.
     *
     * @param string $service name of the service to be called
     * @return external_function_parameters
     */
    protected static function get_parameters(string $service) {
        return new external_function_parameters(
            array(
                'items' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'type' => new external_value(PARAM_ALPHA, 'Moodle internal type of the '. $service),
                            'id' => new external_value(PARAM_INT, 'Moodle internal ID of the '. $service)
                        )
                    )
                )
            )
        );
    }

    /**
     * Get the xAPI data.
     *
     * @param array $items requested items
     * @param string $service name of the service to be called
     * @return array of items with a new xapi property on each item
     */
    protected static function get(array $items, string $service) {
        $controller = new trax_controller();
        return array_map(function ($item) use ($controller, $service) {
            $item['xapi'] = $controller->$service->get_existing($item['type'], $item['id'], false);
            $item['xapi'] = json_encode($item['xapi']);
            return $item;
        }, $items);
    }

    /**
     * Returns description of method result value
     *
     * @param string $service name of the service to be called
     * @return external_description
     */
    protected static function get_returns(string $service) {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'type' => new external_value(PARAM_ALPHA, 'Moodle internal type of the '. $service),
                    'id' => new external_value(PARAM_INT, 'Moodle internal ID of the '. $service),
                    'xapi' => new external_value(PARAM_RAW, 'The xAPI JSON string of the '. $service)
                )
            )
        );
    }

}
