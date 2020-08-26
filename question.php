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
 * tcs question definition class.
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
 * Represents a tcs question.
 *
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>

 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs_question extends question_graded_automatically {
    public $answers;

    public $hypothisistext;
    public $hypothisistextformat;
    public $effecttext;
    public $effecttextformat;
    public $labeleffecttext;
    public $labelhypothisistext;
    public $showquestiontext;
    public $shownumcorrect;
    public $labelnewinformationeffect;
    public $labelsituation;
    public $labelfeedback;
    public $showfeedback;

    public $correctfeedback;
    public $correctfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;

    protected $order = null;

    public function start_attempt(question_attempt_step $step, $variant) {
        $this->order = array_keys($this->answers);
        $step->set_qt_var('_order', implode(',', $this->order));
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $this->order = explode(',', $step->get_qt_var('_order'));
    }

    public function get_order(question_attempt $qa) {
        $this->init_order($qa);
        return $this->order;
    }

    protected function init_order(question_attempt $qa) {
        if (is_null($this->order)) {
            $this->order = explode(',', $qa->get_step(0)->get_qt_var('_order'));
        }
    }

    public function get_expected_data() {
        return array('answer' => PARAM_INT, 'answerfeedback' => PARAM_RAW);
    }

    public function summarise_response(array $response) {
        if (!array_key_exists('answer', $response) || !array_key_exists($response['answer'], $this->order)) {
            return null;
        }

        $ansid = $this->order[$response['answer']];
        return $this->html_to_text($this->answers[$ansid]->answer, $this->answers[$ansid]->answerformat);
    }

    public function get_question_summary() {
        $question = $this->html_to_text($this->questiontext, $this->questiontextformat);
        $choices = array();
        foreach ($this->order as $ansid) {
            $choices[] = $this->html_to_text($this->answers[$ansid]->answer,
                    $this->answers[$ansid]->answerformat);
        }
        return $question . ': ' . implode('; ', $choices);
    }

    public function prepare_simulated_post_data($simulatedresponse) {
        $ansnumbertoanswerid = array_keys($this->answers);
        $ansid = $ansnumbertoanswerid[$simulatedresponse['answer']];
        return array('answer' => array_search($ansid, $this->order));
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key($prevresponse, $newresponse, 'answer');
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) && $response['answer'] !== '';
    }

    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseselectananswer', 'qtype_tcs');
    }

    public function get_response(question_attempt $qa) {
        return $qa->get_last_qt_var('answer', -1);
    }

    public function is_choice_selected($response, $value) {
        return (string) $response === (string) $value;
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && in_array($filearea,
                array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'))) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);

        } else if ($component == 'question' && $filearea == 'answer') {
            $answerid = reset($args); // Itemid is answer id.
            return  in_array($answerid, $this->order);

        } else if ($component == 'question' && $filearea == 'answerfeedback') {
            $answerid = reset($args); // Itemid is answer id.
            $response = $this->get_response($qa);
            $isselected = false;
            foreach ($this->order as $value => $ansid) {
                if ($ansid == $answerid) {
                    $isselected = $this->is_choice_selected($response, $value);
                    break;
                }
            }
            // Param $options->suppresschoicefeedback is a hack specific to the
            // oumultiresponse question type. It would be good to refactor to
            // avoid refering to it here.
            return $options->feedback && empty($options->suppresschoicefeedback) &&
                    $isselected;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else if ($component == 'qtype_tcs' && $filearea == 'hypothisistext') {
            return $qa->get_question()->hypothisistext && $args[0] == $this->id;

        } else if ($component == 'qtype_tcs' && $filearea == 'effecttext') {
            return $qa->get_question()->effecttext && $args[0] == $this->id;

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }

    public function classify_response(array $response) {
        if (!array_key_exists('answer', $response) ||
                !array_key_exists($response['answer'], $this->order)) {
            return array($this->id => question_classified_response::no_response());
        }
        $choiceid = $this->order[$response['answer']];
        $ans = $this->answers[$choiceid];
        return array($this->id => new question_classified_response($choiceid,
                $this->html_to_text($ans->answer, $ans->answerformat), $ans->fraction));
    }

    public function get_correct_response() {
        $maxfraction = $this->get_max_fraction();

        foreach ($this->answers as $key => $answer) {
            if ((string) $answer->fraction === (string) $maxfraction) {
                return array('answer' => $key);
            }
        }

        return array();
    }

    public function get_max_fraction() {
        $max = 0;

        foreach ($this->answers as $answer) {
            if ($answer->fraction > $max) {
                $max = $answer->fraction;
            }
        }

        return $max;
    }

    public function make_html_inline($html) {
        $html = preg_replace('~\s*<p>\s*~u', '', $html);
        $html = preg_replace('~\s*</p>\s*~u', '<br />', $html);
        $html = preg_replace('~(<br\s*/?>)+$~u', '', $html);
        return trim($html);
    }

    // Returns the percentage for the grade.
    public function grade_response(array $response) {
        if (array_key_exists('answer', $response) &&
                array_key_exists($response['answer'], $this->order)) {
            $fraction = $this->answers[$this->order[$response['answer']]]->fraction;
        } else {
            $fraction = 0;
        }

        $maxfraction = $this->get_max_fraction();
        if ($maxfraction == 0) {
            $result = 0;
        } else {
            $result = $fraction / $maxfraction;
        }

        return array($result, question_state::graded_state_for_fraction($result));
    }

    /**
     * Get tcs format renderer.
     *
     * @param moodle_page $page the page we are outputting to.
     * @return qtype_tcs_format_renderer_base the response-format-specific renderer.
     */
    public function get_format_renderer(moodle_page $page) {
        return $page->get_renderer('qtype_tcs', 'format_plain');
    }
}