Feature: Check admin schema import form

  Scenario: Check link to import schema page
    Given I am on "/core/relations.php"
    And I follow "Import Schema or Dump"
    Then I should see "Import schema"

  Scenario: Check schema import form
    Given I am on "/core/schema.php"
    When I select "../schema/Tests.sql" from "schemaFile"
    And I press "Import schema"
    Then I should see "Existing Table Relations"