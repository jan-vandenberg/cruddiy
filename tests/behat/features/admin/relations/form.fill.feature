Feature: Check admin relations creation form

  Scenario: Check that we can add a relation
    Given I am on "/core/relations.php"

    And I select "products (supplier_id)" from "fk"
    And I select "suppliers (id)" from "primary"
    And I select "On Delete: Restrict" from "ondelete"
    And I select "On Update: Restrict" from "onupdate"

    And I press "Create relation"
    Then I should see "products_ibfk_2"
    And I press "Continue CRUD Creation Process"