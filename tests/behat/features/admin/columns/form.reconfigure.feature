Feature: Check field default values

  Scenario: Name and select Brands columns when the generator has already run

    # brands
    And the "text-brands-id" field should contain "id"
    And the "text-brands-name" field should contain "Brand name"

    And I should not see a "file-brands-id" element
    And the "visibility-brands-id" checkbox should be checked
    And the "visibility-fk-brands-id" checkbox should be checked

    And the checkbox "file-brands-name" should not be checked
    And the "visibility-brands-name" checkbox should be checked
    And the "visibility-fk-brands-name" checkbox should be checked



    # products
    And the "text-products-id" field should contain "id"
    And the "text-products-ean" field should contain "EAN-13"
    And the "text-products-product_name" field should contain "Product name"
    And the "text-products-brand_id" field should contain "Brand"
    And the "text-products-supplier_id" field should contain "Supplier"
    And the "text-products-visual_url" field should contain "Visual URL"
    And the "text-products-packaging_details" field should contain "Packaging details"
    And the "text-products-recycled_material_incorporation" field should contain "Recycled materials"
    And the "text-products-hazardous_substance_presence" field should contain "Hazardous substance"

    # id
    And I should not see a "file-products-id" element
    And the "visibility-products-id" checkbox should be checked
    And I should not see a "visibility-fk-products-id" element

    # ean
    And the checkbox "file-products-ean" should not be checked
    And the checkbox "visibility-products-ean" should be checked
    Then I should not see a "visibility-fk-products-ean" element

    # product_name
    And the checkbox "file-products-product_name" should not be checked
    And the checkbox "visibility-products-product_name" should be checked
    Then I should not see a "visibility-fk-products-product_name" element

    # brand_id
    And I should not see a "file-products-brand_id" element
    And the checkbox "visibility-products-brand_id" should be checked
    Then I should not see a "visibility-fk-products-brand_id" element

    # supplier_id
    And I should not see a "file-products-supplier_id" element
    And the checkbox "visibility-products-supplier_id" should be checked
    Then I should not see a "visibility-fk-products-supplier_id" element

    # visual_url
    And the checkbox "file-products-visual_url" should not be checked
    And the checkbox "visibility-products-visual_url" should be checked
    Then I should not see a "visibility-fk-products-visual_url" element

    # packaging_details
    And the checkbox "file-products-packaging_details" should not be checked
    And the checkbox "visibility-products-packaging_details" should not be checked
    Then I should not see a "visibility-fk-products-packaging_details" element

    # recycled_material_incorporation
    And I should not see a "file-products-recycled_material_incorporation" element
    And the checkbox "visibility-products-recycled_material_incorporation" should not be checked
    Then I should not see a "visibility-fk-products-recycled_material_incorporation" element

    # hazardous_substance_presence
    And I should not see a "file-products-hazardous_substance_presence" element
    And the checkbox "visibility-products-hazardous_substance_presence" should not be checked
    Then I should not see a "visibility-fk-products-hazardous_substance_presence" element



    # suppliers
    And the "text-suppliers-id" field should contain "id"
    And the "text-suppliers-name" field should contain "Supplier name"
    And the "text-suppliers-logo" field should contain "Logo"

    And I should not see a "file-suppliers-id" element
    And the "visibility-suppliers-id" checkbox should be checked
    And the "visibility-fk-suppliers-id" checkbox should be checked

    And the checkbox "file-suppliers-name" should not be checked
    And the "visibility-suppliers-name" checkbox should be checked
    And the "visibility-fk-suppliers-name" checkbox should be checked

    And the checkbox "file-suppliers-logo" should be checked
    And the "visibility-suppliers-logo" checkbox should not be checked
    And the "visibility-fk-suppliers-logo" checkbox should not be checked






    # submit form
    And I press "Generate Pages"

    Then I should not see "Parse error"
    And I should not see "Fatal error"