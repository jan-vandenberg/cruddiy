Feature: Check admin tables and columns pages

  Scenario: Check for errors on Tables page
    Given I am on "/core/tables.php"
    Then I should see "All Available Tables"
    And I should not see "Error"
    And I should not see "Warning"
    And I should not see "Fatal"

  Scenario: Check for errors on Columns page
    Given I am on "/core/columns.php"
    Then I should see "All Available Columns"
    And I should not see "Error"
    And I should not see "Warning"
    And I should not see "Fatal"
