Feature: Check .gitignore file

  Scenario: Check confirmation message on generate
    And I should see either "already exists in .gitignore." or "added to .gitignore."

  Scenario: Check for the existence of destination subdirectory in .gitignore
    Given I have a .gitignore file
    Then I check for "app_cruddiy_tests/" in the .gitignore file
