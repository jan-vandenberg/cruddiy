Feature: Check admin relations page content

  Scenario:
    Given I am on "/core/relations.php"
    Then I should see "Existing Table Relations"
    And I should not see "Error"
    And I should not see "Warning"
    And I should not see "Fatal"
