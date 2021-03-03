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
        return array('reasoning', 'judgment');
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
        $qdata->questiontext = array('text' => 'Here is the question', 'format' => FORMAT_PLAIN);
        $qdata->generalfeedback = array('text' => 'General feedback for the question', 'format' => FORMAT_PLAIN);

        $qdata->showquestiontext = true;
        $qdata->labelsituation = 'Situation label';
        $qdata->labelhypothisistext = 'Hypothesis label';
        $qdata->hypothisistext = array('text' => 'The hypothesis is...', 'format' => FORMAT_PLAIN);
        if (static::$qtypename == 'tcs') {
            $qdata->labeleffecttext = 'New information label';
            $qdata->effecttext = array('text' => 'The new information is...', 'format' => FORMAT_PLAIN);
        }
        $qdata->labelnewinformationeffect = 'Your hypothesis or option is';
        $qdata->labelfeedback = 'Comments label';
        $qdata->showfeedback = true;
        $qdata->showoutsidefieldcompetence = true;

        $qdata->correctfeedback = array('text' => test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK,
                                                 'format' => FORMAT_HTML);
        $qdata->partiallycorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK,
                                                          'format' => FORMAT_HTML);
        $qdata->shownumcorrect = 1;
        $qdata->incorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK,
                                                   'format' => FORMAT_HTML);

        for ($i = 1; $i <= static::$nbanswers; $i++) {
            $feedback = ($i == static::$nbanswers) ? "" : "Feedback for choice $i";
            $qdata->fraction[] = $i;
            $qdata->answer[$i - 1] = [
                'text' => get_string("likertscale$i", 'qtype_' . static::$qtypename),
                'format' => FORMAT_PLAIN
            ];
            $qdata->feedback[$i - 1] = [
                'text' => $feedback,
                'format' => FORMAT_PLAIN
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
        $qdata->effecttext = array('text' => '', 'format' => FORMAT_PLAIN);
        $qdata->showfeedback = false;
        $qdata->fraction = [];
        $qdata->answer = [];
        $qdata->feedback = [];
        for ($i = 1; $i <= 3; $i++) {
            $feedback = ($i == 3) ? "" : "Feedback for answer $i";
            $qdata->fraction[] = $i;
            $qdata->answer[$i - 1] = [
                'text' => "Answer $i",
                'format' => FORMAT_PLAIN
            ];
            $qdata->feedback[$i - 1] = [
                'text' => $feedback,
                'format' => FORMAT_PLAIN
            ];
        }
        return $qdata;
    }
}
