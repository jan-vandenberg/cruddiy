Feature: Check admin index page content

  Scenario: No errors on page
    Given I am on "/core/index.php"
    Then I should see "Enter database information"
    And I should not see "Parse error"
    And I should not see "Fatal error"
