Feature: Submit the "Generate pages" form

  Scenario: Run the generator

    And I uncheck "keep_startpage"
    And I uncheck "append_links"

    And I press "Generate Pages"

    Then I should not see "Parse error"
    And I should not see "Fatal error"