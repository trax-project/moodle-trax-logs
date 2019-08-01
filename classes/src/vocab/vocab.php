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
 * Vocab class.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\vocab;

defined('MOODLE_INTERNAL') || die();

/**
 * Vocab class.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class vocab {

    /**
     * Vocab class.
     *
     * @var string $class
     */
    protected $class;

    /**
     * Vocab items.
     *
     * @var array $items
     */
    protected $items = [];


    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->items = json_decode(json_encode($this->items));
    }

    /**
     * Check if an activity type is supported.
     *
     * @param string $key Vocab key
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return bool
     */
    public function supported(string $key, string $plugin = null) {
        return $this->get($key, $plugin) !== false;
    }

    /**
     * Get an activity type info.
     *
     * @param string $key Vocab key
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return \stdClass
     */
    public function get(string $key, string $plugin = null) {

        // Internal vocab first.
        if (isset($this->items->$key)) return $this->items->$key;
        if (!isset($plugin)) return false;

        // Plugin vocab.
        $class = '\\' . $plugin . '\\xapi\\vocab\\' . $this->class;
        if (!class_exists($class)) return false;
        $vocab = new $class();
        return $vocab->get($key);
    }

    /**
     * Access to a vocab item property.
     *
     * @param string $name Property name
     * @param array $args Arguments
     * @return string|false
     */
    public function __call($name, $args) {
        $key = $args[0];
        $plugin = count($args) > 1 ? $args[1] : null;
        $item = $this->get($key, $plugin);
        if (!$item || !isset($item->$name)) return false;
        return $item->$name;
    }

}
