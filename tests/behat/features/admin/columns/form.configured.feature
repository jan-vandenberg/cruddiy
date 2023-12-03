Feature: Check field default values

  Scenario: Name and select Brands columns when the generator has already run

    And the "text-brands-id" field should contain "id"
    And the "text-products-id" field should contain "id"
    And the "text-products-ean" field should contain "ean"
    And the "text-products-product_name" field should contain "product_name"
    And the "text-products-brand_id" field should contain "brand_id"
    And the "text-products-supplier_id" field should contain "supplier_id"
    And the "text-products-visual_url" field should contain "visual_url"
    And the "text-products-packaging_details" field should contain "packaging_details"
    And the "text-products-recycled_material_incorporation" field should contain "recycled_material_incorporation"
    And the "text-products-hazardous_substance_presence" field should contain "hazardous_substance_presence"
    And the "text-suppliers-id" field should contain "id"