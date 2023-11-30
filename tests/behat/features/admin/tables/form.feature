Feature: Check admin tables mapping form

  Scenario: Check checkboxes
    Given I am on "/core/tables.php"
    Then I should not see "Error"
    And I should not see "Warning"
    And I should not see "Fatal"

    And I fill in "table[0][tabledisplay]" with "The Brands"
    And I check "checkboxes-0"

    And I fill in "table[1][tabledisplay]" with "The Products"
    And I check "checkboxes-1"

    And I fill in "table[2][tabledisplay]" with "The Suppliers"
    And I check "checkboxes-2"

    And I press "Select columns from tables"

  Scenario: Check for errors on the Columns page
    Then I should see "All Available Columns"
    And I should see "The Brands (brands)"
    And I should see "The Products (products)"
    And I should see "The Suppliers (suppliers)"

  Scenario: Check for form continuity on the Columns page
    And I should see "Table: The Brands (brands)"
    And I should see "Table: The Products (products)"
    And I should see "Table: The Suppliers (suppliers)"