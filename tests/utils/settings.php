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
 * Configuration for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Configuration for unit tests.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait settings {

    /**
     * LRS endpoint.
     *
     * @var string $lrsendpoint
     */
    protected $lrsendpoint = 'http://starterdev.test/trax/api/9aed4e7f-e077-46cb-b49f-da3ab3a5d472/xapi/std';

    /**
     * Basic HTTP username.
     *
     * @var string $lrsusername
     */
    protected $lrsusername = 'moodle';

    /**
     * Basic HTTP password.
     *
     * @var string $lrspassword
     */
    protected $lrspassword = 'aaaaaaaa';

    /**
     * Platform IRI.
     *
     * @var string $platformiri
     */
    protected $platformiri = 'http://moodle39.test';

}
