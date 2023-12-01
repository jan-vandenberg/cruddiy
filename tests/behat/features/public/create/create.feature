Feature: Check public create page content

  Scenario: Add supplier
    Given I am on "/core/app/suppliers-index.php"
    Then I should see "The Suppliers List"
    And I should see "No records were found."

    # Will be used in Products creation
    When I follow "Add New Record"
    Then I should see "Add New Record"
    And I should see "Supplier name"

    When I fill in "name" with "Test Supplier Name 1"
    And I press "Create"
    Then I should see "Test Supplier Name 1"
    And I should see "View Record"

    When I follow "Back to List"
    Then I should see "1 results - Page 1 of 1"


    # Will be used in Delete test
    When I follow "Add New Record"
    Then I should see "Add New Record"
    And I should see "Supplier name"

    When I fill in "name" with "Test Supplier Name 2"
    And I press "Create"
    Then I should see "Test Supplier Name 2"
    And I should see "View Record"

    When I follow "Back to List"
    Then I should see "2 results - Page 1 of 1"



  Scenario: Add product
    Given I am on "/core/app/products-index.php"
    Then I should see "The Products List"

    When I follow "Add New Record"
    Then I should see "Add New Record"

    When I fill in "ean" with "1234567890123"
    And I fill in "product_name" with "Test Product Name 1"
    And I select "Nike" from "brand_id"
    And I select "Test Supplier Name 1" from "supplier_id"
    And I fill in "visual_url" with "https://www.image1.com"
    And I fill in "packaging_details" with "Vestibulum ullamcorper mauris at ligula.\nProin pretium, leo ac pellentesque mollis."
    And I fill in "recycled_material_incorporation" with "123.45"
    And I select "True" from "hazardous_substance_presence"
    And I press "Create"

    Then I should see "Test Product Name 1"
    And I should see "View Record"

    When I follow "Back to List"
    Then I should see "1 results - Page 1 of 1"


  Scenario: Add product with upload file
    Given I am on "/core/app/products-create.php"

    When I fill in "ean" with "2345678901234"
    And I fill in "product_name" with "Test Product Name 2 with file"
    And I select "Adidas" from "brand_id"
    And I select "Test Supplier Name 1" from "supplier_id"
    And I fill in "visual_url" with "https://www.image2.com"
    And I fill in "packaging_details" with "Lorem ipsum dolor sit amet, nec consectuter adisciping elis."
    And I fill in "recycled_material_incorporation" with "0"
    And I select "False" from "hazardous_substance_presence"
    And I attach the file "./tests/assets/cruddiy_test_image.jpg" to "packshot_file"
    And I press "Create"

    Then I should see "Test Product Name 2 with file"
    And I should see "cruddiy_test_image.jpg"
    And I follow "Back to List"
    And I should see a ".uploaded_file" element
    And I follow "link_packshot_file"
    Then the response status code should be 200