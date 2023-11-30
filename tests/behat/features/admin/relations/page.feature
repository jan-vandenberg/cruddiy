Feature: Check admin relations page content

  Scenario:
    Given I am on "/core/relations.php"
    Then I should see "Existing Table Relations"
    And I should not see "Parse error"
    And I should not see "Fatal error"
