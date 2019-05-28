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
 * xAPI transformation of a H5P event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements\logstore_trax;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\utils;

/**
 * xAPI transformation of a H5P event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hvp_single_question_answered extends hvp_quiz_question_answered {

    /**
     * Vocab type of the H5P activity.
     *
     * @var string $vocabtype
     */
    protected $vocabtype = 'hvp-poll';


    /**
     * Transform the H5P object.
     *
     * @param \stdClass $nativeobject H5P object
     * @param array $base Statement base
     * @return \stdClass
     */
    protected function transform_object($nativeobject, $base) {

        // Change ID.
        $nativeobject->id = $base['context']['contextActivities']['parent'][0]['id'] . '/question';

        // Adapt name and description.
        $this->transform_object_strings($nativeobject, $base);

        // Remove extensions.
        unset($nativeobject->definition->extensions);

        return $nativeobject;
    }


}
