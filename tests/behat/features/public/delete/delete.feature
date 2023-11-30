Feature: Check public delete page content

  Scenario: Go to Brands item
    Given I am on "/core/app/brands-index.php"
    And I follow "delete-3"
    # TODO: display record on the delete page
    # Then the response should contain "Under Armour"
    # And the response should not contain "Gola"
    And I should see "Delete Record"
    And I should see "Are you sure"

    Given I am on "/core/app/brands-index.php"
    And I follow "delete-4"
    # TODO: display record on the delete page
    # Then the response should contain "Gola"
    # And the response should not contain "Under Armour"
    And I should see "Delete Record"
    And I should see "Are you sure"
    When I follow "Yes"
    Then I should see "6 results"



  # Scenario: Update a record
  #   Given I am on "/core/app/brands-update.php?id=3"
  #   And I fill in "name" with "Under Armour updated"
  #   And I press "Edit"

  #   When I should see "View Record"
  #   And I should see "Under Armour updated"

  #   When I follow "Back to List"
  #   Then I should see "Under Armour updated"