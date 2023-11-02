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
 * @package qtype_tcs
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>

 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents a tcs question.
 *
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs_question extends question_graded_automatically {
    /**
     * @var array
     */
    public $answers;

    /**
     * @var string
     */
    public $hypothisistext;
    /**
     * @var int
     */
    public $hypothisistextformat;
    /**
     * @var string
     */
    public $effecttext;
    /**
     * @var int
     */
    public $effecttextformat;
    /**
     * @var string
     */
    public $labeleffecttext;
    /**
     * @var string
     */
    public $labelhypothisistext;
    /**
     * @var boolean
     */
    public $showquestiontext;
    /**
     * @var boolean
     */
    public $shownumcorrect;
    /**
     * @var string
     */
    public $labelnewinformationeffect;
    /**
     * @var string
     */
    public $labelsituation;
    /**
     * @var string
     */
    public $labelfeedback;
    /**
     * @var boolean
     */
    public $showfeedback;
    /**
     * @var boolean
     */
    public $showoutsidefieldcompetence;
    /**
     * @var string
     */
    public $correctfeedback;
    /**
     * @var int
     */
    public $correctfeedbackformat;
    /**
     * @var string
     */
    public $partiallycorrectfeedback;
    /**
     * @var int
     */
    public $partiallycorrectfeedbackformat;
    /**
     * @var string
     */
    public $incorrectfeedback;
    /**
     * @var int
     */
    public $incorrectfeedbackformat;
    /**
     * @var array
     */
    protected $order = null;

    /**
     * @var string The qtype name.
     */
    protected static $qtypename = 'tcs';

    /**
     * Start attempt.
     *
     * @param question_attempt_step $step
     * @param mixed $variant
     */
    public function start_attempt(question_attempt_step $step, $variant) {
        $this->order = array_keys($this->answers);
        $step->set_qt_var('_order', implode(',', $this->order));
    }

    /**
     * Apply attempt state.
     *
     * @param question_attempt_step $step
     */
    public function apply_attempt_state(question_attempt_step $step) {
        $this->order = explode(',', $step->get_qt_var('_order'));
    }

    /**
     * Get order.
     *
     * @param question_attempt $qa
     * @return array Return order
     */
    public function get_order(question_attempt $qa) {
        $this->init_order($qa);
        return $this->order;
    }

    /**
     * Initialize order.
     *
     * @param question_attempt $qa
     */
    protected function init_order(question_attempt $qa) {
        if (is_null($this->order)) {
            $this->order = explode(',', $qa->get_step(0)->get_qt_var('_order'));
        }
    }

    /**
     * Get expected data.
     *
     * @return array answers and feedbacks.
     */
    public function get_expected_data() {
        return array('answer' => PARAM_INT, 'answerfeedback' => PARAM_RAW, 'outsidefieldcompetence' => PARAM_INT);
    }

    /**
     * Summarize response.
     *
     * @param array $response
     * @return string
     */
    public function summarise_response(array $response) {
        $hasanswer = array_key_exists('answer', $response) && array_key_exists($response['answer'], $this->order);
        $hasfeedback = array_key_exists('answerfeedback', $response) && !empty($response['answerfeedback']);

        if (!$hasfeedback && !$hasanswer) {
            // This might be for the correct answer as returned by get_correct_response.
            $selectedchoices = array();
            foreach ($this->order as $key => $ans) {
                $fieldname = $this->field($key);
                if (array_key_exists($fieldname, $response) && $response[$fieldname]) {
                    $selectedchoices[] = trim($this->html_to_text($this->answers[$ans]->answer,
                            $this->answers[$ans]->answerformat) ?? '');
                }
            }
            if (empty($selectedchoices)) {
                return null;
            }
            return implode('; ', $selectedchoices);
        }

        // This is for an answer (and/or feedback) submitted by a user.
        if ($hasanswer) {
            $ansid = $this->order[$response['answer']];
            $retval = trim($this->html_to_text($this->answers[$ansid]->answer, $this->answers[$ansid]->answerformat) ?? '');
            if ($hasfeedback) {
                $retval .= ":\n \n".$response['answerfeedback'];
            }
        } else {
            $retval = $response['answerfeedback'];
        }
        return $retval;
    }

    /**
     * Get question summary.
     *
     * @return string
     */
    public function get_question_summary() {
        $question = $this->html_to_text($this->questiontext, $this->questiontextformat);
        $choices = array();
        foreach ($this->order as $ansid) {
            $choices[] = $this->html_to_text($this->answers[$ansid]->answer,
                    $this->answers[$ansid]->answerformat);
        }
        return $question . ': ' . implode('; ', $choices);
    }

    /**
     * Prepare simulated post data.
     *
     * @param array $simulatedresponse
     * @return array
     */
    public function prepare_simulated_post_data($simulatedresponse) {
        $ansnumbertoanswerid = array_keys($this->answers);
        $ansid = $ansnumbertoanswerid[$simulatedresponse['answer']];
        return array('answer' => array_search($ansid, $this->order));
    }

    /**
     * Is same response.
     *
     * @param array $prevresponse
     * @param array $newresponse
     * @return boolean
     */
    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key($prevresponse, $newresponse, 'answer')
            && question_utils::arrays_same_at_key($prevresponse, $newresponse, 'answerfeedback')
            && question_utils::arrays_same_at_key($prevresponse, $newresponse, 'outsidefieldcompetence');
    }

    /**
     * Return true if the answer is completed, false otherwise.
     *
     * @param array $response
     * @return boolean
     */
    private function is_answer_completed(array $response) {
        if (array_key_exists('outsidefieldcompetence', $response) && $response['outsidefieldcompetence']) {
            return true;
        }
        return array_key_exists('answer', $response) && $response['answer'] !== '';
    }

    /**
     * Return true if the answer's feedback is completed, false otherwise.
     * If the feedback is not necessary, it also returns true.
     *
     * @param array $response
     * @return boolean
     */
    private function is_feedback_completed(array $response) {
        if (array_key_exists('outsidefieldcompetence', $response) && $response['outsidefieldcompetence']) {
            return true;
        }
        $feedbackcomplete = true;
        if (array_key_exists('answerfeedback', $response) && $response['answerfeedback'] == '') {
            $feedbackcomplete = false;
        }
        return $feedbackcomplete;
    }

    /**
     * Is complete response.
     *
     * @param array $response
     * @return boolean
     */
    public function is_complete_response(array $response) {
        return $this->is_feedback_completed($response) && $this->is_answer_completed($response);
    }

    /**
     * Is gradable response.
     *
     * @param array $response
     * @return boolean
     */
    public function is_gradable_response(array $response) {
        return $this->is_feedback_completed($response) || $this->is_answer_completed($response);
    }

    /**
     * Get validation error.
     *
     * @param array $response
     * @return string
     */
    public function get_validation_error(array $response) {
        if (!$this->is_answer_completed($response)) {
            return get_string('errnoanswer', 'qtype_tcs');
        }
        if (!$this->is_feedback_completed($response)) {
            return get_string('errnofeedback', 'qtype_tcs');
        }
        return '';
    }

    /**
     * Get response.
     *
     * @param question_attempt $qa
     * @return mixed string value
     */
    public function get_response(question_attempt $qa) {
        return $qa->get_last_qt_var('answer', -1);
    }

    /**
     * Is choice selected.
     *
     * @param string $response
     * @param string $value
     * @return boolean
     */
    public function is_choice_selected($response, $value) {
        return (string) $response === (string) $value;
    }

    /**
     * Check file access.
     *
     * @param mixed $qa
     * @param array $options
     * @param string $component
     * @param string $filearea
     * @param array $args
     * @param boolean $forcedownload
     * @return boolean
     */
    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && in_array($filearea,
                array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'))) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);

        } else if (($component == 'question' && $filearea == 'answer') ||
                ($component == 'question' && $filearea == 'answerfeedback')) {
            $answerid = reset($args); // Itemid is answer id.
            return  in_array($answerid, $this->order);

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else if ($component == 'qtype_' . static::$qtypename && $filearea == 'hypothisistext') {
            return $qa->get_question(false)->hypothisistext && $args[0] == $this->id;

        } else if (static::$qtypename == 'tcs' && $component == 'qtype_tcs' && $filearea == 'effecttext') {
            return $qa->get_question(false)->effecttext && $args[0] == $this->id;

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }

    /**
     * Classify response.
     *
     * @param array $response
     * @return array response
     */
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

    /**
     * For use by get_correct_response and summarise_response. Similar to multichoice.
     *
     * @param int $key choice number
     * @return string the question-type variable name.
     */
    protected function field($key) {
        return 'choice' . $key;
    }

    /**
     * Get correct response.
     *
     * @return array
     */
    public function get_correct_response() {
        $maxfraction = $this->get_max_fraction();

        $response = array();
        foreach ($this->order as $key => $answerid) {
            if ((string) $this->answers[$answerid]->fraction === (string) $maxfraction) {
                $response[$this->field($key)] = 1;
            }
        }
        return $response;
    }

    /**
     * Get max fraction.
     *
     * @return int
     */
    public function get_max_fraction() {
        $max = 0;

        foreach ($this->answers as $answer) {
            if ($answer->fraction > $max) {
                $max = $answer->fraction;
            }
        }

        return $max;
    }

    /**
     * Make HTML INLINE.
     *
     * @param string $html
     * @return string
     */
    public function make_html_inline($html) {
        if (is_string($html)) {
            $html = preg_replace('~\s*<p>\s*~u', '', $html);
            $html = preg_replace('~\s*</p>\s*~u', '<br />', $html);
            $html = preg_replace('~(<br\s*/?>)+$~u', '', $html);
            return trim($html);
        }
        return '';
    }

    /**
     * Returns the percentage for the grade.
     *
     * @param array $response
     * @return array
     */
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
        return $page->get_renderer('qtype_' . static::$qtypename, 'format_plain');
    }
}
