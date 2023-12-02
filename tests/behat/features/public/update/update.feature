Feature: Check public update page content

  Scenario: Go to Brands item
    Given I am on "/core/app/brands-index.php"
    And I follow "update-3"
    Then the response should contain "Under Armour"
    And the response should not contain "Gola"
    And I should see "Please edit the input values and submit to update the record."
    And I should see "Update Record"

    Given I am on "/core/app/brands-index.php"
    And I follow "update-4"
    Then the response should contain "Gola"
    And the response should not contain "Under Armour"
    And I should see "Please edit the input values and submit to update the record."
    And I should see "Update Record"



  Scenario: Update a record
    Given I am on "/core/app/brands-update.php?id=3"
    And I fill in "name" with "Under Armour updated"
    And I press "Edit"

    When I should see "View Record"
    And I should see "Under Armour updated"

    When I follow "Back to List"
    Then I should see "Under Armour updated"