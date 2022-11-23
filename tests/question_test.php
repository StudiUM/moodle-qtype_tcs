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
 * Unit tests for the tcs question definition class.
 *
 * @package    qtype_tcs
 * @copyright  2020 Université de Montréal
 * @author     Issam Taboubi <issa.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_tcs;

use test_question_maker;
use question_attempt_step;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for qtype_tcs_definition.
 *
 * @package    qtype_tcs
 * @copyright  2020 Université de Montréal
 * @author     Issam Taboubi <issa.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_tcs_question
 */
class question_test extends \advanced_testcase {

    /**
     * Test get_question_data and get_question_form_data for the two examples of questions.
     * @return void
     */
    public function test_questiondata() {
        $question = test_question_maker::get_question_data('tcs');
        $this->assertEquals('tcs', $question->qtype);
        $this->assertCount(5, $question->options->answers);
        $answer1 = $question->options->answers[1];
        $this->assertEquals(get_string('likertscale1', 'qtype_tcs'), $answer1['answer']);

        // Test form data.
        $formdata = test_question_maker::get_question_form_data('tcs');
        $this->assertCount(5, $formdata->answer);
        $this->assertCount(5, $formdata->feedback);
        $this->assertCount(5, $formdata->fraction);
        $this->assertEquals(get_string('likertscale1', 'qtype_tcs'), $formdata->answer[0]['text']);

        // Tcs judgment.
        $question = test_question_maker::get_question_data('tcs', 'judgment');
        $this->assertEquals('tcs', $question->qtype);
        $this->assertCount(3, $question->options->answers);
        $answer1 = $question->options->answers[1];
        $this->assertEquals('Answer 1', $answer1['answer']);

        // Test form data.
        $formdata = test_question_maker::get_question_form_data('tcs', 'judgment');
        $this->assertCount(3, $formdata->answer);
        $this->assertCount(3, $formdata->feedback);
        $this->assertCount(3, $formdata->fraction);
        $this->assertEquals('Answer 1', $formdata->answer[0]['text']);
    }

    /**
     * Test is_complete_response.
     * @return void
     */
    public function test_is_complete_response() {
        $question = test_question_maker::make_question('tcs', 'reasoning');
        $this->assertFalse($question->is_complete_response(array()));
        $this->assertTrue($question->is_complete_response(array('answer' => '0')));
        $this->assertTrue($question->is_complete_response(array('answer' => '2', 'answerfeedback' => 'Test')));
        $this->assertFalse($question->is_complete_response(array('answer' => '1', 'answerfeedback' => '')));
        $this->assertFalse($question->is_complete_response(array('answer' => '', 'answerfeedback' => 'Test')));
    }

    /**
     * Test is_same_response.
     * @return void
     */
    public function test_is_same_response() {
        $question = test_question_maker::make_question('tcs', 'reasoning');
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($question->is_same_response(
                array(),
                array()));

        $this->assertFalse($question->is_same_response(
                array(),
                array('answer' => '1')));

        $this->assertTrue($question->is_same_response(
                array('answer' => '1'),
                array('answer' => '1')));

        $this->assertFalse($question->is_same_response(
                array('answer' => '2', 'answerfeedback' => 'Test'),
                array('answer' => '1', 'answerfeedback' => 'Test')));

        $this->assertFalse($question->is_same_response(
                array('answer' => '1', 'answerfeedback' => 'Test 1'),
                array('answer' => '1', 'answerfeedback' => 'Test 2')));

        $this->assertTrue($question->is_same_response(
                array('answer' => '1', 'answerfeedback' => 'Test'),
                array('answer' => '1', 'answerfeedback' => 'Test')));
    }

    /**
     * Test grade_response.
     * @return void
     */
    public function test_grading() {
        // Question with only one good answer.
        $question = test_question_maker::make_question('tcs', 'reasoning');
        $question->start_attempt(new question_attempt_step(), 1);

        // Most popular answer has 4 panelists : others are based on the order of the answers (for easy testing).
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => 0)));

        $this->assertEquals(array(0.25, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 1)));

        $this->assertEquals(array(0.5, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 2)));

        $this->assertEquals(array(0.75, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 3)));

        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => 4)));

        // Question with two good answers.
        $question = test_question_maker::make_question('tcs', 'judgment');
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => 0)));

        $this->assertEquals(array(0.5, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 1)));

        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => 2)));

        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => 3)));
    }

    /**
     * Test get_correct_response.
     * @return void
     */
    public function test_get_correct_response() {
        // Question with only one good answer.
        $question = test_question_maker::make_question('tcs', 'reasoning');
        $question->start_attempt(new question_attempt_step(), 1);
        $this->assertEquals(array('choice4' => 1), $question->get_correct_response());

        // Question with two good answers.
        $question = test_question_maker::make_question('tcs', 'judgment');
        $question->start_attempt(new question_attempt_step(), 1);
        $this->assertEquals(array('choice2' => 1, 'choice3' => 1), $question->get_correct_response());
    }

    /**
     * Test summarise_response.
     * @return void
     */
    public function test_summarise_response() {
        $question = test_question_maker::make_question('tcs', 'reasoning');
        $question->start_attempt(new question_attempt_step(), 1);

        $summary = $question->summarise_response(array('answer' => '1', 'answerfeedback' => 'Comment 1'),
            test_question_maker::get_a_qa($question));
        $this->assertEquals("Weakened:\n \nComment 1", $summary);

        $summary = $question->summarise_response(array('answer' => '1'), test_question_maker::get_a_qa($question));
        $this->assertEquals("Weakened", $summary);

        $this->assertNull($question->summarise_response(array(), test_question_maker::get_a_qa($question)));
        $this->assertNull($question->summarise_response(array('answer' => '-1'), test_question_maker::get_a_qa($question)));
    }
}
