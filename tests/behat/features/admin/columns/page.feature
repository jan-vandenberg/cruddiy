Feature: Check admin columns page content

  @javascript
  Scenario:
    Given I am on "/core/index.php"
    Then I should see "Enter database information"
    And I should not see "Error"
    And I should not see "Warning"
    And I should not see "Fatal"