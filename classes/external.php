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

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use \logstore_trax\Controller;

class logstore_trax_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_activities_parameters()
    {
        return self::get_parameters('activity');
    }

    /**
     * Get the xAPI data.
     *
     * @param array $items requested items 
     * @return array of items with a new xapi property on each item
     */
    public static function get_activities(array $items)
    {
        return self::get($items, 'activity');
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_activities_returns()
    {
        return self::get_returns('activity');
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_actors_parameters()
    {
        return self::get_parameters('actor');
    }

    /**
     * Get the xAPI data.
     *
     * @param array $items requested items 
     * @return array of items with a new xapi property on each item
     */
    public static function get_actors(array $items)
    {
        return self::get($items, 'actor');
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_actors_returns()
    {
        return self::get_returns('actor');
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    protected static function get_parameters($name)
    {
        return new external_function_parameters(
            array(
                'items' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'type' => new external_value(PARAM_ALPHA, 'Moodle internal type of the '. $name),
                            'id' => new external_value(PARAM_INT, 'Moodle internal ID of the '. $name)
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
     * @return array of items with a new xapi property on each item
     */
    protected static function get(array $items, $name)
    {
        $controller = new Controller();
        return array_map(function ($item) use ($controller, $name) {
            $item['xapi'] = $controller->$name($item['type'], $item['id']);
            $item['xapi'] = json_encode($item['xapi']);
            return $item;
        }, $items);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    protected static function get_returns($name)
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'type' => new external_value(PARAM_ALPHA, 'Moodle internal type of the '. $name),
                    'id' => new external_value(PARAM_INT, 'Moodle internal ID of the '. $name),
                    'xapi' => new external_value(PARAM_RAW, 'The xAPI JSON string of the '. $name)
                )
            )
        );
    }

}
