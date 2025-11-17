# ElevateCommerce Purchasable Package

Complete cart, wishlist, order, and checkout functionality for ElevateCommerce.

## Features

### 🛒 Cart
- Session-based cart for guests
- Database cart for authenticated users
- Add, update, remove items
- Cart totals with tax and shipping
- Persistent cart across sessions

### ❤️ Wishlist
- Save items for later
- Move items to cart
- Authenticated users only
- Unlimited items

### 📦 Orders
- Order management
- Order statuses (pending, processing, shipped, delivered, cancelled, refunded)
- Order history for customers
- Admin order management
- Order notifications

### 💳 Checkout
- Guest checkout support
- Multi-step checkout process
- Address management
- Payment integration ready
- Order confirmation

## Installation

The package is auto-discovered by Laravel. Just make sure it's in your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/elevatecommerce/purchasable"
        }
    ],
    "require": {
        "elevatecommerce/purchasable": "@dev"
    }
}
```

Then run:

```bash
composer update
php artisan migrate
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=purchasable-config
```

## Usage

### Cart

```php
use ElevateCommerce\Purchasable\Support\Cart\CartManager;

// Add item to cart
$cart = app(CartManager::class);
$cart->add($product, $quantity, $options);

// Get cart items
$items = $cart->items();

// Get cart total
$total = $cart->total();

// Clear cart
$cart->clear();
```

### Wishlist

```php
use ElevateCommerce\Purchasable\Support\Wishlist\WishlistManager;

// Add to wishlist
$wishlist = app(WishlistManager::class);
$wishlist->add($product);

// Remove from wishlist
$wishlist->remove($productId);

// Move to cart
$wishlist->moveToCart($productId);
```

### Orders

```php
use ElevateCommerce\Purchasable\Models\Order;

// Create order
$order = Order::create([
    'user_id' => auth()->id(),
    'status' => 'pending',
    'total' => $cart->total(),
    // ... other fields
]);

// Update order status
$order->updateStatus('processing');

// Get customer orders
$orders = auth()->user()->orders()->latest()->get();
```

## Routes

### Customer Routes
- `GET /cart` - View cart
- `POST /cart/add` - Add to cart
- `GET /wishlist` - View wishlist
- `GET /checkout` - Checkout page
- `GET /account/orders` - Order history

### Admin Routes
- `GET /admin/orders` - Manage orders
- `GET /admin/orders/{order}` - View order details
- `PATCH /admin/orders/{order}/status` - Update order status

## Models

- `Cart` - Shopping cart
- `CartItem` - Cart items
- `Wishlist` - Customer wishlist
- `WishlistItem` - Wishlist items
- `Order` - Customer orders
- `OrderItem` - Order line items
- `OrderAddress` - Shipping/billing addresses

## Events

- `CartUpdated` - Fired when cart is modified
- `OrderCreated` - Fired when order is placed
- `OrderStatusChanged` - Fired when order status changes
- `WishlistUpdated` - Fired when wishlist is modified

## Testing

```bash
php artisan test packages/elevatecommerce/purchasable
```

## License

MIT
