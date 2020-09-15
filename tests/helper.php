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

/**
 * Test helper class for the TCS question type.
 *
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs_test_helper extends question_test_helper {
    /**
     * Implements the parent function.
     *
     * @return array of example question names that can be passed as the $which
     * argument of {@link test_question_maker::make_question} when $qtype is
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
        $qdata->qtype = 'tcs';
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
        $qdata->labeleffecttext = 'New information label';
        $qdata->effecttext = 'The new information is...';
        $qdata->effecttextformat = FORMAT_PLAIN;
        $qdata->labelnewinformationeffect = 'Your hypothesis or option is';
        $qdata->labelfeedback = 'Comments label';
        $qdata->showfeedback = true;

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

        $qdata->options->answers = array(
            13 => (object) array(
                'id' => 13,
                'answer' => 'Severely weakened',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 3,
                'feedback' => 'Feedback for choice 1',
                'feedbackformat' => FORMAT_PLAIN,
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => 'weakened',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 2,
                'feedback' => 'Feedback for choice 2',
                'feedbackformat' => FORMAT_PLAIN,
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => 'Unchanged',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0,
                'feedback' => '',
                'feedbackformat' => FORMAT_PLAIN,
            ),
            16 => (object) array(
                'id' => 16,
                'answer' => 'Reinforced',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 2,
                'feedback' => 'Feedback for choice 4',
                'feedbackformat' => FORMAT_PLAIN,
            ),
            17 => (object) array(
                'id' => 17,
                'answer' => 'Strongly reinforced',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0,
                'feedback' => '',
                'feedbackformat' => FORMAT_PLAIN,
            )
        );

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
        $qdata->labeleffecttext = 'New information label';
        $qdata->effecttext = array('text' => 'The new information is...', 'format' => FORMAT_PLAIN);
        $qdata->labelnewinformationeffect = 'Your hypothesis or option is';
        $qdata->labelfeedback = 'Comments label';
        $qdata->showfeedback = true;

        $qdata->correctfeedback = array('text' => test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK,
                                                 'format' => FORMAT_HTML);
        $qdata->partiallycorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK,
                                                          'format' => FORMAT_HTML);
        $qdata->shownumcorrect = 1;
        $qdata->incorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK,
                                                   'format' => FORMAT_HTML);

        $qdata->fraction = array('3', '2', '0', '2', '0');

        $qdata->answer = array(
            0 => array(
                'text' => 'Severely weakened',
                'format' => FORMAT_PLAIN
            ),
            1 => array(
                'text' => 'Weakened',
                'format' => FORMAT_PLAIN
            ),
            2 => array(
                'text' => 'Unchanged',
                'format' => FORMAT_PLAIN
            ),
            3 => array(
                'text' => 'Reinforced',
                'format' => FORMAT_PLAIN
            ),
            4 => array(
                'text' => 'Strongly reinforced',
                'format' => FORMAT_PLAIN
            )
        );

        $qdata->feedback = array(
            0 => array(
                'text' => 'Feedback for choice 1',
                'format' => FORMAT_PLAIN
            ),
            1 => array(
                'text' => 'Feedback for choice 2',
                'format' => FORMAT_PLAIN
            ),
            2 => array(
                'text' => '',
                'format' => FORMAT_PLAIN
            ),
            3 => array(
                'text' => 'Feedback for choice 4',
                'format' => FORMAT_PLAIN
            ),
            4 => array(
                'text' => '',
                'format' => FORMAT_PLAIN
            )
        );

        return $qdata;
    }


    /**
     * Get the question data for a judgment question, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_tcs_question_data_judgment() {
        global $USER;

        $qdata = new stdClass();

        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->qtype = 'tcs';
        $qdata->name = 'TCS-002';
        $qdata->questiontext = 'Here is the question';
        $qdata->questiontextformat = FORMAT_PLAIN;
        $qdata->generalfeedback = 'General feedback for the question';
        $qdata->generalfeedbackformat = FORMAT_PLAIN;

        $qdata->showquestiontext = false;
        $qdata->labelsituation = 'Situation label';
        $qdata->labelhypothisistext = 'Hypothesis label';
        $qdata->hypothisistext = 'The hypothesis is...';
        $qdata->hypothisistextformat = FORMAT_PLAIN;
        $qdata->labeleffecttext = '';
        $qdata->effecttext = '';
        $qdata->effecttextformat = FORMAT_PLAIN;
        $qdata->labelnewinformationeffect = 'Your hypothesis or option is';
        $qdata->labelfeedback = 'Comments label';
        $qdata->showfeedback = false;

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

        $qdata->options->answers = array(
            13 => (object) array(
                'id' => 13,
                'answer' => 'Answer 1',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 2,
                'feedback' => 'Feedback for answer 1',
                'feedbackformat' => FORMAT_PLAIN,
            ),
            14 => (object) array(
                'id' => 14,
                'answer' => 'Answer 2',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 2,
                'feedback' => 'Feedback for answer 2',
                'feedbackformat' => FORMAT_PLAIN,
            ),
            15 => (object) array(
                'id' => 15,
                'answer' => 'Answer 3',
                'answerformat' => FORMAT_PLAIN,
                'fraction' => 0,
                'feedback' => '',
                'feedbackformat' => FORMAT_PLAIN,
            )
        );

        return $qdata;
    }

    /**
     * Get the question data for a judgment question, as it would be loaded by get_question_options.
     * @return object
     */
    public static function get_tcs_question_form_data_judgment() {
        $qdata = new stdClass();

        $qdata->name = 'TCS-002';
        $qdata->questiontext = array('text' => 'Here is the question', 'format' => FORMAT_PLAIN);
        $qdata->generalfeedback = array('text' => 'General feedback for the question', 'format' => FORMAT_PLAIN);

        $qdata->showquestiontext = false;
        $qdata->labelsituation = 'Situation label';
        $qdata->labelhypothisistext = 'Hypothesis label';
        $qdata->hypothisistext = array('text' => 'The hypothesis is...', 'format' => FORMAT_PLAIN);
        $qdata->labeleffecttext = '';
        $qdata->effecttext = array('text' => '', 'format' => FORMAT_PLAIN);
        $qdata->labelnewinformationeffect = 'Your hypothesis or option is';
        $qdata->labelfeedback = 'Comments label';
        $qdata->showfeedback = false;

        $qdata->correctfeedback = array('text' => test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK,
                                                 'format' => FORMAT_HTML);
        $qdata->partiallycorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK,
                                                          'format' => FORMAT_HTML);
        $qdata->shownumcorrect = 1;
        $qdata->incorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK,
                                                   'format' => FORMAT_HTML);

        $qdata->fraction = array('2', '2', '0');

        $qdata->answer = array(
            0 => array(
                'text' => 'Answer 1',
                'format' => FORMAT_PLAIN
            ),
            1 => array(
                'text' => 'Answer 2',
                'format' => FORMAT_PLAIN
            ),
            2 => array(
                'text' => 'Answer 3',
                'format' => FORMAT_PLAIN
            )
        );

        $qdata->feedback = array(
            0 => array(
                'text' => 'Feedback for answer 1',
                'format' => FORMAT_PLAIN
            ),
            1 => array(
                'text' => 'Feedback for answer 2',
                'format' => FORMAT_PLAIN
            ),
            2 => array(
                'text' => '',
                'format' => FORMAT_PLAIN
            )
        );

        return $qdata;
    }
}
