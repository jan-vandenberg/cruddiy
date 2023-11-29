Feature: Check admin columns page form

  @javascript
  Scenario:
    Given I am on "/core/columns.php"

    # brands

    # id
    # And I check "checkboxes_brands-0"
    And I wait for the element with id "checkboxes_brands-0" to appear
    And I check "checkboxes_brands-0"
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

    Then I should see "Deleting existing files"
    And I should not see "Error"
    And I should not see "Warning"
    And I should not see "Fatal"