Feature: Check admin tables page content

  Scenario:
    Given I am on "/core/tables.php"
    Then I should see "All Available Tables"
    And I should not see "Error"
    And I should not see "Warning"
    And I should not see "Fatal"
