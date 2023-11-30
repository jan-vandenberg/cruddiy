Feature: Check public read page content

  Scenario: Go to Brands item
    Given I am on "/core/app/brands-index.php"
    And I follow "read-3"
    Then I should see "Under Armour"
    And I should see "Update Record"

    Given I am on "/core/app/brands-index.php"
    And I follow "read-4"
    Then I should see "Gola"
    And I should see "Update Record"