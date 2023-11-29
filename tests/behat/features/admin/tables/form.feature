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

    Then I should see "All Available Columns"
    Then I should see "The Brands (brands)"
    Then I should see "The Products (products)"
    Then I should see "The Suppliers (suppliers)"