Feature: Check admin columns form

  Scenario: Name and select Brands columns

    # brands
    And I should see "The Brands (brands)"

    And I should not see a "file-brands-id" element
    And the "visibility-brands-id" checkbox should be checked
    And the "visibility-fk-brands-id" checkbox should be checked

    And I fill in "text-brands-name" with "Brand name"
    And the checkbox "file-brands-name" should not be checked
    And the "visibility-brands-name" checkbox should be checked
    And the "visibility-fk-brands-name" checkbox should be checked




    # products
    And I should see "The Products (products)"

    # id
    And I should not see a "file-products-id" element
    And the "visibility-products-id" checkbox should be checked
    And I should not see a "visibility-fk-products-id" element

    # ean
    And I fill in "text-products-ean" with "EAN-13"
    And the checkbox "file-products-ean" should not be checked
    And the checkbox "visibility-products-ean" should be checked
    Then I should not see a "visibility-fk-products-ean" element

    # product_name
    And I fill in "text-products-product_name" with "Product name"
    And the checkbox "file-products-product_name" should not be checked
    And the checkbox "visibility-products-product_name" should be checked
    Then I should not see a "visibility-fk-products-product_name" element

    # brand_id
    And I fill in "text-products-brand_id" with "Brand"
    And I should not see a "file-products-brand_id" element
    And the checkbox "visibility-products-brand_id" should be checked
    Then I should not see a "visibility-fk-products-brand_id" element

    # supplier_id
    And I fill in "text-products-supplier_id" with "Supplier"
    And I should not see a "file-products-supplier_id" element
    And the checkbox "visibility-products-supplier_id" should be checked
    Then I should not see a "visibility-fk-products-supplier_id" element

    # visual_url
    And I fill in "text-products-visual_url" with "Visual URL"
    And the checkbox "file-products-visual_url" should not be checked
    And the checkbox "visibility-products-visual_url" should be checked
    Then I should not see a "visibility-fk-products-visual_url" element

    # packaging_details
    And I fill in "text-products-packaging_details" with "Packaging details"
    And the checkbox "file-products-packaging_details" should not be checked
    And the checkbox "visibility-products-packaging_details" should be checked
    And I uncheck "visibility-products-packaging_details"
    Then I should not see a "visibility-fk-products-packaging_details" element

    # recycled_material_incorporation
    And I fill in "text-products-recycled_material_incorporation" with "Recycled materials"
    And I should not see a "file-products-recycled_material_incorporation" element
    And the checkbox "visibility-products-recycled_material_incorporation" should be checked
    And I uncheck "visibility-products-recycled_material_incorporation"
    Then I should not see a "visibility-fk-products-recycled_material_incorporation" element

    # hazardous_substance_presence
    And I fill in "text-products-hazardous_substance_presence" with "Hazardous substance"
    And I should not see a "file-products-hazardous_substance_presence" element
    And the checkbox "visibility-products-hazardous_substance_presence" should be checked
    And I uncheck "visibility-products-hazardous_substance_presence"
    Then I should not see a "visibility-fk-products-hazardous_substance_presence" element



    # suppliers
    And I should see "The Suppliers (suppliers)"

    And I should not see a "file-suppliers-id" element
    And the "visibility-suppliers-id" checkbox should be checked
    And the "visibility-fk-suppliers-id" checkbox should be checked

    And I fill in "text-suppliers-name" with "Supplier name"
    And the checkbox "file-suppliers-name" should not be checked
    And the "visibility-suppliers-name" checkbox should be checked
    And the "visibility-fk-suppliers-name" checkbox should be checked

    And I fill in "text-suppliers-logo" with "Logo"
    And the checkbox "file-suppliers-logo" should be checked
    And the "visibility-suppliers-logo" checkbox should be checked
    And the "visibility-fk-suppliers-logo" checkbox should not be checked
