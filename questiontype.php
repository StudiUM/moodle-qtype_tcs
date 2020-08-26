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
 * Question type class for the tcs question type.
 *
 * @package qtype
 * @subpackage tcs
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>

 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/tcs/question.php');


/**
 * The tcs question type.
 *
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>

 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs extends question_type {
    public function get_question_options($question) {
        global $DB, $OUTPUT;
        $question->options = $DB->get_record('qtype_tcs_options',
                array('questionid' => $question->id), '*', MUST_EXIST);

        parent::get_question_options($question);
    }

    /**
     * Redefines the parent function : Set any missing settings for this question to the default values. This is
     * called before displaying the question editing form.
     *
     * @param object $questiondata the question data, loaded from the databsae,
     *      or more likely a newly created question object that is only partially
     *      initialised.
     */
    public function set_default_options($questiondata) {
        if (empty($questiondata->options)) {
            // Sets the default values for the different fields.
            $questiondata->options = new \stdClass();
            $questiondata->options->hypothisistext = '';
            $questiondata->options->hypothisistextformat = FORMAT_HTML;
            $questiondata->options->effecttext = '';
            $questiondata->options->effecttextformat = FORMAT_HTML;
            $questiondata->options->correctfeedback = '';
            $questiondata->options->correctfeedbackformat = FORMAT_HTML;
            $questiondata->options->partiallycorrectfeedback = '';
            $questiondata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
            $questiondata->options->incorrectfeedback = '';
            $questiondata->options->incorrectfeedbackformat = FORMAT_HTML;
            $questiondata->options->labeleffecttext = get_string('effecttextdefault', 'qtype_tcs');
            $questiondata->options->labelhypothisistext = get_string('hypothisistextdefault', 'qtype_tcs');
            $questiondata->options->labelnewinformationeffect = get_string('newinformationeffect', 'qtype_tcs');
            $questiondata->options->labelfeedback = get_string('feedback', 'qtype_tcs');
            $questiondata->options->labelsituation = get_string('situation', 'qtype_tcs');
            $questiondata->options->showquestiontext = 1;
            $questiondata->options->showfeedback = 1;
        }
    }

    public function save_question_options($question) {
        global $DB;
        $context = $question->context;
        $result = new stdClass();

        $oldanswers = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC');

        $answercount = 0;
        foreach ($question->answer as $key => $answer) {
            if ($answer != '') {
                $answercount++;
            }
        }

        if ($answercount < 2) { // Check there are at lest 2 answers for multiple choice.
            $result->notice = get_string('notenoughanswers', 'qtype_tcs', '2');
            return $result;
        }

        foreach ($question->answer as $key => $answerdata) {
            if (trim($answerdata['text']) == '') {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }

            // Doing an import.
            $answer->answer = $this->import_or_save_files($answerdata,
                    $context, 'question', 'answer', $answer->id);
            $answer->answerformat = $answerdata['format'];
            $answer->fraction = (float) $question->fraction[$key];
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];

            $DB->update_record('question_answers', $answer);
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }

        $options = $DB->get_record('qtype_tcs_options', array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->hypothisistext = '';
            $options->hypothisistextformat = FORMAT_HTML;
            $options->effecttext = '';
            $options->effecttextformat = FORMAT_HTML;
            $options->correctfeedback = '';
            $options->correctfeedbackformat = FORMAT_HTML;
            $options->partiallycorrectfeedback = '';
            $options->partiallycorrectfeedbackformat = FORMAT_HTML;
            $options->incorrectfeedback = '';
            $options->incorrectfeedbackformat = FORMAT_HTML;
            $options->labeleffecttext = get_string('effecttextdefault', 'qtype_tcs');
            $options->labelhypothisistext = get_string('hypothisistextdefault', 'qtype_tcs');
            $options->labelnewinformationeffect = get_string('newinformationeffect', 'qtype_tcs');
            $options->labelfeedback = get_string('feedback', 'qtype_tcs');
            $options->labelsituation = get_string('situation', 'qtype_tcs');
            $options->showfeedback = 1;
            $options->showquestiontext = 1;
            $options->id = $DB->insert_record('qtype_tcs_options', $options);
        }

        $options->hypothisistext = $this->import_or_save_files($question->hypothisistext,
                $context, 'qtype_tcs', 'hypothisistext', $question->id);
        $options->hypothisistextformat = $question->hypothisistext['format'];
        $options->effecttext = $this->import_or_save_files($question->effecttext,
                $context, 'qtype_tcs', 'effecttext', $question->id);
        $options->effecttextformat = $question->effecttext['format'];
        $options->labeleffecttext = $question->labeleffecttext;
        $options->labelhypothisistext = $question->labelhypothisistext;
        $options->showquestiontext = (int) $question->showquestiontext;
        $options->labelnewinformationeffect = $question->labelnewinformationeffect;
        $options->labelfeedback = $question->labelfeedback;
        $options->labelsituation = $question->labelsituation;
        $options->showfeedback = (int) $question->showfeedback;
        $options = $this->save_combined_feedback_helper($options, $question, $context, false);
        $DB->update_record('qtype_tcs_options', $options);

        $this->save_hints($question, true);
    }

    protected function make_question_instance($questiondata) {
        question_bank::load_question_definition_classes($this->name());
        return new qtype_tcs_question();
    }

    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->hypothisistext = $questiondata->options->hypothisistext;
        $question->effecttext = $questiondata->options->effecttext;
        $question->labeleffecttext = $questiondata->options->labeleffecttext;
        $question->labelhypothisistext = $questiondata->options->labelhypothisistext;
        $question->showquestiontext = $questiondata->options->showquestiontext;
        $question->labelnewinformationeffect = $questiondata->options->labelnewinformationeffect;
        $question->labelfeedback = $questiondata->options->labelfeedback;
        $question->labelsituation = $questiondata->options->labelsituation;
        $question->showfeedback = $questiondata->options->showfeedback;

        $this->initialise_combined_feedback($question, $questiondata, false);

        $this->initialise_question_answers($question, $questiondata, false);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_tcs_options', array('questionid' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    public function get_random_guess_score($questiondata) {
        // TODO.
        return 0;
    }

    public function get_possible_responses($questiondata) {
        $responses = array();

        foreach ($questiondata->options->answers as $aid => $answer) {
            $responses[$aid] = new question_possible_response(
                    question_utils::to_plain_text($answer->answer, $answer->answerformat),
                    $answer->fraction);
        }

        $responses[null] = question_possible_response::no_response();
        return array($questiondata->id => $responses);
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid, true);
        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
        $fs = get_file_storage();
        $fs->move_area_files_to_new_context($oldcontextid, $newcontextid, 'qtype_tcs', 'hypothisistext', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid, $newcontextid, 'qtype_tcs', 'effecttext', $questionid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid, true);
        $this->delete_files_in_combined_feedback($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
        $fs = get_file_storage();
        $fs->delete_area_files($contextid, 'qtype_tcs', 'hypothisistext', $questionid);
        $fs->delete_area_files($contextid, 'qtype_tcs', 'effecttext', $questionid);
    }
}
