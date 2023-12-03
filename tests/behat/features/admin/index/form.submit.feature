Feature: The form should be pre-field

  Scenario: Look for config file variables

    Given I am on "/core/index.php"
    And I press "Submit"
    Then I should not see "Access denied"