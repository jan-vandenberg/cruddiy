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


  Scenario: Update a record with a file upload
    Given I am on "/core/app/products-update.php?id=2"
    And I fill in "product_name" with "Test Product Name 2 with updated file"
    And I attach the file "./tests/assets/cruddiy_test_image.jpg" to "packshot_file"
    And I press "Edit"

    Then I should see "Test Product Name 2 with updated file"
    And I should see "cruddiy_test_image.jpg"
    And I follow "Back to List"
    And I should see a ".uploaded_file" element
    And I follow "link_packshot_file"
    Then the response status code should be 200


  # @javascript
  Scenario: Preserve existing attachment when updating a record without picking a new file
    Given I am on "/core/app/products-update.php?id=2"
    And I fill in "product_name" with "Test Product Name 2 with preserved file"
    And I press "Edit"

    Then I should see "Test Product Name 2 with preserved file"
    And I should see "cruddiy_test_image.jpg"
    And I follow "Back to List"
    And I should see a ".uploaded_file" element
    And I follow "link_packshot_file"
    Then the response status code should be 200



  Scenario: Successfully updating a product file
    Given I am on "/core/app/products-update.php?id=2"
    When I attach the file "./tests/assets/cruddiy_test_image.jpg" to "packshot_file"
    And I press "Edit"
    Then I should see "cruddiy_test_image.jpg"

  Scenario: Attempting to upload a product file but leaving the file input empty
    Given I am on "/core/app/products-update.php?id=2"
    When I press "Edit"
    Then I should see "cruddiy_test_image.jpg"

  Scenario: Using a backup file for a product when no file is uploaded
    Given I am on "/core/app/products-update.php?id=2"
    When I attach the file "" to "packshot_file"
    And I press "Edit"
    Then I should see "cruddiy_test_image.jpg"

  Scenario: Deleting an uploaded product file
    Given I am on "/core/app/products-update.php?id=2"
    When I check "cruddiy_delete_packshot_file"
    And I press "Edit"
    Then I should not see "cruddiy_test_image.jpg"

  Scenario: Attempting to delete a product file that does not exist
    Given I am on "/core/app/products-update.php?id=2"
    Then I should not see a "cruddiy_backup_packshot_file" element

  Scenario: Successfully updating a new product file
    Given I am on "/core/app/products-update.php?id=2"
    When I attach the file "./tests/assets/cruddiy_test_image.jpg" to "packshot_file"
    And I press "Edit"
    Then I should see "cruddiy_test_image.jpg"