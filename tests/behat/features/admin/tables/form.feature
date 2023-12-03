Feature: Check admin tables mapping form

  Scenario: Name and select tables
    Given I am on "/core/tables.php"
    Then I should not see "Parse error"
    And I should not see "Fatal error"

    And I fill in "text-brands" with "The Brands"
    And I check "generate-brands"

    And I fill in "text-products" with "The Products"
    And I check "generate-products"

    And I fill in "text-suppliers" with "The Suppliers"
    And I check "generate-suppliers"

    And I press "Select columns from tables"