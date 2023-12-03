Feature: Check the generator execution

  Scenario: Verify errors and execution
    And I should see "Deleting existing files"
    And I should see "Table: The Brands"
    And I should see "Table: The Products"
    And I should see "Table: The Suppliers"

    And I should not see "Parse error"
    And I should not see "Fatal error"

    And I should see "Your app has been created!"