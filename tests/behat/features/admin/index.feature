Feature: Check admin index page content

  Scenario: Checking content on the admin homepage
    Given I am on "/core/index.php"
    Then I should see "Enter database information"
