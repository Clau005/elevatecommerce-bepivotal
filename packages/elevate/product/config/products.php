<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Settings
    |--------------------------------------------------------------------------
    */

    'default_weight_unit' => 'kg',
    
    'weight_units' => [
        'kg' => 'Kilograms',
        'g' => 'Grams',
        'lb' => 'Pounds',
        'oz' => 'Ounces',
    ],

    'default_status' => 'draft',

    'statuses' => [
        'draft' => 'Draft',
        'active' => 'Active',
        'archived' => 'Archived',
    ],

    'types' => [
        'simple' => 'Simple Product',
        'variable' => 'Product with Variants',
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory Settings
    |--------------------------------------------------------------------------
    */

    'track_inventory_by_default' => true,
    'continue_selling_when_out_of_stock' => false,

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    */

    'taxable_by_default' => true,
    'default_tax_rate' => 0.0,

    /*
    |--------------------------------------------------------------------------
    | Shipping Settings
    |--------------------------------------------------------------------------
    */

    'requires_shipping_by_default' => true,

    /*
    |--------------------------------------------------------------------------
    | Variant Settings
    |--------------------------------------------------------------------------
    */

    'max_variants_per_product' => 100,
    'max_options' => 3, // Maximum number of variant options (Size, Color, Material, etc.)
];
