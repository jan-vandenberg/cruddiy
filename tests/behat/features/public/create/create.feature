Feature: Check public create page content

  Scenario: Add supplier
    Given I am on "/core/app/suppliers-index.php"
    Then I should see "The Suppliers List"
    And I should see "No records were found."

    When I follow "Add New Record"
    Then I should see "Add New Record"
    And I should see "Supplier name"

    When I fill in "name" with "Test Supplier Name 1"
    And I press "Create"
    Then I should see "Test Supplier Name 1"
    And I should see "View Record"

    When I follow "Back to List"
    Then I should see "1 results - Page 1 of 1"



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