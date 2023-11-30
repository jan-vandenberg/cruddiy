Feature: Check admin tables and columns pages

  Scenario: Check for errors on Tables page
    Given I am on "/core/tables.php"
    Then I should see "All Available Tables"
    And I should not see "Parse error"
    And I should not see "Fatal error"



  Scenario: Check for errors on Columns page
    Given I am on "/core/columns.php"
    Then I should see "All Available Columns"
    And I should not see "Parse error"
    And I should not see "Fatal error"