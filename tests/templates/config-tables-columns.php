<?php

$tables_and_columns_names = array(
  'brands'    =>
    array(
      'name'    => 'The Brands',
      'columns' =>
        array(
          'id'   =>
            array(
              'columndisplay'   => 'id',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 1,
            ),
          'name' =>
            array(
              'columndisplay'   => 'name',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 1,
            ),
        ),
    ),
  'products'  =>
    array(
      'name'    => 'The Products',
      'columns' =>
        array(
          'id'                              =>
            array(
              'columndisplay'   => 'id',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'ean'                             =>
            array(
              'columndisplay'   => 'ean',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'product_name'                    =>
            array(
              'columndisplay'   => 'product_name',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'brand_id'                        =>
            array(
              'columndisplay'   => 'brand_id',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'supplier_id'                     =>
            array(
              'columndisplay'   => 'supplier_id',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'visual_url'                      =>
            array(
              'columndisplay'   => 'visual_url',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'packaging_details'               =>
            array(
              'columndisplay'   => 'packaging_details',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'recycled_material_incorporation' =>
            array(
              'columndisplay'   => 'recycled_material_incorporation',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'hazardous_substance_presence'    =>
            array(
              'columndisplay'   => 'hazardous_substance_presence',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
          'packshot_file'                   =>
            array(
              'columndisplay'   => 'packshot_file',
              'is_file'         => 1,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
        ),
    ),
  'suppliers' =>
    array(
      'name'    => 'The Suppliers',
      'columns' =>
        array(
          'id'   =>
            array(
              'columndisplay'   => 'id',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 1,
            ),
          'name' =>
            array(
              'columndisplay'   => 'name',
              'is_file'         => 0,
              'columnvisible'   => 1,
              'columninpreview' => 1,
            ),
          'logo' =>
            array(
              'columndisplay'   => 'logo',
              'is_file'         => 1,
              'columnvisible'   => 1,
              'columninpreview' => 0,
            ),
        ),
    ),
);

?>