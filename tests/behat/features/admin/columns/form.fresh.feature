Feature: Check field default values

  Scenario: Name and select Brands columns on a fresh installation

    And the "text-brands-id" field should contain ""
    And the "text-products-id" field should contain ""
    And the "text-products-ean" field should contain ""
    And the "text-products-product_name" field should contain ""
    And the "text-products-brand_id" field should contain ""
    And the "text-products-supplier_id" field should contain ""
    And the "text-products-visual_url" field should contain ""
    And the "text-products-packaging_details" field should contain ""
    And the "text-products-recycled_material_incorporation" field should contain ""
    And the "text-products-hazardous_substance_presence" field should contain ""
    And the "text-suppliers-id" field should contain ""