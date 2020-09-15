@qtype @qtype_tcs
Feature: Preview a TCS question
  As a teacher
  In order to check my TCS questions will work for students
  I need to preview them

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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name       | template        |
      | Test questions   | tcs       | TCS-001    | reasoning       |
      | Test questions   | tcs       | TCS-002    | judgment        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript @_switch_window
  Scenario: Preview a TCS reasoning question, created with all default values.
    Given I choose "Preview" action for "TCS-001" in the question bank
    When I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    Then I should see "Situation"
    And I should see "Here is the question"
    And I should see "Hypothesis label"
    And I should see "The hypothesis is..."
    And I should see "New information label"
    And I should see "The new information is..."
    And I should see "Comments label"
    And I click on "Severely weakened" "radio"
    And I press "Check"
    And I should see "The most popular answer is: Severely weakened"
    And I should see that "3" panelists have answered "Severely weakened" for question "1"
    And I should see "Feedback for choice 1" for answer "Severely weakened" of question "1"
    And I should see that "2" panelists have answered "Weakened" for question "1"
    And I should see "Feedback for choice 2" for answer "Weakened" of question "1"
    And I should see that "0" panelists have answered "Unchanged" for question "1"
    And I should see no comments for answer "Unchanged" of question "1"
    And I should see that "2" panelists have answered "Reinforced" for question "1"
    And I should see "Feedback for choice 4" for answer "Reinforced" of question "1"
    And I should see that "0" panelists have answered "Strongly reinforced" for question "1"
    And I should see no comments for answer "Strongly reinforced" of question "1"
    And I switch to the main window


  @javascript @_switch_window
  Scenario: Preview a TCS judgment question.
    Given I choose "Preview" action for "TCS-002" in the question bank
    When I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    Then I should not see "Situation"
    And I should not see "Here is the question"
    And I should see "Hypothesis label"
    And I should see "The hypothesis is..."
    And I should not see "New information"
    And I should not see "Comments"
    And I click on "Answer 1" "radio"
    And I press "Check"
    And I should see "The most popular answer is: Answer 1, Answer 2"
    And I should see that "2" panelists have answered "Answer 1" for question "1"
    And I should see "Feedback for answer 1" for answer "Answer 1" of question "1"
    And I should see that "2" panelists have answered "Answer 2" for question "1"
    And I should see "Feedback for answer 2" for answer "Answer 2" of question "1"
    And I should see that "0" panelists have answered "Answer 3" for question "1"
    And I should see no comments for answer "Answer 3" of question "1"
    And I switch to the main window
