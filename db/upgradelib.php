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
 * Upgrade library code for the tcs question type.
 *
 * @package    qtype
 * @subpackage tcs
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Class for converting attempt data for tcs questions when upgrading
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  based on work by 2014 Julien Girardot <julien.girardot@actimage.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs_qe2_attempt_updater extends question_qtype_attempt_updater {
    protected $order;

    public function is_blank_answer($state) {
        // Blank tcs answers are not empty strings, they rather end in a colon.
        return empty($state->answer) || substr($state->answer, -1) == ':';
    }

    public function right_answer() {
        $max = 0;
        $rightanswer = null;

        foreach ($this->question->options->answers as $answer) {
            if ($answer->fraction > $max) {
                $max = $answer->fraction;
                $rightanswer = $answer;
            }
        }

        if (!empty($rightanswer)) {
            return $this->to_text($rightanswer->answer);
        }

        return -1;
    }

    protected function explode_answer($answer) {
        if (strpos($answer, ':') !== false) {
            list($order, $responses) = explode(':', $answer);
            return $responses;
        } else {
            // Sometimes, a bug means that a state is missing the <order>: bit,
            // We need to deal with that.
            $this->logger->log_assumption("Dealing with missing order information
                    in attempt at tcs question {$this->question->id}");
            return $answer;
        }
    }

    public function response_summary($state) {
        $responses = $this->explode_answer($state->answer);

        if (is_numeric($responses)) {
            if (array_key_exists($responses, $this->question->options->answers)) {
                return $this->to_text($this->question->options->answers[$responses]->answer);
            } else {
                $this->logger->log_assumption("Dealing with a place where the
                        student selected a choice that was later deleted for
                        tcs question {$this->question->id}");
                return '[CHOICE THAT WAS LATER DELETED]';
            }
        } else {
            return null;
        }
    }

    public function was_answered($state) {
        $responses = $this->explode_answer($state->answer);
        return is_numeric($responses);
    }

    public function set_first_step_data_elements($state, &$data) {
        if (!$state->answer) {
            return;
        }
        list($order, $responses) = explode(':', $state->answer);
        $data['_order'] = $order;
        $this->order = explode(',', $order);
    }

    public function supply_missing_first_step_data(&$data) {
        $data['_order'] = implode(',', array_keys($this->question->options->answers));
    }

    public function set_data_elements_for_step($state, &$data) {
        $responses = $this->explode_answer($state->answer);
        if (is_numeric($responses)) {
            $flippedorder = array_combine(array_values($this->order), array_keys($this->order));
            if (array_key_exists($responses, $flippedorder)) {
                $data['answer'] = $flippedorder[$responses];
            } else {
                $data['answer'] = '-1';
            }
        }
    }
}
