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
 * Defines the editing form for the tcs question type.
 *
 * @package qtype
 * @subpackage tcs
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>

 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * tcs question editing form definition.
 *
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>

 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs_edit_form extends question_edit_form {

    protected function definition_inner($mform) {

        $menu = array(
            get_string('caseno', 'qtype_tcs'),
            get_string('caseyes', 'qtype_tcs')
        );
        $mform->addElement('selectyesno', 'showquestiontext', get_string('showquestiontext', 'qtype_tcs'));

        $mform->addElement('text', 'labelsituation', get_string('labelsituation', 'qtype_tcs'), array('size' => 40));
        $mform->setType('labelsituation', PARAM_TEXT);

        $mform->addElement('text', 'labelhypothisistext', get_string('labelhypothisistext', 'qtype_tcs'), array('size' => 40));
        $mform->setType('labelhypothisistext', PARAM_TEXT);

        $mform->addElement('editor', 'hypothisistext', get_string('hypothisistext', 'qtype_tcs'), array('rows' => 5),
            $this->editoroptions);

        $mform->addElement('text', 'labeleffecttext', get_string('labeleffecttext', 'qtype_tcs'), array('size' => 40));
        $mform->setType('labeleffecttext', PARAM_TEXT);
        $mform->addHelpButton('labeleffecttext', 'labeleffecttext', 'qtype_tcs');

        $mform->addElement('editor', 'effecttext', get_string('effecttext', 'qtype_tcs'), array('rows' => 5), $this->editoroptions);

        $mform->addElement('text', 'labelnewinformationeffect',
                get_string('labelnewinformationeffect', 'qtype_tcs'), array('size' => 40));
        $mform->setType('labelnewinformationeffect', PARAM_TEXT);

        $mform->addElement('selectyesno', 'showfeedback', get_string('labelshowquestionfeedback', 'qtype_tcs'));

        $mform->addElement('text', 'labelfeedback', get_string('labelquestionfeedback', 'qtype_tcs'), array('size' => 40));
        $mform->setType('labelfeedback', PARAM_TEXT);

        $this->add_per_answer_fields($mform, get_string('choiceno', 'qtype_tcs', '{no}'),
                0, max(5, QUESTION_NUMANS_START));

        $this->add_combined_feedback_fields(false);

        $mform->disabledIf('labelfeedback', 'showfeedback', 'eq', 0);
        $mform->disabledIf('labelsituation', 'showquestiontext', 'eq', 0);
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question, true);
        $question = $this->data_preprocessing_combined_feedback($question, false);
        $question = $this->data_preprocessing_hints($question, true, true);

        // Prepare hypothisis text.
        $draftid = file_get_submitted_draft_itemid('hypothisistext');

        if (!empty($question->options->hypothisistext)) {
            $hypothisistext = $question->options->hypothisistext;
        } else {
            $hypothisistext = $this->_form->getElement('hypothisistext')->getValue();
            $hypothisistext = $hypothisistext['text'];
        }
        $hypothisistext = file_prepare_draft_area($draftid, $this->context->id,
                'qtype_tcs', 'hypothisistext', empty($question->id) ? null : (int) $question->id,
                $this->fileoptions, $hypothisistext);

        $question->hypothisistext = array();
        $question->hypothisistext['text'] = $hypothisistext;
        $question->hypothisistext['format'] = empty($question->options->hypothisistextformat) ?
            editors_get_preferred_format() : $question->options->hypothisistextformat;
        $question->hypothisistext['itemid'] = $draftid;

        // Prepare hypothisis text.
        $draftid = file_get_submitted_draft_itemid('effecttext');

        if (!empty($question->options->effecttext)) {
            $effecttext = $question->options->effecttext;
        } else {
            $effecttext = $this->_form->getElement('effecttext')->getValue();
            $effecttext = $effecttext['text'];
        }
        $effecttext = file_prepare_draft_area($draftid, $this->context->id,
                'qtype_tcs', 'effecttext', empty($question->id) ? null : (int) $question->id,
                $this->fileoptions, $effecttext);

        $question->effecttext = array();
        $question->effecttext['text'] = $effecttext;
        $question->effecttext['format'] = empty($question->options->effecttextformat) ?
            editors_get_preferred_format() : $question->options->effecttextformat;
        $question->effecttext['itemid'] = $draftid;

        $question->labeleffecttext = empty($question->options->labeleffecttext) ? '' : $question->options->labeleffecttext;
        $question->labelhypothisistext = empty($question->options->labelhypothisistext) ?
            '' : $question->options->labelhypothisistext;
        $question->labelnewinformationeffect = empty($question->options->labelnewinformationeffect) ?
            '' : $question->options->labelnewinformationeffect;
        $question->labelfeedback = empty($question->options->labelfeedback) ?
            '' : $question->options->labelfeedback;
        $question->labelsituation = empty($question->options->labelsituation) ?
            '' : $question->options->labelsituation;
        $question->showquestiontext = empty($question->options->showquestiontext) ? '' : $question->options->showquestiontext;
        $question->showfeedback = empty($question->options->showfeedback) ? '' : $question->options->showfeedback;

        return $question;
    }

    protected function get_per_answer_fields($mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        global $PAGE;
        $repeated = array();
        $repeated[] = $mform->createElement('editor', 'answer', $label, array('rows' => 3), $this->editoroptions);
        $repeated[] = $mform->createElement('text', 'fraction', get_string('fraction', 'qtype_tcs'), $gradeoptions);
        $repeated[] = $mform->createElement('editor', 'feedback', get_string('feedback', 'question'), array('rows' => 3),
            $this->editoroptions);
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['type'] = PARAM_TEXT;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        // Fill default values for answers.
        $renderer = $PAGE->get_renderer('core');
        if (!isset($this->question->options)) {
            $nbanswers = max(5, QUESTION_NUMANS_START);
            for ($i = 0; $i < $nbanswers; $i++) {
                $htmllikertscale = $renderer->render_from_template('qtype_tcs/texteditor_wrapper',
                        ['text' => get_string('likertscale' . ($i + 1), 'qtype_tcs')]);
                $mform->setDefault("answer[$i]", ['text' => $htmllikertscale]);
            }
        }

        return $repeated;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $answercount = 0;
        $totalfraction = 0;

        foreach ($answers as $key => $answer) {
            // Check number of choices, total fraction, etc.
            $trimmedanswer = trim($answer['text']);
            $fraction = (float) $data['fraction'][$key];

            if ($trimmedanswer === '' && empty($fraction)) {
                continue;
            }
            if ($trimmedanswer === '') {
                $errors['fraction['.$key.']'] = get_string('errgradesetanswerblank', 'qtype_tcs');
            }

            if (!is_numeric($data['fraction'][$key])) {
                $errors['fraction['.$key.']'] = get_string('fractionshouldbenumber', 'qtype_tcs');
            }
            $totalfraction += $fraction;

            $answercount++;
        }

        // Number of choices.
        if ($answercount == 0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_tcs', 2);
            $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_tcs', 2);
        } else if ($answercount == 1) {
            $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_tcs', 2);

        }

        return $errors;
    }

    public function qtype() {
        return 'tcs';
    }
}