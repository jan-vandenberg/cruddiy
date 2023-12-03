Feature: Check the generator result

  Scenario: Look for errors and data continuity
    When I follow "Go to your app"

    Then I should see "Cruddiy Tests"

    And I should not see "Parse error"
    And I should not see "Fatal error"

# continue to the Public test suite.