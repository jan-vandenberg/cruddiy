Feature: Check admin columns page

  Scenario: Check for errors on the Columns page
    Then I should see "All Available Columns"
    And I should not see "Parse error"
    And I should not see "Fatal error"

