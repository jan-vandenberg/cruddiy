Feature: Check admin relations page content

  Scenario: No errors on page

    Then I should not see "Parse error"
    And I should not see "Fatal error"
    And I should see "Existing Table Relations"
