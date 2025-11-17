# Polymorphic Purchasable System

## Overview

The Purchasable package uses a **polymorphic architecture** that allows ANY model to become sellable by simply adding the `IsPurchasable` trait. This provides maximum flexibility while maintaining strict data integrity.

## Core Concept

```
┌─────────────────────────────────────────────────────────┐
│                    IsPurchasable Trait                  │
│  (Add to ANY model to make it sellable)                │
└─────────────────────────────────────────────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
    ┌───▼───┐         ┌────▼────┐       ┌────▼────┐
    │Product│         │ Service │       │Membership│
    └───┬───┘         └────┬────┘       └────┬────┘
        │                  │                  │
        └──────────────────┼──────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
   ┌────▼─────┐      ┌─────▼────┐      ┌─────▼────┐
   │CartItem  │      │Wishlist  │      │OrderItem │
   │(morph)   │      │Item      │      │(morph)   │
   └──────────┘      │(morph)   │      └──────────┘
                     └──────────┘
```

## Required Fields

Every model using `IsPurchasable` **MUST** have these fields:

```php
// Required (validated on create)
'name'              => string    // Product/item name
'price'             => integer   // Selling price (smallest unit)
'sku'               => string    // Stock Keeping Unit

// Recommended
'short_description' => string    // Brief description
'description'       => text      // Full description
'unit_price'        => integer   // Original price (nullable)
'cost_price'        => integer   // Cost to business (nullable)
```

## Price Storage Rules

### ⚠️ CRITICAL: All prices stored as integers in smallest currency unit

```php
// ❌ WRONG - Never store as decimal
$product->price = 20.00;

// ✅ CORRECT - Store as cents/smallest unit
$product->price = 2000;  // $20.00 = 2000 cents

// ✅ CORRECT - Use setter (auto-converts)
$product->price = 20.00; // Trait converts to 2000
```

### Why Integers?

1. **Precision**: No floating-point rounding errors
2. **Database**: Integer operations are faster
3. **Currency**: Works with all currencies (cents, pence, yen, etc.)
4. **Calculations**: Accurate tax and discount calculations

### Conversion Examples

```php
// USD: $20.00 = 2000 cents
$price = 2000;
$formatted = $price / 100; // 20.00

// EUR: €15.50 = 1550 cents
$price = 1550;
$formatted = $price / 100; // 15.50

// JPY: ¥1000 = 1000 (no decimal)
$price = 1000;
$formatted = $price / 100; // 10.00 (or handle differently)
```

## Using the IsPurchasable Trait

### Step 1: Add Required Fields to Migration

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('short_description')->nullable();
    $table->text('description')->nullable();
    $table->integer('price');           // Required - in cents
    $table->integer('unit_price')->nullable();
    $table->integer('cost_price')->nullable();
    $table->string('sku')->unique();   // Required
    $table->timestamps();
});
```

### Step 2: Add Trait to Model

```php
use ElevateCommerce\Purchasable\Traits\IsPurchasable;

class Product extends Model
{
    use IsPurchasable;

    protected $fillable = [
        'name',
        'short_description',
        'description',
        'price',
        'unit_price',
        'cost_price',
        'sku',
    ];
}
```

### Step 3: Use It!

```php
// Create product
$product = Product::create([
    'name' => 'T-Shirt',
    'short_description' => 'Comfortable cotton t-shirt',
    'price' => 2999,  // $29.99
    'sku' => 'TSHIRT-001',
]);

// Add to cart (polymorphic)
$cartItem = CartItem::create([
    'cart_id' => $cart->id,
    'purchasable_id' => $product->id,
    'purchasable_type' => Product::class,
    'quantity' => 2,
    'price' => $product->price,
]);

// Add to wishlist (polymorphic)
$wishlistItem = WishlistItem::create([
    'wishlist_id' => $wishlist->id,
    'purchasable_id' => $product->id,
    'purchasable_type' => Product::class,
]);

// Create order (polymorphic)
$orderItem = OrderItem::create([
    'order_id' => $order->id,
    'purchasable_id' => $product->id,
    'purchasable_type' => Product::class,
    'name' => $product->name,  // Snapshot
    'sku' => $product->sku,    // Snapshot
    'quantity' => 2,
    'price' => $product->price,
]);
```

## Models Overview

### Cart & CartItem
- **Session-based** for guests
- **Database-based** for authenticated users
- Auto-calculates totals (subtotal, tax, shipping, discount)
- Supports polymorphic items

### Wishlist & WishlistItem
- **Session-based** for guests
- **Database-based** for authenticated users
- Priority levels (1-5)
- Customer notes
- Supports polymorphic items

### Order, OrderItem, OrderAddress, OrderTimeline
- **Guest checkout** supported
- **Snapshot** of items at purchase time
- **Status tracking** (pending, processing, shipped, delivered, cancelled, refunded)
- **Timeline logging** (all events tracked)
- **Polymorphic items** with full snapshot

## Polymorphic Relationships

### CartItem → Purchasable

```php
// Get the actual product/service/etc
$cartItem->purchasable; // Returns Product, Service, etc.

// From purchasable side
$product->cartItems(); // All cart items for this product
```

### WishlistItem → Purchasable

```php
// Get the actual product/service/etc
$wishlistItem->purchasable;

// From purchasable side
$product->wishlistItems();
```

### OrderItem → Purchasable

```php
// Get the actual product (may be null if deleted)
$orderItem->purchasable;

// From purchasable side
$product->orderItems();
```

## Session vs Authenticated

### Guest (Session-based)
```php
// Cart
$cart = Cart::create([
    'session_id' => session()->getId(),
    'subtotal' => 0,
    'total' => 0,
]);

// Wishlist
$wishlist = Wishlist::create([
    'session_id' => session()->getId(),
]);
```

### Authenticated (Database)
```php
// Cart
$cart = Cart::create([
    'user_id' => auth()->id(),
    'subtotal' => 0,
    'total' => 0,
]);

// Wishlist
$wishlist = Wishlist::create([
    'user_id' => auth()->id(),
]);
```

### Migration on Login
```php
// When guest logs in, migrate their cart
$guestCart = Cart::where('session_id', session()->getId())->first();
if ($guestCart) {
    $guestCart->update(['user_id' => auth()->id(), 'session_id' => null]);
}
```

## Order Snapshots

Orders capture a **complete snapshot** of items at purchase time:

```php
OrderItem::create([
    'order_id' => $order->id,
    'purchasable_id' => $product->id,
    'purchasable_type' => Product::class,
    
    // Snapshot fields (won't change if product changes)
    'name' => $product->name,
    'sku' => $product->sku,
    'price' => $product->price,
    'cost_price' => $product->cost_price,
    
    // Additional snapshot data
    'metadata' => [
        'short_description' => $product->short_description,
        'description' => $product->description,
        'image' => $product->purchasable_image,
    ],
]);
```

## Timeline Logging

Every order event is logged:

```php
// System events
$order->logTimeline('order_created', 'Order created');
$order->logTimeline('payment_received', 'Payment processed via Stripe');

// Admin events
$order->logTimeline('status_changed', 'Status updated', 'Customer requested cancellation', auth()->id());

// Customer events
$order->logTimeline('note_added', 'Customer added note', $note, auth()->id());
```

## Trait Methods

The `IsPurchasable` trait provides:

```php
// Relationships
$model->cartItems()
$model->wishlistItems()
$model->orderItems()

// Formatted prices (converts from cents)
$model->formatted_price      // 20.00
$model->formatted_unit_price // 25.00
$model->formatted_cost_price // 15.00

// Stock checking (override in your model)
$model->inStock()           // true/false
$model->availableQuantity() // int|null
$model->canPurchase(5)      // Check if 5 units can be purchased

// Display
$model->purchasable_name    // Name for display
$model->purchasable_image   // Image URL (override in model)
```

## Example: Selling Different Types

```php
// Product
class Product extends Model {
    use IsPurchasable;
}

// Service
class Service extends Model {
    use IsPurchasable;
}

// Membership
class Membership extends Model {
    use IsPurchasable;
}

// Digital Download
class DigitalProduct extends Model {
    use IsPurchasable;
}

// All work the same way!
$cart->items()->create([
    'purchasable_id' => $product->id,
    'purchasable_type' => Product::class,
    'quantity' => 1,
    'price' => $product->price,
]);
```

## Benefits

1. **Flexibility**: Sell anything - products, services, memberships, etc.
2. **Consistency**: Same interface for all purchasable items
3. **Type Safety**: Polymorphic relationships maintain referential integrity
4. **Snapshots**: Orders preserve item data even if original is deleted
5. **Precision**: Integer prices eliminate floating-point errors
6. **Scalability**: Works with any currency and pricing model
7. **Auditability**: Complete timeline of all order events
