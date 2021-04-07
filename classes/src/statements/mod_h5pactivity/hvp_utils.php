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
 * xAPI transformation of a Moodle event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src\statements\mod_h5pactivity;

defined('MOODLE_INTERNAL') || die();

/**
 * xAPI transformation of a Moodle event.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait hvp_utils {

    /**
     * Return the H5P module vocab type.
     *
     * @param string $hvptype HVP type
     * @return string
     */
    protected function vocab_type(string $hvptype) {
        switch ($hvptype) {

            // Questions.
            case 'H5P.DragQuestion':
            case 'H5P.Blanks':
            case 'H5P.MarkTheWords':
            case 'H5P.DragText':
            case 'H5P.TrueFalse':
            case 'H5P.MultiChoice':
                return 'h5pactivity-single-question';

            // Quiz.
            case 'H5P.SingleChoiceSet':
            case 'H5P.QuestionSet':
                return 'h5pactivity-quiz';

            // Summary.
            case 'H5P.Summary':
                return 'h5pactivity-summary';

            // Interactive Video.
            case 'H5P.InteractiveVideo':
                return 'h5pactivity-interactive-video';

            // Course Presentation.
            case 'H5P.CoursePresentation':
                return 'h5pactivity-course-presentation';

            // Column.
            case 'H5P.Column':
                return 'h5pactivity-column';
        }
    }

    /**
     * Return the H5P module vocab type.
     *
     * @param \stdClass $hvp HVP module record
     * @return string
     */
    protected function module_vocab_type(\stdClass $hvp) {
        global $DB;
        $module = $DB->get_record('modules', array('name' => 'h5pactivity'), '*', MUST_EXIST);
        $cm = $DB->get_record('course_modules', array('module' => $module->id, 'instance' => $hvp->id), '*', MUST_EXIST);
        $context = $DB->get_record('context', array('contextlevel' => 70, 'instanceid' => $cm->id), '*', MUST_EXIST);
        $files = $DB->get_records('files', array('contextid' => $context->id));
        $file = array_shift($files);
        $h5p = $DB->get_record('h5p', array('pathnamehash' => $file->pathnamehash, 'contenthash' => $file->contenthash), '*', MUST_EXIST);
        $library = $DB->get_record('h5p_libraries', array('id' => $h5p->mainlibraryid), '*', MUST_EXIST);
        return $this->vocab_type($library->machinename);
    }

    /**
     * Get the Statement level, and the parent UUID if level 3.
     * 1 - H5P module
     * 2 - H5P module direct child
     * 3 - H5P module deep child
     *
     * @param \stdClass $statement Statement
     * @return array
     */
    protected function statement_level($statement) {

        $hasparent = isset($statement->context->contextActivities->parent) && !empty($statement->context->contextActivities->parent);

        // Level 1.
        if (!$hasparent) {
            return [1, null, null];
        }

        // Object UUID.
        $objectuuid = explode('subContentId=', $statement->object->id)[1];

        // Parent data.
        $parentid = $statement->context->contextActivities->parent[0]->id;
        $contextparts = explode('subContentId=', $parentid);

        // Level 2.
        if (count($contextparts) == 1) {
            return [2, $objectuuid, null];
        }

        // Level 3.
        return [3, $objectuuid, $contextparts[1]];
    }

    /**
     * Get the Statement verb, by priority:
     * - passed or failed if result.success is defined.
     * - scored if a score is defined.
     * - completed if $completed param is true
     * - the original verb
     *
     * @param \stdClass $statement Statement
     * @return string
     */
    protected function statement_verb($statement) {

        // Default verb.
        $verb = $statement->verb;
        unset($statement->verb->display);

        // Result.
        if (isset($statement->result)) {
            if (isset($statement->result->success)) {

                // Success.
                if ($statement->result->success) {
                    $verb = $this->verbs->get('passed');
                } else {
                    $verb = $this->verbs->get('failed');
                }

            } else if (isset($statement->result->score)) {

                // Simple score.
                $verb = $this->verbs->get('scored');

            } else if (isset($statement->result->completion) && $statement->result->completion) {

                // Completion.
                $verb = $this->verbs->get('completed');
            }
        }
        return $verb;
    }

    /**
     * Get the Statement base and object.
     *
     * @param \stdClass $statement Statement
     * @param string $vocabtype Vocab type
     * @return array
     */
    protected function statement_base_object($statement, $vocabtype) {
        global $DB;

        // Get some data.
        list($level, $objectuuid, $parentuuid) = $this->statement_level($statement);
        $module = $DB->get_record('h5pactivity', array('id' => $this->event->objectid), '*', MUST_EXIST);
        $moduletype = $this->module_vocab_type($module);
        $base = $this->base('h5pactivity', true, $vocabtype);

        // Define the object and context.
        if ($level == 1) {

            // Object.
            $object = $this->activities->get('h5pactivity', $this->event->objectid, true, 'module', $vocabtype);

        } else {

            // Top object.
            $module = $this->activities->get('h5pactivity', $this->event->objectid, false, 'module', $moduletype);
            
            // Object.
            $objecttype = $this->activities->types->get($vocabtype);
            $object = $statement->object;
            $object->id = $module['id'] . '/item/' . $objectuuid;
            $object->definition->type = $objecttype->type;
            unset($object->definition->extensions);

            // Set granularity level to 'inside-learning-unit'.
            foreach ($base['context']['contextActivities']['category'] as &$category) {
                if ($category['definition']['type'] == 'http://vocab.xapi.fr/activities/granularity-level') {
                    $category['id'] = 'http://vocab.xapi.fr/categories/inside-learning-unit';
                }
            }

            // Move course from parent to grouping.
            $base['context']['contextActivities']['grouping'][] = $base['context']['contextActivities']['parent'][0];

            if ($level == 2) {

                // Add module to parent.
                $base['context']['contextActivities']['parent'][0] = $module;

            } else {

                // Add module to grouping.
                $base['context']['contextActivities']['grouping'][] = $module;

                // Add parent.
                $base['context']['contextActivities']['parent'][0] = (object)[
                    'id' => $module['id'] . '/item/' . $parentuuid
                ];
            }
        }
        return [$base, $object];
    }


}
