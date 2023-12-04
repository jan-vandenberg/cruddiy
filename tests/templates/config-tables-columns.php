<?php

$tables_and_columns_names = array (
  'brands' =>
  array (
    'name' => 'The Brands',
    'columns' =>
    array (
      'id' =>
      array (
        'columndisplay' => 'id',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 1,
      ),
      'name' =>
      array (
        'columndisplay' => 'Brand name',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 1,
      ),
    ),
  ),
  'products' =>
  array (
    'name' => 'The Products',
    'columns' =>
    array (
      'id' =>
      array (
        'columndisplay' => 'id',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 0,
      ),
      'ean' =>
      array (
        'columndisplay' => 'EAN-13',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 0,
      ),
      'product_name' =>
      array (
        'columndisplay' => 'Product name',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 0,
      ),
      'brand_id' =>
      array (
        'columndisplay' => 'Brand',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 0,
      ),
      'supplier_id' =>
      array (
        'columndisplay' => 'Supplier',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 0,
      ),
      'visual_url' =>
      array (
        'columndisplay' => 'Visual URL',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 0,
      ),
      'packaging_details' =>
      array (
        'columndisplay' => 'Packaging details',
        'is_file' => 0,
        'columnvisible' => 0,
        'columninpreview' => 0,
      ),
      'recycled_material_incorporation' =>
      array (
        'columndisplay' => 'Recycled materials',
        'is_file' => 0,
        'columnvisible' => 0,
        'columninpreview' => 0,
      ),
      'hazardous_substance_presence' =>
      array (
        'columndisplay' => 'Hazardous substance',
        'is_file' => 0,
        'columnvisible' => 0,
        'columninpreview' => 0,
      ),
      'packshot_file' =>
      array (
        'columndisplay' => 'Packshot',
        'is_file' => 1,
        'columnvisible' => 1,
        'columninpreview' => 0,
      ),
    ),
  ),
  'suppliers' =>
  array (
    'name' => 'The Suppliers',
    'columns' =>
    array (
      'id' =>
      array (
        'columndisplay' => 'id',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 1,
      ),
      'name' =>
      array (
        'columndisplay' => 'Supplier name',
        'is_file' => 0,
        'columnvisible' => 1,
        'columninpreview' => 1,
      ),
      'logo' =>
      array (
        'columndisplay' => 'Logo',
        'is_file' => 1,
        'columnvisible' => 0,
        'columninpreview' => 0,
      ),
    ),
  ),
);

?>