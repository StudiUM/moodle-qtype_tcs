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
 * Test helper code for the TCS question type.
 *
 * @package    qtype_tcs
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/tcs/question.php');

/**
 * Test helper class for the TCS question type.
 *
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs_test_helper extends question_test_helper {

    /**
     * @var string The qtype name.
     */
    protected static $qtypename = 'tcs';

    /**
     * @var int The default answers number.
     */
    protected static $nbanswers = 5;

    /**
     * Implements the parent function.
     *
     * @return array of example question names that can be passed as the $which
     * argument of test_question_maker::make_question when $qtype is
     * this question type.
     */
    public function get_test_questions() {
        return ['reasoning', 'judgment'];
    }

    /**
     * Get the question data for a reasoning question, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_tcs_question_data_reasoning() {
        global $USER;

        $qdata = new stdClass();

        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = static::$qtypename;
        $qdata->name = 'TCS-001';
        $qdata->questiontext = 'Here is the question';
        $qdata->questiontextformat = FORMAT_PLAIN;
        $qdata->generalfeedback = 'General feedback for the question';
        $qdata->generalfeedbackformat = FORMAT_PLAIN;

        $qdata->showquestiontext = true;
        $qdata->labelsituation = 'Situation label';
        $qdata->labelhypothisistext = 'Hypothesis label';
        $qdata->hypothisistext = 'The hypothesis is...';
        $qdata->hypothisistextformat = FORMAT_PLAIN;
        if (static::$qtypename == 'tcs') {
            $qdata->labeleffecttext = 'New information label';
            $qdata->effecttext = 'The new information is...';
            $qdata->effecttextformat = FORMAT_PLAIN;
        }
        $qdata->labelnewinformationeffect = 'Your hypothesis or option is';
        $qdata->labelfeedback = 'Comments label';
        $qdata->showfeedback = true;
        $qdata->showoutsidefieldcompetence = true;

        $qdata->options = new stdClass();
        $qdata->options->correctfeedback =
                test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK;
        $qdata->options->correctfeedbackformat = FORMAT_HTML;
        $qdata->options->partiallycorrectfeedback =
                test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->shownumcorrect = 1;
        $qdata->options->incorrectfeedback =
                test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK;
        $qdata->options->incorrectfeedbackformat = FORMAT_HTML;

        for ($i = 1; $i <= static::$nbanswers; $i++) {
            $feedback = ($i == static::$nbanswers) ? "" : "Feedback for choice $i";
            $qdata->options->answers[$i] = [
                'id' => $i,
                'answer' => get_string("likertscale$i", 'qtype_' . static::$qtypename),
                'answerformat' => FORMAT_PLAIN,
                'fraction' => $i,
                'feedback' => $feedback,
                'feedbackformat' => FORMAT_PLAIN,
            ];
        }

        return $qdata;
    }

    /**
     * Get the question data for a reasoning question, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_tcs_question_form_data_reasoning() {
        $qdata = new stdClass();

        $qdata->name = 'TCS-001';
        $qdata->questiontext = ['text' => 'Here is the question', 'format' => FORMAT_PLAIN];
        $qdata->generalfeedback = ['text' => 'General feedback for the question', 'format' => FORMAT_PLAIN];

        $qdata->showquestiontext = true;
        $qdata->labelsituation = 'Situation label';
        $qdata->labelhypothisistext = 'Hypothesis label';
        $qdata->hypothisistext = ['text' => 'The hypothesis is...', 'format' => FORMAT_PLAIN];
        if (static::$qtypename == 'tcs') {
            $qdata->labeleffecttext = 'New information label';
            $qdata->effecttext = ['text' => 'The new information is...', 'format' => FORMAT_PLAIN];
        }
        $qdata->labelnewinformationeffect = 'Your hypothesis or option is';
        $qdata->labelfeedback = 'Comments label';
        $qdata->showfeedback = true;
        $qdata->showoutsidefieldcompetence = true;

        $qdata->correctfeedback = ['text' => test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK,
                                                 'format' => FORMAT_HTML];
        $qdata->partiallycorrectfeedback = ['text' => test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK,
                                                          'format' => FORMAT_HTML];
        $qdata->shownumcorrect = 1;
        $qdata->incorrectfeedback = ['text' => test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK,
                                                   'format' => FORMAT_HTML];

        for ($i = 1; $i <= static::$nbanswers; $i++) {
            $feedback = ($i == static::$nbanswers) ? "" : "Feedback for choice $i";
            $qdata->fraction[] = $i;
            $qdata->answer[$i - 1] = [
                'text' => get_string("likertscale$i", 'qtype_' . static::$qtypename),
                'format' => FORMAT_PLAIN,
            ];
            $qdata->feedback[$i - 1] = [
                'text' => $feedback,
                'format' => FORMAT_PLAIN,
            ];
        }

        return $qdata;
    }

    /**
     * Get the question data for a judgment question, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_tcs_question_data_judgment() {
        $qdata = self::get_tcs_question_data_reasoning();

        $qdata->name = 'TCS-002';
        $qdata->showquestiontext = false;
        $qdata->labeleffecttext = '';
        $qdata->effecttext = '';
        $qdata->effecttextformat = FORMAT_PLAIN;
        $qdata->showfeedback = false;
        $qdata->showoutsidefieldcompetence = false;
        $qdata->options->answers = [];
        for ($i = 1; $i <= 3; $i++) {
            $feedback = ($i == 3) ? "" : "Feedback for answer $i";
            $qdata->options->answers[$i] = [
                'id' => $i,
                'answer' => "Answer $i",
                'answerformat' => FORMAT_PLAIN,
                'fraction' => $i,
                'feedback' => $feedback,
                'feedbackformat' => FORMAT_PLAIN,
            ];
        }

        return $qdata;
    }

    /**
     * Get the question data for a judgment question, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_tcs_question_form_data_judgment() {
        $qdata = self::get_tcs_question_form_data_reasoning();
        $qdata->name = 'TCS-002';
        $qdata->showquestiontext = false;
        $qdata->labeleffecttext = '';
        $qdata->effecttext = ['text' => '', 'format' => FORMAT_PLAIN];
        $qdata->showfeedback = false;
        $qdata->showoutsidefieldcompetence = false;
        $qdata->fraction = [];
        $qdata->answer = [];
        $qdata->feedback = [];
        for ($i = 1; $i <= 3; $i++) {
            $feedback = ($i == 3) ? "" : "Feedback for answer $i";
            $qdata->fraction[] = $i;
            $qdata->answer[$i - 1] = [
                'text' => "Answer $i",
                'format' => FORMAT_PLAIN,
            ];
            $qdata->feedback[$i - 1] = [
                'text' => $feedback,
                'format' => FORMAT_PLAIN,
            ];
        }
        return $qdata;
    }

    /**
     * Makes a TCS reasoning question with some basic options.
     * Most yes/no options are set at "yes" (true).
     * @return qtype_tcs_question
     */
    public static function make_tcs_question_reasoning() {
        question_bank::load_question_definition_classes('tcs');
        $tcs = new qtype_tcs_question();
        test_question_maker::initialise_a_question($tcs);

        $tcs->qtype = static::$qtypename;
        $tcs->name = 'TCS-001';
        $tcs->questiontext = 'Here is the question';
        $tcs->questiontextformat = FORMAT_PLAIN;
        $tcs->generalfeedback = 'General feedback for the question';
        $tcs->generalfeedbackformat = FORMAT_PLAIN;

        $tcs->showquestiontext = true;
        $tcs->labelsituation = 'Situation label';
        $tcs->labelhypothisistext = 'Hypothesis label';
        $tcs->hypothisistext = 'The hypothesis is...';
        $tcs->hypothisistextformat = FORMAT_PLAIN;
        if (static::$qtypename == 'tcs') {
            $tcs->labeleffecttext = 'New information label';
            $tcs->effecttext = 'The new information is...';
            $tcs->effecttextformat = FORMAT_PLAIN;
        }
        $tcs->labelnewinformationeffect = 'Your hypothesis or option is';
        $tcs->labelfeedback = 'Comments label';
        $tcs->showfeedback = true;
        $tcs->showoutsidefieldcompetence = true;

        $tcs->answers = [];
        for ($i = 1; $i <= static::$nbanswers; $i++) {
            $answer = get_string("likertscale$i", 'qtype_' . static::$qtypename);
            $feedback = ($i == static::$nbanswers) ? "" : "Feedback for choice $i";
            $tcs->answers[$i] = new question_answer($i, $answer, $i - 1, $feedback, FORMAT_PLAIN);
        }

        return $tcs;
    }

    /**
     * Makes a TCS question similar to a judgment question, with other options than the reasoning question above.
     * Most yes/no options are set at "no" (false).
     * @return qtype_tcs_question
     */
    public static function make_tcs_question_judgment() {
        question_bank::load_question_definition_classes('tcs');
        $tcs = new qtype_tcs_question();
        test_question_maker::initialise_a_question($tcs);

        $tcs->qtype = static::$qtypename;
        $tcs->name = 'TCS-002';
        $tcs->questiontext = 'Here is the question';
        $tcs->questiontextformat = FORMAT_PLAIN;
        $tcs->generalfeedback = 'General feedback for the question';
        $tcs->generalfeedbackformat = FORMAT_PLAIN;

        $tcs->showquestiontext = false;
        $tcs->labelsituation = 'Situation label';
        $tcs->labelhypothisistext = 'Hypothesis label';
        $tcs->hypothisistext = 'The hypothesis is...';
        $tcs->hypothisistextformat = FORMAT_PLAIN;
        if (static::$qtypename == 'tcs') {
            $tcs->labeleffecttext = '';
            $tcs->effecttext = '';
            $tcs->effecttextformat = FORMAT_PLAIN;
        }
        $tcs->labelnewinformationeffect = 'Your hypothesis or option is';
        $tcs->labelfeedback = 'Comments label';
        $tcs->showfeedback = false;
        $tcs->showoutsidefieldcompetence = false;

        $tcs->answers = [];
        $tcs->answers[1] = new question_answer(1, "Answer 1", 0, "Feedback for choice 1", FORMAT_PLAIN);
        $tcs->answers[2] = new question_answer(2, "Answer 2", 1, "Feedback for choice 2", FORMAT_PLAIN);
        $tcs->answers[3] = new question_answer(3, "Answer 3", 2, "Feedback for choice 3", FORMAT_PLAIN);
        $tcs->answers[4] = new question_answer(4, "Answer 4", 2, "Feedback for choice 4", FORMAT_PLAIN);

        return $tcs;
    }
}
