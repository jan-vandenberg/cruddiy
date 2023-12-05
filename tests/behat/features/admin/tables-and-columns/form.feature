Feature: Check admin tables mapping form

  Scenario: Check checkboxes
    Given I am on "/core/tables.php"
    Then I should not see "Parse error"
    And I should not see "Fatal error"

    And I fill in "table[0][tabledisplay]" with "The Brands"
    And I check "checkboxes-0"

    And I fill in "table[1][tabledisplay]" with "The Products"
    And I check "checkboxes-1"

    And I fill in "table[2][tabledisplay]" with "The Suppliers"
    And I check "checkboxes-2"

    And I press "Select columns from tables"



  Scenario: Check for errors on the Columns page
    Then I should see "All Available Columns"
    And I should see "The Brands (brands)"
    And I should see "The Products (products)"
    And I should see "The Suppliers (suppliers)"

  Scenario: Check for form continuity on the Columns page
    And I should see "Table: The Brands (brands)"
    And I should see "Table: The Products (products)"
    And I should see "Table: The Suppliers (suppliers)"



  Scenario: Check for default checkboxes and states, fill the Columns form
    # brands

    # id
    When I check "checkboxes_brands-0"
    And I check "checkboxes_brands-0-2"

    # name
    And I fill in "brandscolumns[1][columndisplay]" with "Brand name"
    And I check "brandscolumns[1][columnvisible]"
    And I check "brandscolumns[1][columninpreview]"



    # products

    # id
    And I check "productscolumns[0][columnvisible]"
    Then I should not see a "#productscolumns\[0\]\[columninpreview\]" element

    # ean
    And I fill in "productscolumns[1][columndisplay]" with "EAN-13"
    And I check "productscolumns[1][columnvisible]"
    Then I should not see a "#productscolumns\[1\]\[columninpreview\]" element

    # product_name
    And I fill in "productscolumns[2][columndisplay]" with "Name"
    And I check "productscolumns[2][columnvisible]"
    Then I should not see a "#productscolumns\[2\]\[columninpreview\]" element

    # brand_id
    And I fill in "productscolumns[3][columndisplay]" with "Brand name"
    And I check "productscolumns[3][columnvisible]"
    Then I should not see a "#productscolumns\[3\]\[columninpreview\]" element

    # supplier_id
    And I fill in "productscolumns[4][columndisplay]" with "Supplier name"
    And I check "productscolumns[4][columnvisible]"
    Then I should not see a "#productscolumns\[4\]\[columninpreview\]" element

    # visual_url
    And I fill in "productscolumns[5][columndisplay]" with "Photo URL"
    And I check "productscolumns[5][columnvisible]"
    Then I should not see a "#productscolumns\[5\]\[columninpreview\]" element

    # packaging_details
    And I fill in "productscolumns[6][columndisplay]" with "Packaging details"
    And I check "productscolumns[6][columnvisible]"
    Then I should not see a "#productscolumns\[6\]\[columninpreview\]" element

    # recycled_material_incorporation
    And I fill in "productscolumns[7][columndisplay]" with "Contains recycled material?"
    And I check "productscolumns[7][columnvisible]"
    Then I should not see a "#productscolumns\[7\]\[columninpreview\]" element

    # hazardous_substance_presence
    And I fill in "productscolumns[8][columndisplay]" with "Contains hazardous substance?"
    And I check "productscolumns[8][columnvisible]"
    Then I should not see a "#productscolumns\[8\]\[columninpreview\]" element


    # suppliers

    # id
    And I check "supplierscolumns[0][columnvisible]"
    And I check "supplierscolumns[0][columninpreview]"

    # name
    And I fill in "supplierscolumns[1][columndisplay]" with "Supplier name"
    And I check "supplierscolumns[1][columnvisible]"
    And I check "supplierscolumns[1][columninpreview]"




  Scenario: Check auto-detection of input type=file elements (guesslist)
    # brands
    And I should not see a "file_brands-0" element
    And the checkbox "file_brands-1" should not be checked

    # products
    And I should not see a "file_products-0" element
    And the checkbox "file_products-1" should not be checked
    And the checkbox "file_products-2" should not be checked
    And I should not see a "file_products-3" element
    And I should not see a "file_products-4" element
    And the checkbox "file_products-5" should not be checked
    And the checkbox "file_products-6" should not be checked
    And I should not see a "file_products-7" element
    And I should not see a "file_products-8" element
    And the checkbox "file_products-9" should be checked

    # suppliers
    And I should not see a "#file_suppliers-0" element
    And the checkbox "file_suppliers-1" should not be checked
    And the checkbox "file_suppliers-2" should be checked




  Scenario: Check the Visibility checkboxes in "This table"
    # by default they are all checked
    And the checkbox "checkboxes_brands-0" should be checked
    And the checkbox "checkboxes_brands-1" should be checked

    And the checkbox "checkboxes_products-0" should be checked
    And the checkbox "checkboxes_products-2" should be checked
    And the checkbox "checkboxes_products-3" should be checked
    And the checkbox "checkboxes_products-4" should be checked
    And the checkbox "checkboxes_products-5" should be checked
    And the checkbox "checkboxes_products-6" should be checked
    And the checkbox "checkboxes_products-7" should be checked
    And the checkbox "checkboxes_products-8" should be checked
    And the checkbox "checkboxes_products-9" should be checked

    And the checkbox "checkboxes_suppliers-0" should be checked
    And the checkbox "checkboxes_suppliers-1" should be checked
    And the checkbox "checkboxes_suppliers-2" should be checked





  Scenario: Check the Visibility checkboxes in "Related tables"
    # by default only the "id", "name", "reference" keywords are checked
    And the checkbox "checkboxes_brands-0-2" should be checked
    And the checkbox "checkboxes_brands-1-2" should be checked

    And the checkbox "checkboxes_suppliers-0-2" should be checked
    And the checkbox "checkboxes_suppliers-1-2" should be checked
    And the checkbox "checkboxes_suppliers-2-2" should not be checked

    # there should be no visibility checkbox for products as this table is not referenced
    And I should not see a "checkboxes_products-0-2" element
    And I should not see a "checkboxes_products-1-2" element
    And I should not see a "checkboxes_products-2-2" element
    And I should not see a "checkboxes_products-3-2" element
    And I should not see a "checkboxes_products-4-2" element
    And I should not see a "checkboxes_products-5-2" element
    And I should not see a "checkboxes_products-6-2" element
    And I should not see a "checkboxes_products-7-2" element
    And I should not see a "checkboxes_products-8-2" element
    And I should not see a "checkboxes_products-9-2" element



  Scenario: Submit the "Generate pages" form (launch the generator)
    And I uncheck "keep_startpage"
    And I uncheck "append_links"

    And I press "Generate Pages"

    Then I should not see "Parse error"
    And I should not see "Fatal error"




  Scenario: Check for POST data continuity
    And I should see "Deleting existing files"
    And I should see "Table: The Brands"
    And I should see "Table: The Products"
    And I should see "Table: The Suppliers"

    And I should see "Your app has been created!"

    When I follow "Go to your app"
    Then I should see "Cruddiy Tests"
