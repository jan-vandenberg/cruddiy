Feature: Check admin tables mapping form

  Scenario: Fill the tables form
    Given I am on "/core/tables.php"

    And the "generate-brands" checkbox should not be checked
    And the "generate-products" checkbox should not be checked
    And the "generate-suppliers" checkbox should not be checked

    And the "text-brands" field should contain ""
    And the "text-products" field should contain ""
    And the "text-suppliers" field should contain ""

    And I fill in "text-brands" with "The Brands"
    And I check "generate-brands"

    And I fill in "text-products" with "The Products"
    And I check "generate-products"

    And I fill in "text-suppliers" with "The Suppliers"
    And I check "generate-suppliers"

    And I press "Select columns from tables"
    And I should see "The Suppliers (suppliers)"