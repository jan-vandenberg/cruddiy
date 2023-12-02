Feature: Check admin relations creation form

  Scenario: Check unicity display of existing relations
    Given I am on "/core/relations.php"
    Then I should not see "Parse error"
    And I should not see "Fatal error"

    And I should see "products_ibfk_1" only once




  Scenario: Check that we can add a relation
    Given I am on "/core/relations.php"

    And I select "products (supplier_id)" from "fk"
    And I select "suppliers (id)" from "primary"
    And I select "On Delete: Restrict" from "ondelete"
    And I select "On Update: Restrict" from "onupdate"

    And I press "Create relation"
    Then I should see "products_ibfk_2"
    And I press "Continue CRUD Creation Process"

    Then I should see "All Available Tables"
    And I should not see "Parse error"
    And I should not see "Fatal error"