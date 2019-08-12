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
 * Some util functions.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src;

defined('MOODLE_INTERNAL') || die();

/**
 * Some util functions.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Generate an UUID.
     * Based on code from http://rusticisoftware.github.io/TinCanPHP/
     *
     * @return string
     */
    public static function uuid() {
        $randomstring = openssl_random_pseudo_bytes(16);
        $timelow = bin2hex(substr($randomstring, 0, 4));
        $timemid = bin2hex(substr($randomstring, 4, 2));
        $timehiandversion = bin2hex(substr($randomstring, 6, 2));
        $clockseqhiandreserved = bin2hex(substr($randomstring, 8, 2));
        $node = bin2hex(substr($randomstring, 10, 6));

        // Set the four most significant bits (bits 12 through 15) of the
        // timehiandversion field to the 4-bit version number from
        // Section 4.1.3.
        $timehiandversion = hexdec($timehiandversion);
        $timehiandversion = $timehiandversion >> 4;
        $timehiandversion = $timehiandversion | 0x4000;

        // Set the two most significant bits (bits 6 and 7) of the
        // clockseqhiandreserved to zero and one, respectively.
        $clockseqhiandreserved = hexdec($clockseqhiandreserved);
        $clockseqhiandreserved = $clockseqhiandreserved >> 2;
        $clockseqhiandreserved = $clockseqhiandreserved | 0x8000;

        return sprintf(
            '%08s-%04s-%04x-%04x-%012s',
            $timelow,
            $timemid,
            $timehiandversion,
            $clockseqhiandreserved,
            $node
        );
    }

    /**
     * Generate a timestamp.
     *
     * @param int $time Time in milliseconds
     * @return string
     */
    public static function timestamp($time) {
        return date('c', $time);
    }

    /**
     * Format a lang string.
     *
     * @param string $text Text to format
     * @param \stdClass $course Course or course ID
     * @return array
     */
    public static function lang_string(string $text, \stdClass $course = null) {
        global $CFG;
        $lang = !isset($course) || empty($course->lang) ? $CFG->lang : $course->lang;
        $lang = explode('_', $lang)[0];
        $text = strip_tags($text);
        return [$lang => $text];
    }

    /**
     * Convert second to ISO duration.
     * 
     * @param int $seconds duration in seconds
     */
    public static function iso8601_duration($seconds) {
        $intervals = array('D' => 60 * 60 * 24, 'H' => 60 * 60, 'M' => 60, 'S' => 1);

        $pt = 'PT';
        $result = '';
        foreach ($intervals as $tag => $divisor) {
            $qty = floor($seconds / $divisor);
            if (!$qty && $result == '') {
                continue;
            }

            $seconds -= $qty * $divisor;
            $result  .= "$qty$tag";
        }
        if ($result == '')
            $result = '0S';
        return "$pt$result";
    }


}
