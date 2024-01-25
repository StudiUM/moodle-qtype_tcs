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
 * Tcs question form.
 *
 * @module     qtype_tcs/tcs
 * @copyright  2021 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'],
    function($) {

        var SELECTORS = {
            RADIOANSWER: ".answer input[type='radio']",
            TEXTAREAFEEDBACK: ".answerfeedback textarea"
        };

        /**
         * Constructor
         *
         * @param {String} outsidefieldcompetenceid
         * @param {String} inputhidden
         */
        var TcsQuestion = function(outsidefieldcompetenceid, inputhidden) {

            this.attachEventListeners(outsidefieldcompetenceid, inputhidden);
        };

        /**
         * Private method
         *
         * @param {String} checkboxid
         * @param {String} inputhidden
         * @method attachEventListeners
         * @private
         */
        TcsQuestion.prototype.attachEventListeners = function(checkboxid, inputhidden) {
            $('input[id="' + checkboxid + '"]').on('change', function() {
                // Enable/disable answer and feedback.
                var questionform = $(this).parent(".tcs-container");
                var answers = questionform.find(SELECTORS.RADIOANSWER);
                var feedback = questionform.find(SELECTORS.TEXTAREAFEEDBACK);
                if ($(this).is(":checked")) {
                    $('input[id="' + inputhidden + '"]').val(1);
                    answers.prop('disabled', true);
                    feedback.prop('disabled', true);
                } else {
                    $('input[id="' + inputhidden + '"]').val(0);
                    answers.prop('disabled', false);
                    feedback.prop('disabled', false);
                }
            });
        };

        return /** @alias module:qtype_tcs/tcs */ {
            // Public variables and functions.

            /**
             * Initialise.
             *
             * @method init
             * @param {String} outsidefieldcompetenceid
             * @param {String} inputhidden
             * @return {TcsQuestion}
             */
            'init': function(outsidefieldcompetenceid, inputhidden) {
                return new TcsQuestion(outsidefieldcompetenceid, inputhidden);
            }
        };
    });
