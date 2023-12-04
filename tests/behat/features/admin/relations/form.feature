Feature: Check admin relations creation form

  Scenario: Check unicity display of existing relations
    Given I am on "/core/relations.php"
    Then I should not see "Parse error"
    And I should not see "Fatal error"

    And I should see "products_ibfk_1" only once