Feature: Fill the form

  Scenario: Inject environment variables

    Given I am on "/core/index.php"
    And I fill in "server" with the environment variable "DB_HOST"
    And I fill in "database" with the environment variable "DB_BASE"
    And I fill in "username" with the environment variable "DB_USER"
    And I fill in "password" with the environment variable "DB_PASS"

    And I fill in "numrecordsperpage" with "2"
    And I fill in "appname" with "Cruddiy Tests"
    And I fill in "language" with "en"

    And I press "Submit"
    Then I should not see "Access denied"