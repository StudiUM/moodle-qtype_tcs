@qtype @qtype_tcs
Feature: Test exporting tcs questions
  As a teacher
  In order to be able to reuse my tcs questions
  I need to export them

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

  @javascript
  Scenario: Export a tcs question
    When I navigate to "Question bank > Export" in current page administration
    And I set the field "id_format_xml" to "1"
    And I press "Export questions to file"
    Then following "click here" should download between "4650" and "4900" bytes
    And I log out
