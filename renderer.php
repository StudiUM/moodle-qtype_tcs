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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
* tcs question renderer class.
*
* @package qtype
* @subpackage tcs
* @copyright 2014 Julien Girardot (julien.girardot@ctimage.com)

* @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


defined('MOODLE_INTERNAL') || die();


/**
* Generates the output for tcs questions.
*
* @copyright 2014 Julien Girardot (julien.girardot@ctimage.com)

* @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
class qtype_tcs_renderer extends qtype_with_combined_feedback_renderer {
    /**
     * Whether a choice should be considered right, wrong or partially right.
     * @param question_answer $ans representing one of the choices.
     * @return fload 1.0, 0.0 or something in between, respectively.
     */
    protected function is_right(question_answer $ans) {
        return $ans->fraction;
    }
    
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        
        $question   = $qa->get_question();
                
        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
        }
        
        if ($placeholder) {
            $questiontext = substr_replace($questiontext, $input,
                    strpos($questiontext, $placeholder), strlen($placeholder));
        }
        
        if(!empty($question->showquestiontext)) {
            $result = html_writer::tag('div', $questiontext, array('class' => 'qtext'));
        }
        
        $result .= html_writer::start_tag('table', array('class' => 'w100 tcs-table generaltable'));
        
        $result .= html_writer::start_tag('thead', array('class' => ''));
        $result .= html_writer::start_tag('tr', array('class' => ''));
        $result .= html_writer::tag('th', $question->labelhypothisistext, array('class' => 'w30 header'));
        $result .= html_writer::tag('th', $question->labeleffecttext, array('class' => 'w40 header'));
        $result .= html_writer::tag('th', 'Cette nouvelle information rend votre hypothèse', array('class' => 'w30 header'));
        $result .= html_writer::end_tag('tr');
        $result .= html_writer::end_tag('thead');
        
        $result .= html_writer::start_tag('tbody', array('class' => ''));
        $result .= html_writer::start_tag('tr', array('class' => ''));
        $result .= html_writer::tag('td', $question->hypothisistext, array('class' => 'leftalign cell')); // affichage de l'hypothèse
        $result .= html_writer::tag('td', $question->effecttext, array('class' => 'leftalign cell')); // affichage de l'effet sur hypothèse
        
        $result .= html_writer::start_tag('td', array('class' => 'leftalign cell')); // Affichage des réponses
        
        $result .= $this->get_answers_result($qa, $options);
        
        $result .= html_writer::end_tag('td');
        $result .= html_writer::end_tag('tr');
        $result .= html_writer::end_tag('tbody');
        
        $result .= html_writer::end_tag('table');
        
        return $result;
    }
    
    public function get_answers_result(question_attempt $qa, question_display_options $options) {
        $radiobuttons = array();
        $feedbackimg = array();
        $feedback = array();
        $classes = array();
        $cpt = -2;
        
        $question = $qa->get_question();
        $response = $question->get_response($qa);
        
        $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = array(
            'type' => 'radio',
            'name' => $inputname,
        );
        
        if ($options->readonly) {
            $inputattributes['disabled'] = 'disabled';
        }
        
        $max_fraction = $question->get_max_fraction($question->answers);
        
        foreach ($question->get_order($qa) as $value => $ansid) {
            $ans = $question->answers[$ansid];
            $inputattributes['name']    = $qa->get_qt_field_name('answer');
            $inputattributes['value']   = $value;
            $inputattributes['id']      = $qa->get_qt_field_name('answer' . $value);
            
            $isselected = $question->is_choice_selected($response, $value);
            if ($isselected) {
                $inputattributes['checked'] = 'checked';
            } else {
                unset($inputattributes['checked']);
            }
            
            $radiobuttons[] =   html_writer::empty_tag('input', $inputattributes) .
                                html_writer::tag('label', 
                                                 $cpt . ' '. $question->make_html_inline($question->format_text($ans->answer, $ans->answerformat, $qa, 'question', 'answer', $ansid)),
                                                 array('for' => $inputattributes['id'])
                                                );
            
            // Param $options->suppresschoicefeedback is a hack specific to the
            // oumultiresponse question type. It would be good to refactor to
            // avoid refering to it here.
            $classes[] = 'r' . ($value % 2);
            if ($options->correctness) {
                $percent = round(($ans->fraction / $max_fraction)*100);
                $feedbackstruct  = html_writer::start_tag('span', array('class' => 'gauge'));
                $feedbackstruct .= html_writer::tag('span', '', array('style' => 'width:'.$percent.'%'));
                $feedbackstruct .= html_writer::end_tag('span');
                $feedbackstruct .= html_writer::tag('span', (int)$ans->fraction);
                $feedback[] = $feedbackstruct;
            } else {
                $feedback[] = '';
            }
            
            $cpt++;
        }
        
        $result = '';
        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        
        foreach ($radiobuttons as $key => $radio) {
            $result .= html_writer::tag('div', $radio . ' ' . $feedback[$key], array('class' => $classes[$key])) . "\n";
        }
        
        $result .= html_writer::end_tag('div'); // Answer.

        $result .= html_writer::end_tag('div'); // Ablock.
        
        return $result;
    }
    
    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }
    
    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        
        $right = array();
        $max_fraction = $this->get_max_fraction($question->answers);
        
        foreach ($question->answers as $ansid => $ans) {
            if((string) $ans->fraction === (string) $max_fraction) {
                $right[] = $question->make_html_inline($question->format_text($ans->answer, $ans->answerformat,
                            $qa, 'question', 'answer', $ansid));
            }
        }
        
        if (!empty($right)) {
                return get_string('correctansweris', 'qtype_tcs',
                        implode(', ', $right));
        }
        
        return '';
    }
    
    public function get_max_fraction($arrAnswers) {
        $max = 0;
        
        foreach($arrAnswers as $answer) {
            if($answer->fraction > $max) {
                $max = $answer->fraction;
            }
        }
        
        return $max;
    }
}
