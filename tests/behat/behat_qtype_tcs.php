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
 * Step definitions for Concordance.
 *
 * @package    qtype_tcs
 * @category   test
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2020 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Step definitions for Concordance.
 *
 * @package    qtype_tcs
 * @category   test
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2020 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_tcs extends behat_base {
    /**
     * Check the number of panelists who chose the specified answer choice, for the specified question.
     *
     * @Given I should see that :nbpanelists panelists have answered :answer for question :questionnb
     * @param string $nbpanelists The number of panelists who chose this answer.
     * @param string $answer The answer the panelist chose.
     * @param int $questionnb The question number.
     */
    public function i_should_see_that_panelists_have_answered_for_question($nbpanelists, $answer, $questionnb) {
        $xpath = "(//div[contains(@class,'tcs')])[$questionnb]//div[contains(@class,'formulation')]//div[contains(@class,'answer')]"
            . "//label[.='$answer']/following::span[not(contains(@class,'gauge'))][2]";
        $this->execute("behat_general::assert_element_contains_text",
            array($nbpanelists, $xpath, "xpath_element")
        );
    }

    /**
     * Check that the feedback appears for the specified answer choice, for the specified question.
     *
     * @Given I should see :comment for answer :answer of question :questionnb
     * @param string $comment The comment entered by the panelist.
     * @param string $answer The answer the panelist chose.
     * @param int $questionnb The question number.
     */
    public function i_should_see_for_answer_of_question($comment, $answer, $questionnb) {
        $xpath = "(//div[contains(@class,'tcs')])[$questionnb]//div[contains(@class,'specificfeedback')]/p[contains(.,'$answer')]"
            . "/following::div[1]";
        $this->execute("behat_general::assert_element_contains_text",
            array($comment, $xpath, "xpath_element")
        );
    }

    /**
     * Check that no panelists made comments for the specified answer choice, for the specified question.
     *
     * @Given I should see no comments for answer :answer of question :questionnb
     * @param string $answer The answer the panelist chose.
     * @param int $questionnb The question number.
     */
    public function i_should_see_no_comments_for_answer_of_question($answer, $questionnb) {
        $xpath = "(//div[contains(@class,'tcs')])[$questionnb]//div[contains(@class,'specificfeedback')]/p[contains(.,'$answer')]";
        $this->execute("behat_general::should_not_exist", array($xpath, "xpath_element"));
    }
}
