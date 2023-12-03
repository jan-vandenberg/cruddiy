Feature: Fill admin columns form

  Scenario: Set values

    And I fill in "text-brands-name" with "Brand name"

    And I fill in "text-products-ean" with "EAN-13"
    And I fill in "text-products-product_name" with "Product name"
    And I fill in "text-products-brand_id" with "Brand"
    And I fill in "text-products-supplier_id" with "Supplier"
    And I fill in "text-products-visual_url" with "Visual URL"
    And I fill in "text-products-packaging_details" with "Packaging details"
    And I fill in "text-products-recycled_material_incorporation" with "Recycled materials"
    And I fill in "text-products-hazardous_substance_presence" with "Hazardous substance"
    And I fill in "text-products-packshot_file" with "Packshot"

    And I fill in "text-suppliers-name" with "Supplier name"
    And I fill in "text-suppliers-logo" with "Logo"
    And I uncheck "visibility-suppliers-logo"

    And I uncheck "visibility-products-packaging_details"
    And I uncheck "visibility-products-recycled_material_incorporation"
    And I uncheck "visibility-products-hazardous_substance_presence"

    And I uncheck "keep_startpage"
    And I uncheck "append_links"

    And I press "Generate Pages"

    Then I should not see "Parse error"
    And I should not see "Fatal error"