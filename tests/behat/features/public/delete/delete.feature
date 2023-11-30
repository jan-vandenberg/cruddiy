Feature: Check public delete page content

  Scenario: Check if there is a warning before deletion
    Given I am on "/core/app/brands-index.php"
    And I follow "delete-3"
    # TODO: display record on the delete page
    # Then the response should contain "Under Armour"
    # And the response should not contain "Gola"
    And I should see "Delete Record"
    And I should see "Are you sure"

  Scenario: Delete a Suppliers record
    Given I am on "/core/app/suppliers-index.php"
    And I follow "delete-2"
    And I should see "Delete Record"
    And I should see "Are you sure"
    When I press "Yes"
    Then I should see "1 results"

  Scenario: Check FK dependency
    Given I am on "/core/app/suppliers-index.php"
    And I follow "delete-1"
    And I should see "Delete Record"
    And I should see "Are you sure"
    When I press "Yes"
    Then I should see "Cannot delete or update a parent row"