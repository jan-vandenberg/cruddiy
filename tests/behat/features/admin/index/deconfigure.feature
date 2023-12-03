Feature: Reset the installation

  @deconfigure
  Scenario: No existing credentials
    Given I am on "/core/index.php"
    Then the "server" field should contain ""
    And the "database" field should contain ""
    And the "username" field should contain ""
    And the "password" field should contain ""
    And the "appname" field should contain ""
    And the "language" field should contain "en"
