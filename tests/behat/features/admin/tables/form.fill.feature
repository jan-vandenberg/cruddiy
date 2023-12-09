Feature: Check admin tables mapping form

  Scenario: Fill the tables form

    And the "generate-brands" checkbox should not be checked
    And the "generate-products" checkbox should not be checked
    And the "generate-suppliers" checkbox should not be checked

    And the "text-brands" field should contain ""
    And the "text-products" field should contain ""
    And the "text-suppliers" field should contain ""

    And I check "generate-brands"
    And I check "generate-products"
    And I check "generate-suppliers"

    And I fill in "text-brands" with "The Brands"
    And I fill in "text-products" with "The Products"
    And I fill in "text-suppliers" with "The Suppliers"

    When I press "Select columns from tables"

    Then I should see "Select existing app"
    And I select "app_cruddiy_tests" from "configDir"
    And I press "Load Configuration"

    # Then I log the content of the page