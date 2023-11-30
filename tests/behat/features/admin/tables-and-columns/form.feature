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



Scenario: Check for POST data continuity between Tables and Columns
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
    And I check "productscolumns[0][columninpreview]"

    # ean
    And I fill in "productscolumns[1][columndisplay]" with "EAN-13"
    And I check "productscolumns[1][columnvisible]"
    And I check "productscolumns[1][columninpreview]"

    # product_name
    And I fill in "productscolumns[2][columndisplay]" with "Name"
    And I check "productscolumns[2][columnvisible]"
    And I check "productscolumns[2][columninpreview]"

    # brand_id
    And I fill in "productscolumns[3][columndisplay]" with "Brand name"
    And I check "productscolumns[3][columnvisible]"
    And I check "productscolumns[3][columninpreview]"

    # supplier_id
    And I fill in "productscolumns[4][columndisplay]" with "Supplier name"
    And I check "productscolumns[4][columnvisible]"
    And I check "productscolumns[4][columninpreview]"

    # visual_url
    And I fill in "productscolumns[5][columndisplay]" with "Photo URL"
    And I check "productscolumns[5][columnvisible]"
    And I check "productscolumns[5][columninpreview]"

    # packaging_details
    And I fill in "productscolumns[6][columndisplay]" with "Packaging details"
    And I check "productscolumns[6][columnvisible]"
    And I check "productscolumns[6][columninpreview]"

    # recycled_material_incorporation
    And I fill in "productscolumns[7][columndisplay]" with "Contains recycled material?"
    And I check "productscolumns[7][columnvisible]"
    And I check "productscolumns[7][columninpreview]"

    # hazardous_substance_presence
    And I fill in "productscolumns[8][columndisplay]" with "Contains hazardous substance?"
    And I check "productscolumns[8][columnvisible]"
    And I check "productscolumns[8][columninpreview]"



    # suppliers

    # id
    And I check "supplierscolumns[0][columnvisible]"
    And I check "supplierscolumns[0][columninpreview]"

    # name
    And I fill in "supplierscolumns[1][columndisplay]" with "Supplier name"
    And I check "supplierscolumns[1][columnvisible]"
    And I check "supplierscolumns[1][columninpreview]"



    # Other
    And I check "keep_startpage"
    And I check "append_links"

    And I press "Generate Pages"

    Then I should not see "Parse error"
    And I should not see "Fatal error"

    And I should not see "Deleting existing files"
    And I should see "Table: The Brands"
    And I should see "Table: The Products"
    And I should see "Table: The Suppliers"

    And I should see "Your app has been created!"

    When I follow "Go to your app"
    Then I should see "Cruddiy Tests"