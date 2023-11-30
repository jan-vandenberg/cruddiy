Feature: Check admin index form submission

  Scenario:
    Given I am on "/core/index.php"
    Then I should not see "Parse error"
    And I should not see "Fatal error"

    And I fill in "server" with the environment variable "DB_HOST"
    And I fill in "database" with the environment variable "DB_BASE"
    And I fill in "username" with the environment variable "DB_USER"
    And I fill in "password" with the environment variable "DB_PASS"

    And I fill in "numrecordsperpage" with "1"
    And I fill in "appname" with "Cruddiy Tests"
    And I fill in "language" with "en"

    And I press "Submit"

    Then I should see "Existing Table Relations"
    And I should not see "Parse error"
    And I should not see "Fatal error"
