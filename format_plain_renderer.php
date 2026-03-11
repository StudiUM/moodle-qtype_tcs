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
 * tcs question format plain renderer class.
 *
 * @package qtype_tcs
 * @copyright  2020 Université  de Montréal.
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * An tcs format renderer for tcs where the student should use a plain input box.
 *
 * @package qtype_tcs
 * @copyright  2020 Université  de Montréal.
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_tcs_format_plain_renderer extends plugin_renderer_base {
    /**
     * Return the HTML for the textarea.
     *
     * @param string $response content of the textarea
     * @param array $attributes textarea attributes
     * @return string the HTML for the textarea.
     */
    protected function textarea($response, $attributes) {
        $attributes['class'] = $this->class_name() . ' qtype_tcs_response';
        $attributes['rows'] = 7;
        return html_writer::tag('textarea', s($response), $attributes);
    }

    /**
     * Return class name.
     *
     * @return string class name
     */
    protected function class_name() {
        return 'qtype_tcs_plain';
    }

    /**
     * Return the HTML for the textarea.
     *
     * @param string $name the name of the textarea
     * @param question_attempt $qa
     * @param question_attempt_step $step
     * @param array $attributes textarea attributes
     * @return string the HTML for the textarea.
     */
    public function response_area_input($name, $qa, $step, $attributes = []) {
        $inputname = $qa->get_qt_field_name($name);
        $attributes += ['name' => $inputname, 'id' => $inputname];
        return $this->textarea($step->get_qt_var($name), $attributes) .
                html_writer::empty_tag('input', ['type' => 'hidden',
                    'name' => $inputname . 'format', 'value' => FORMAT_PLAIN]);
    }
}
