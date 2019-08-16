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
 * Verbs service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\services;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\vocab\verbs as verbs_vocab;

/**
 * Verbs service.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verbs {

    /**
     * Vocab: verbs.
     *
     * @var verbs_vocab $verbs
     */
    protected $verbs;


    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->verbs = new verbs_vocab();
    }

    /**
     * Get a verb, given its code.
     *
     * @param string $code Code of the verb
     * @param string $plugin Plugin where the vocab is located (ex. mod_forum)
     * @return array
     */
    public function get(string $code, string $plugin = null) {
        return [
            'id' => $this->verbs->iri($code, $plugin),
        ];
    }

    /**
     * Get a verb IRI, given its code.
     *
     * @param string $code Code of the verb
     * @param string $plugin Plugin where the vocab is located (ex. mod_forum)
     * @return array
     */
    public function iri(string $code, string $plugin = null) {
        return $this->verbs->iri($code, $plugin);
    }

}
