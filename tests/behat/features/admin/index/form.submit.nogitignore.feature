Feature: The form should be pre-field

  Scenario: Look for config file variables and uncheck gitignore

    And I uncheck "gitignore"

    And I press "Submit"
    Then I should not see "Access denied"