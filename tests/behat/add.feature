@qtype @qtype_tcs
Feature: Test creating a TCS question
  As a teacher
  In order to test my students
  I need to be able to create a TCS question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  Scenario: Create a TCS reasoning question, created with all default values.
    Given I press "Create a new question ..."
    And I set the field "Script concordance test" to "1"
    When I click on "Add" "button"
    Then the following fields match these values:
      | id_labelsituation               | Situation                     |
      | id_labelhypothisistext          | Option                        |
      | id_labeleffecttext              | New information               |
      | id_labelnewinformationeffect    | Your hypothesis or option is  |
      | id_labelfeedback                | Comments                      |
    And I set the following fields to these values:
      | Question name            | TCS-001                            |
      | Question text            | Here is the question               |
      | General feedback         | General feedback for the question  |
      | showquestiontext         | Yes                                |
      | id_hypothisistext        | The hypothesis is...               |
      | id_effecttext            | The new information is...          |
      | showfeedback             | Yes                                |
      | id_fraction_0            | 3                                  |
      | id_fraction_1            | 2                                  |
      | id_fraction_2            | 0                                  |
      | id_fraction_3            | 2                                  |
      | id_fraction_4            | 0                                  |
      | id_feedback_0            | Feedback for choice 1              |
      | id_feedback_1            | Feedback for choice 2              |
      | id_feedback_2            |                                    |
      | id_feedback_3            | Feedback for choice 4              |
      | id_feedback_4            |                                    |
    And I press "id_submitbutton"
    And I should see "TCS-001"


  Scenario: Create a TCS judgment question.
    When I add a "Script concordance test" question filling the form with:
      | Question name            | TCS-002                            |
      | Question text            | Here is the question               |
      | General feedback         | General feedback for the question  |
      | showquestiontext         | No                                 |
      | labelhypothisistext      | Hypothesis label                   |
      | id_hypothisistext        | The hypothesis is...               |
      | labeleffecttext          |                                    |
      | id_effecttext            |                                    |
      | labelnewinformationeffect| Your hypothesis or option is       |
      | showfeedback             | No                                 |
      | Choice 1                 | Answer 1                           |
      | Choice 2                 | Answer 2                           |
      | Choice 3                 | Answer 3                           |
      | Choice 4                 |                                    |
      | Choice 5                 |                                    |
      | id_fraction_0            | 2                                  |
      | id_fraction_1            | 2                                  |
      | id_fraction_2            | 0                                  |
      | id_feedback_0            | Feedback for answer 1              |
      | id_feedback_1            | Feedback for answer 2              |
      | id_feedback_2            |                                    |
    Then I should see "TCS-002"
