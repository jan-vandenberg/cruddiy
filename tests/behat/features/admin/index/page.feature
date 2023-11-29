Feature: Check admin index page content

  Scenario:
    Given I am on "/core/index.php"
    Then I should see "Enter database information"
