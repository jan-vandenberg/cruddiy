Feature: Check admin relations page content

  Scenario:
    Given I am on "/core/relations.php"
    Then I should see "Existing Table Relations"
