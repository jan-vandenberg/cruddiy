Feature: Check admin tables mapping form

  Scenario: Submit the form with preconfigured values
    And I press "Select columns from tables"

    And I select "app_cruddiy_tests" from "configDir"
    And I press "Load Configuration"

    # Then I log the content of the page