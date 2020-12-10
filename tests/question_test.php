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
 */
class qtype_tcs_question_test extends advanced_testcase {

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
}
