# Purchasable Package Structure

```
packages/elevatecommerce/purchasable/
├── config/
│   └── purchasable.php                    # Package configuration
│
├── database/
│   ├── factories/                         # Model factories
│   ├── migrations/                        # Database migrations
│   └── seeders/                          # Database seeders
│
├── resources/
│   ├── views/
│   │   ├── cart/
│   │   │   ├── index.blade.php          # Cart page
│   │   │   └── mini-cart.blade.php      # Mini cart widget
│   │   ├── wishlist/
│   │   │   └── index.blade.php          # Wishlist page
│   │   ├── checkout/
│   │   │   ├── index.blade.php          # Checkout page
│   │   │   ├── success.blade.php        # Order success
│   │   │   └── cancel.blade.php         # Checkout cancelled
│   │   └── orders/
│   │       ├── index.blade.php          # Order history
│   │       └── show.blade.php           # Order details
│   ├── css/
│   └── js/
│
├── routes/
│   ├── web.php                           # Customer routes
│   └── admin.php                         # Admin routes
│
├── src/
│   ├── Console/
│   │   └── Commands/                     # Artisan commands
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CartController.php
│   │   │   ├── WishlistController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── OrderController.php
│   │   │   └── Admin/
│   │   │       └── OrderController.php
│   │   └── Middleware/                   # Custom middleware
│   │
│   ├── Models/
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Wishlist.php
│   │   ├── WishlistItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   └── OrderAddress.php
│   │
│   ├── Support/
│   │   ├── Cart/
│   │   │   ├── CartManager.php          # Cart business logic
│   │   │   └── CartCalculator.php       # Price calculations
│   │   ├── Wishlist/
│   │   │   └── WishlistManager.php      # Wishlist business logic
│   │   ├── Order/
│   │   │   ├── OrderManager.php         # Order business logic
│   │   │   └── OrderStatusManager.php   # Status management
│   │   ├── Checkout/
│   │   │   ├── CheckoutManager.php      # Checkout process
│   │   │   └── PaymentProcessor.php     # Payment handling
│   │   └── Helpers/
│   │       └── PriceHelper.php          # Price formatting
│   │
│   └── PurchasableServiceProvider.php    # Service provider
│
├── tests/                                 # Package tests
├── .gitignore
├── composer.json
├── README.md
└── PACKAGE_STRUCTURE.md
```

## Key Components

### Models
- **Cart**: Shopping cart for guests and authenticated users
- **CartItem**: Individual items in cart
- **Wishlist**: Customer wishlist
- **WishlistItem**: Items in wishlist
- **Order**: Customer orders
- **OrderItem**: Line items in orders
- **OrderAddress**: Shipping and billing addresses

### Controllers
- **CartController**: Cart CRUD operations
- **WishlistController**: Wishlist management
- **CheckoutController**: Checkout process
- **OrderController**: Customer order views
- **Admin/OrderController**: Admin order management

### Support Classes
- **CartManager**: Cart business logic
- **WishlistManager**: Wishlist operations
- **OrderManager**: Order creation and management
- **CheckoutManager**: Checkout workflow
- **PaymentProcessor**: Payment integration

### Routes
- **web.php**: Customer-facing routes (cart, wishlist, checkout, orders)
- **admin.php**: Admin routes (order management)

## Integration Points

### With Core Package
- Uses core User/Customer models
- Integrates with core navigation
- Uses core UI components

### With Products Package
- References product models
- Uses product pricing
- Handles product variants

### With Payments Package
- Payment gateway integration
- Transaction handling
- Refund processing

## Configuration

All configuration in `config/purchasable.php`:
- Cart settings (session, cookies)
- Wishlist limits
- Order statuses
- Checkout options
- Tax rates
- Shipping rules

## Events & Listeners

- `CartUpdated`: When cart changes
- `OrderCreated`: When order is placed
- `OrderStatusChanged`: When order status updates
- `WishlistUpdated`: When wishlist changes

## Notifications

- Order confirmation emails
- Order status update emails
- Admin new order notifications
