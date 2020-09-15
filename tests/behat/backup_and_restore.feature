@qtype @qtype_tcs
Feature: Test duplicating a quiz containing a TCS question
  As a teacher
  In order re-use my courses containing TCS questions
  I need to be able to backup and restore them

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype   | name     | template    |
      | Test questions   | tcs     | TCS-001  | reasoning   |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | TCS-001   | 1 |
    And I log in as "admin"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Backup and restore a course containing a TCS question
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I navigate to "Question bank" in current page administration
    And I choose "Edit question" action for "TCS-001" in the question bank
    Then the following fields match these values:
      | Question name                 | TCS-001                            |
      | Question text                 | Here is the question               |
      | General feedback              | General feedback for the question  |
      | showquestiontext              | Yes                                |
      | id_labelsituation             | Situation label                    |
      | id_labelhypothisistext        | Hypothesis label                   |
      | id_hypothisistext             | The hypothesis is...               |
      | id_labeleffecttext            | New information label              |
      | id_effecttext                 | The new information is...          |
      | id_labelnewinformationeffect  | Your hypothesis or option is       |
      | id_labelfeedback              | Comments label                     |
      | showfeedback                  | Yes                                |
      | id_fraction_0                 | 3                                  |
      | id_fraction_1                 | 2                                  |
      | id_fraction_2                 | 0                                  |
      | id_fraction_3                 | 2                                  |
      | id_fraction_4                 | 0                                  |
      | id_feedback_0                 | Feedback for choice 1              |
      | id_feedback_1                 | Feedback for choice 2              |
      | id_feedback_2                 |                                    |
      | id_feedback_3                 | Feedback for choice 4              |
      | id_feedback_4                 |                                    |

