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

  @javascript
  Scenario: Preview a TCS reasoning question, created with all default values.
    Given I choose "Preview" action for "TCS-001" in the question bank
    #And I switch to "questionpreview" window
    And I expand all fieldsets
    When I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    Then I should see "Situation"
    And I should see "Here is the question"
    And I should see "This question is outside my field of competence"
    And I should see "Hypothesis label"
    And I should see "The hypothesis is..."
    And I should see "New information label"
    And I should see "The new information is..."
    And I should see "Comments label"
    # Check with no choice selected.
    And I press "Check"
    And I should see "Please select an answer."
    And I click on "Severely weakened" "radio"
    # Check with no comment entered.
    And I press "Check"
    And I should see "Please enter a comment."
    And I set the field "Comments label" to "Comment 1"
    # Check with the choice selected and the comment entered.
    And I press "Check"
    And I should see "The most popular answer is: Strongly reinforced"
    And I should see that "1" panelists have answered "Severely weakened" for question "1"
    And I should see "Feedback for choice 1" for answer "Severely weakened" of question "1"
    And I should see that "2" panelists have answered "Weakened" for question "1"
    And I should see "Feedback for choice 2" for answer "Weakened" of question "1"
    And I should see that "3" panelists have answered "Unchanged" for question "1"
    And I should see "Feedback for choice 3" for answer "Unchanged" of question "1"
    And I should see that "4" panelists have answered "Reinforced" for question "1"
    And I should see "Feedback for choice 4" for answer "Reinforced" of question "1"
    And I should see that "5" panelists have answered "Strongly reinforced" for question "1"
    And I should see no comments for answer "Strongly reinforced" of question "1"
    And I press "Start again"
    And I click on "This question is outside my field of competence" "checkbox"
    And the "Severely weakened" "radio" should be disabled
    And the "Weakened" "radio" should be disabled
    And the "Unchanged" "radio" should be disabled
    And the "Reinforced" "radio" should be disabled
    And the "Strongly reinforced" "radio" should be disabled
    And the "Comments label" "field" should be disabled
    And I press "Check"
    # User should still see the feedback.
    And I should see "The most popular answer is: Strongly reinforced"
    And I should see that "1" panelists have answered "Severely weakened" for question "1"

  @javascript
  Scenario: Preview a TCS judgment question.
    Given I choose "Preview" action for "TCS-002" in the question bank
    And I expand all fieldsets
    When I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    Then I should not see "Situation"
    And I should not see "Here is the question"
    And I should see "Hypothesis label"
    And I should see "The hypothesis is..."
    And I should not see "New information"
    And I should not see "Comments" in the ".tcs-container" "css_element"
    And I should not see "This question is outside my field of competence"
    And I click on "Answer 1" "radio"
    And I press "Check"
    And I should see "The most popular answer is: Answer 3"
    And I should see that "1" panelists have answered "Answer 1" for question "1"
    And I should see "Feedback for answer 1" for answer "Answer 1" of question "1"
    And I should see that "2" panelists have answered "Answer 2" for question "1"
    And I should see "Feedback for answer 2" for answer "Answer 2" of question "1"
    And I should see that "3" panelists have answered "Answer 3" for question "1"
    And I should see no comments for answer "Answer 3" of question "1"
