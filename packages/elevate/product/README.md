# Elevate Product Package

Product management package for the Elevate e-commerce platform with support for simple products and products with variants.

## Features

- ✅ **Simple Products** - Single SKU products with direct pricing
- ✅ **Variable Products** - Products with up to 3 variant options (Size, Color, Material, etc.)
- ✅ **Purchasable Trait** - Full implementation of the commerce-core Purchasable interface
- ✅ **Inventory Tracking** - Track stock levels per product or variant
- ✅ **Pricing** - Price, compare-at price, and cost per item
- ✅ **Shipping** - Weight tracking and shipping requirements
- ✅ **Tax** - Configurable tax rates per product
- ✅ **Media** - Featured image and gallery images
- ✅ **SEO** - Meta title and description
- ✅ **Templates** - Assign storefront templates per product
- ✅ **Soft Deletes** - Safe deletion with recovery

## Installation

The package is already installed via composer. Run migrations:

```bash
php artisan migrate --path=packages/elevate/product/database/migrations
```

## Models

### Product Model

Located at `Elevate\Product\Models\Product`

**Key Fields:**
- `name` - Product name
- `slug` - URL-friendly identifier
- `sku` - Stock keeping unit
- `type` - `simple` or `variable`
- `status` - `draft`, `active`, or `archived`
- `price` - Base price
- `track_inventory` - Boolean for inventory tracking
- `stock` - Stock level (for simple products)
- `weight` - Shipping weight
- `template_id` - Foreign key to templates table

**Relationships:**
- `variants()` - HasMany ProductVariant
- `template()` - BelongsTo Template

**Purchasable Trait Methods:**
```php
$product->getName();              // Get product name
$product->getPrice();             // Get price (lowest variant price if variable)
$product->getCompareAtPrice();    // Get compare-at price
$product->getSku();               // Get SKU
$product->getImageUrl();          // Get featured image URL
$product->tracksInventory();      // Check if tracks inventory
$product->getStockLevel();        // Get stock (sum of variants if variable)
$product->getWeight();            // Get weight for shipping
$product->getTaxRate();           // Get tax rate
$product->requiresShipping();     // Check if requires shipping
$product->getMetaData();          // Get additional meta data
```

### ProductVariant Model

Located at `Elevate\Product\Models\ProductVariant`

**Key Fields:**
- `product_id` - Foreign key to products
- `name` - Variant name
- `sku` - Variant SKU
- `price` - Variant price
- `stock` - Variant stock level
- `option1_name` / `option1_value` - First option (e.g., Size: Large)
- `option2_name` / `option2_value` - Second option (e.g., Color: Red)
- `option3_name` / `option3_value` - Third option (e.g., Material: Cotton)

**Methods:**
```php
$variant->getVariantTitle();  // Returns "Large / Red / Cotton"
```

## Usage Examples

### Creating a Simple Product

```php
use Elevate\Product\Models\Product;

$product = Product::create([
    'name' => 'Basic T-Shirt',
    'slug' => 'basic-t-shirt',
    'sku' => 'TSHIRT-001',
    'type' => 'simple',
    'status' => 'active',
    'price' => 29.99,
    'track_inventory' => true,
    'stock' => 100,
    'weight' => 0.2,
    'requires_shipping' => true,
    'is_taxable' => true,
]);
```

### Creating a Variable Product with Variants

```php
use Elevate\Product\Models\Product;
use Elevate\Product\Models\ProductVariant;

// Create the parent product
$product = Product::create([
    'name' => 'Premium T-Shirt',
    'slug' => 'premium-t-shirt',
    'type' => 'variable',
    'status' => 'active',
    'price' => 0, // Will be calculated from variants
    'requires_shipping' => true,
]);

// Create variants
$sizes = ['Small', 'Medium', 'Large'];
$colors = ['Red', 'Blue', 'Black'];

foreach ($sizes as $size) {
    foreach ($colors as $color) {
        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => "TSHIRT-{$size}-{$color}",
            'price' => 39.99,
            'option1_name' => 'Size',
            'option1_value' => $size,
            'option2_name' => 'Color',
            'option2_value' => $color,
            'track_inventory' => true,
            'stock' => 50,
        ]);
    }
}
```

### Using Products with Cart/Checkout

```php
use Elevate\Product\Models\Product;
use Elevate\Product\Models\ProductVariant;

// Simple product
$product = Product::find(1);
$cart->addItem($product, 2); // Add 2 units

// Variable product - add specific variant
$variant = ProductVariant::find(5);
$cart->addItem($variant, 1); // Add 1 unit of this variant
```

### Querying Products

```php
// Get active simple products
$products = Product::active()->simple()->get();

// Get variable products with variants
$products = Product::variable()->with('variants')->get();

// Check stock availability
if ($product->hasStock(5)) {
    // Can purchase 5 units
}

// Get price range for variable product
$minPrice = $product->variants()->min('price');
$maxPrice = $product->variants()->max('price');
```

## Admin Routes

The package registers the following admin routes:

- `GET /admin/products` - List products
- `GET /admin/products/create` - Create product form
- `POST /admin/products` - Store product
- `GET /admin/products/{product}` - Show product
- `GET /admin/products/{product}/edit` - Edit product form
- `PUT /admin/products/{product}` - Update product
- `DELETE /admin/products/{product}` - Delete product

**Variant Management:**
- `GET /admin/products/{product}/variants` - List variants
- `POST /admin/products/{product}/variants` - Create variant
- `PUT /admin/products/{product}/variants/{variant}` - Update variant
- `DELETE /admin/products/{product}/variants/{variant}` - Delete variant

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=product-config
```

Edit `config/products.php` to customize:

- Default weight unit
- Default status
- Inventory tracking defaults
- Tax settings
- Shipping settings
- Maximum variants per product

## Next Steps

To complete the product package, you'll need to create:

1. **Admin Controller** - `ProductController` with CRUD operations
2. **Admin Views** - Create, edit, index, and variant management views
3. **Product Factory** - For testing and seeding
4. **Product Seeder** - Sample products
5. **Storefront Routes** - Public product pages
6. **Storefront Controller** - Display products on storefront
7. **Product Templates** - Theme templates for product pages

## Integration with Collections

Products can be added to collections using the collectable polymorphic relationship:

```php
use Elevate\Collections\Models\Collection;
use Elevate\Product\Models\Product;

$collection = Collection::find(1);
$product = Product::find(1);

// Add product to collection
$collection->collectables()->create([
    'collectable_type' => Product::class,
    'collectable_id' => $product->id,
    'sort_order' => 0,
]);
```
