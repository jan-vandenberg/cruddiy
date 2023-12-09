Feature: Check admin tables page

  @reconfigure
  Scenario: Check for preconfiguration from config-tables-columns.php
    Given I am on "/core/tables.php"

    And I select "app_cruddiy_tests" from "configDir"
    And I press "Load Configuration"

    Then the "text-brands" field should contain "The Brands"
    And the "text-products" field should contain "The Products"
    And the "text-suppliers" field should contain "The Suppliers"

    And the "generate-brands" checkbox should be checked
    And the "generate-products" checkbox should be checked
    And the "generate-suppliers" checkbox should be checked