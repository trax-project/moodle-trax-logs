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
 * @copyright  2018 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax;

defined('MOODLE_INTERNAL') || die();

trait ActivityTypes {

    /**
     * Types of activity.
     * 
     * @var array $types
     */
    protected $types = [
        
        'profile' => [
            'db' => 0, 
            'type' => 'http://adlnet.gov/expapi/activities/profile',
        ],
        
        // Structural concepts

        'system' => [
            'db' => 1, 
            'type' => 'http://vocab.xapi.fr/activities/system',
        ],
        'course' => [
            'db' => 3, 
            'type' => 'http://vocab.xapi.fr/activities/course',
        ],
        
        // Modules

        'assign' => [
            'db' => 101, 
            'type' => 'http://vocab.xapi.fr/activities/assignment',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'book' => [
            'db' => 102, 
            'type' => 'http://vocab.xapi.fr/activities/web-content',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'chat' => [
            'db' => 103, 
            'type' => 'http://vocab.xapi.fr/activities/chat-room',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'choice' => [
            'db' => 104, 
            'type' => 'http://vocab.xapi.fr/activities/poll',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'data' => [
            'db' => 105, 
            'type' => 'http://vocab.xapi.fr/activities/web-content',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'feedback' => [
            'db' => 106, 
            'type' => 'http://vocab.xapi.fr/activities/survey',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'folder' => [
            'db' => 107, 
            'type' => 'http://vocab.xapi.fr/activities/file-collection',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'forum' => [
            'db' => 108, 
            'type' => 'http://vocab.xapi.fr/activities/forum',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'glossary' => [
            'db' => 109, 
            'type' => 'http://vocab.xapi.fr/activities/glossary',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'imscp' => [
            'db' => 110, 
            'type' => 'http://vocab.xapi.fr/activities/content-package',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'lesson' => [
            'db' => 111, 
            'type' => 'http://vocab.xapi.fr/activities/web-content',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'lti' => [
            'db' => 112, 
            'type' => 'http://vocab.xapi.fr/activities/external-activity',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'page' => [
            'db' => 113, 
            'type' => 'http://vocab.xapi.fr/activities/web-page',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'quiz' => [
            'db' => 114, 
            'type' => 'http://vocab.xapi.fr/activities/quiz',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'resource' => [
            'db' => 115, 
            'type' => 'http://vocab.xapi.fr/activities/file',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'scorm' => [
            'db' => 116, 
            'type' => 'http://vocab.xapi.fr/activities/content-package',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'survey' => [
            'db' => 117, 
            'type' => 'http://vocab.xapi.fr/activities/survey',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'url' => [
            'db' => 118, 
            'type' => 'http://vocab.xapi.fr/activities/web-link',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'wiki' => [
            'db' => 119, 
            'type' => 'http://vocab.xapi.fr/activities/wiki',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],
        'workshop' => [
            'db' => 120, 
            'type' => 'http://vocab.xapi.fr/activities/workshop',
            'level' => 'http://vocab.xapi.fr/categories/learning-unit',
        ],

    ];


}
