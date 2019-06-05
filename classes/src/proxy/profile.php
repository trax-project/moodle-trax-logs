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
use logstore_trax\src\services\activities;

defined('MOODLE_INTERNAL') || die();

/**
 * Proxy profile.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class profile {

    /**
     * Actors service.
     *
     * @var actors $actors
     */
    protected $actors;

    /**
     * Activities service.
     *
     * @var activities $activities
     */
    protected $activities;


    /**
     * Construct.
     *
     * @param actors $actors Actors service
     * @param activities $activities Activities service
     */
    public function __construct(actors $actors, activities $activities) {
        $this->actors = $actors;
        $this->activities = $activities;
    }

    /**
     * Get transformed statements.
     *
     * @return array
     */
    public function get($data) {
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
        global $USER;

        // Force the actor.
        $statement->actor = $this->actors->get('user', $USER->id);

        // Remove verb display.
        if (isset($statement->verb->display)) {
            unset($statement->verb->display);
        }

        // Set context->platform.
        $statement->context->platform = 'Moodle';
        
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
