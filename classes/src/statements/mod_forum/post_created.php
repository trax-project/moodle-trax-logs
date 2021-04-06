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
 * xAPI transformation of a SCORM event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements\mod_forum;

defined('MOODLE_INTERNAL') || die();

use logstore_trax\src\statements\base_statement;
use logstore_trax\src\utils\inside_module_context;
use logstore_trax\src\utils as logstore_utils;

/**
 * xAPI transformation of a Forum event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_created extends base_statement {

    use inside_module_context {
        base_context as native_base_context;
    }

    /**
     * Build the Statement.
     *
     * @return array
     */
    protected function statement() {

        return array_replace($this->base('forum'), [
            'actor' => $this->actors->get('user', $this->event->userid),
            'verb' => $this->verbs->get('created'),
            'object' => $this->statement_object(),
        ]);

    }

    /**
     * Get the object.
     *
     * @return array
     */
    protected function statement_object() {
        global $DB;
        $course = $DB->get_record('course', ['id' => $this->event->courseid], '*', MUST_EXIST);
        $post = $DB->get_record($this->event->objecttable, ['id' => $this->event->objectid], '*', MUST_EXIST);
        $module = $this->activities->get('forum', $this->eventother->forumid, true, 'module');

        return [
            'objectType' => 'Activity',
            'id' => $module['id'] . '/discussion/' . $this->eventother->discussionid . '/post/' . $post->id,
            'definition' => [
                'type' => $this->activities->types->type('forum-reply'),
                'name' => logstore_utils::lang_string($post->subject, $course),
                'description' => logstore_utils::lang_string($post->message, $course)
            ]
        ];
    }

    /**
     * Build the context.
     *
     * @param string $activitytype Type of activity
     * @param bool $withsystem Include the system activity in the context?
     * @param string $vocabtype Type of activity
     * @param string $plugin Plugin where the implementation is located (ex. mod_forum)
     * @return array
     */
    protected function base_context($activitytype, $withsystem, $vocabtype, $plugin = null) {
        $context = $this->native_base_context($activitytype, $withsystem, $vocabtype, $plugin);

        // Move the parent to grouping.
        $context['contextActivities']['grouping'][] = $context['contextActivities']['parent'][0];

        // Set the parent.
        $module = $this->activities->get('forum', $this->eventother->forumid, true, 'module');
        $context['contextActivities']['parent'] = [[
            'objectType' => 'Activity',
            'id' => $module['id'] . '/discussion/' . $this->eventother->discussionid,
            'definition' => [
                'type' => $this->activities->types->type('forum-topic')
            ]
        ]];

        return $context;
    }
}
