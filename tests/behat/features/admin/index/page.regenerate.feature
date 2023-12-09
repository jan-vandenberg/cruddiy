Feature: Check admin index page content when regenerating CRUD

    Scenario: Existing configuration was detected
        Given I am on "/core/index.php"
        Then I should see "Select existing app"



    Scenario: Restart configuration from scratch
        Given I am on "/core/index.php"
            And I follow "Restart from scratch"
            Then I should see "Enter database information"



    Scenario: Re-using an existing configuration
        Given I am on "/core/index.php"
            And I select "app_cruddiy_tests" from "configDir"
            And I press "Load Configuration"

            Then the "server" field should contain "localhost"
            And the "database" field should contain "cruddiy_tests"
            And the "username" field should contain "root"
            And the "password" field should contain "root"
            And the "numrecordsperpage" field should contain "2"
            And the "appname" field should contain "Cruddiy Tests"
            And the "destination" field should contain "app_cruddiy_tests"
            And the "gitignore" checkbox should be checked
            And the "language" field should contain "en"

