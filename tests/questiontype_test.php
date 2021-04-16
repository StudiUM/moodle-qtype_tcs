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
 * @package   qtype_tcs
 * @copyright 2021 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/tcs/tests/helper.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');

/**
 * Unit tests for the tcs question definition class.
 *
 * @copyright 2021 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs_test extends question_testcase {
    /** @var qtype_tcs instance of the question type class to test. */
    protected $qtype;

    protected function setUp(): void {
        $this->qtype = question_bank::get_qtype('tcs');
    }

    protected function tearDown(): void {
        $this->qtype = null;
    }

    public function test_xml_import() {
        $qdata = new stdClass();

        $qdata->name = 'TCS-001';
        $qdata->questiontext = 'Here is the question';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'General feedback for the question';
        $qdata->generalfeedbackformat = FORMAT_HTML;

        $qdata->showquestiontext = 1;
        $qdata->labelsituation = 'Situation label';
        $qdata->labelhypothisistext = 'Hypothesis label';
        $qdata->hypothisistext = array('text' => 'The hypothesis is...', 'format' => FORMAT_HTML);
        $qdata->labeleffecttext = 'New information label';
        $qdata->effecttext = array('text' => 'The new information is...', 'format' => FORMAT_HTML);
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

        for ($i = 1; $i <= 5; $i++) {
            $feedback = "Feedback for choice $i";
            $qdata->fractionimport[] = $i;
            $qdata->answer[$i - 1] = [
                'text' => get_string("likertscale$i", 'qtype_tcs'),
                'format' => FORMAT_HTML
            ];
            $qdata->feedback[$i - 1] = [
                'text' => $feedback,
                'format' => FORMAT_HTML
            ];
        }
        $qdata->qtype = 'tcs';
        $qdata->defaultmark = 1;
        $qdata->penalty = 0;
        $qdata->idnumber = null;

        $xmldata = xmlize(file_get_contents(__DIR__.'/fixtures/questiontest.xml'));

        $importer = new qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'tcs');
        $this->assert(new question_check_specified_fields_expectation($qdata), $q);
    }

    public function test_xml_export() {

        $qdata = new stdClass();

        $qdata->name = 'TCS-001';
        $qdata->questiontext = 'Here is the question';
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedback = 'General feedback for the question';
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->options = new stdClass();
        $qdata->options->showquestiontext = true;
        $qdata->options->labelsituation = 'Situation label';
        $qdata->options->labelhypothisistext = 'Hypothesis label';
        $qdata->options->hypothisistext = 'The hypothesis is...';
        $qdata->options->hypothisistextformat = FORMAT_HTML;
        $qdata->options->labeleffecttext = 'New information label';
        $qdata->options->effecttext = 'The new information is...';
        $qdata->options->effecttextformat = FORMAT_HTML;
        $qdata->options->labelnewinformationeffect = 'Your hypothesis or option is';
        $qdata->options->labelfeedback = 'Comments label';
        $qdata->options->showfeedback = true;
        $qdata->options->showoutsidefieldcompetence = true;
        $qdata->options->correctfeedback = test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK;
        $qdata->options->correctfeedbackformat = FORMAT_HTML;
        $qdata->options->partiallycorrectfeedback = test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->shownumcorrect = 1;
        $qdata->options->incorrectfeedback = test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK;
        $qdata->options->incorrectfeedbackformat = FORMAT_HTML;
        $qdata->options->answers = [];
        for ($i = 1; $i <= 5; $i++) {
            $feedback = "Feedback for choice $i";
            $qdata->options->answers[$i] = (object) [
                'id' => $i,
                'answer' => get_string("likertscale$i", 'qtype_tcs'),
                'answerformat' => FORMAT_HTML,
                'fraction' => $i,
                'feedback' => $feedback,
                'feedbackformat' => FORMAT_HTML,
            ];
        }
        $qdata->contextid = \context_system::instance()->id;
        $qdata->id = 123;
        $qdata->qtype = 'tcs';
        $qdata->defaultmark = 1;
        $qdata->penalty = 0;
        $qdata->hidden = 0;
        $qdata->idnumber = null;

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);
        $this->assertXmlStringEqualsXmlFile(__DIR__.'/fixtures/questiontest.xml', $xml);
    }
}
