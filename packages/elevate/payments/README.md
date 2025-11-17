# Elevate Payments

Easy-to-configure payment gateway management for Laravel applications.

## Features

- ✅ **Multiple Payment Gateways**: Stripe, PayPal, and more
- ✅ **Easy Configuration**: Simple admin interface to manage gateway credentials
- ✅ **Toggle On/Off**: Enable or disable gateways with a single click
- ✅ **Unified Checkout**: Single checkout page that adapts to enabled gateways
- ✅ **Secure**: Encrypted credential storage
- ✅ **Extensible**: Easy to add new payment gateways

## Supported Payment Methods

### Stripe
- Credit/Debit Cards
- Google Pay
- Apple Pay
- Klarna

### PayPal
- PayPal Express Checkout

## Installation

1. The package is already included in your `composer.json`. Run:

```bash
composer dump-autoload
```

2. Run migrations:

```bash
php artisan migrate
```

3. Install default gateways:

```bash
php artisan payments:install
```

## Configuration

### Admin Interface

Visit `/admin/payment-gateways` to configure your payment gateways.

For each gateway:
1. Toggle it **ON**
2. Enter your API credentials (publishable key, secret key, etc.)
3. Click **Save Changes**

### Stripe Setup

1. Get your API keys from [Stripe Dashboard](https://dashboard.stripe.com/apikeys)
2. Enter:
   - **Publishable Key**: `pk_test_XXXXX` or `pk_live_XXXXX`
   - **Secret Key**: `sk_test_XXXXX` or `sk_live_XXXXX`

### PayPal Setup

1. Get your credentials from [PayPal Developer](https://developer.paypal.com/)
2. Enter:
   - **Client ID**
   - **Secret**

## Usage

### Checkout Page

The checkout page is available at `/checkout`. It automatically displays all enabled payment gateways.

### Programmatic Usage

```php
use Elevate\Payments\Services\PaymentService;

$paymentService = app(PaymentService::class);

// Get enabled gateways
$gateways = $paymentService->getEnabledGateways();

// Process a payment
$result = $paymentService->charge(
    gatewayId: 1,
    amount: 99.99,
    paymentData: [
        'currency' => 'GBP',
        'token' => $stripeToken,
        'returnUrl' => route('checkout.complete'),
        'cancelUrl' => route('checkout.index'),
    ]
);

if ($result['success']) {
    // Payment successful
    $transactionId = $result['reference'];
}
```

## Adding New Gateways

1. Install the Omnipay driver:

```bash
composer require omnipay/worldpay
```

2. Add to database:

```php
PaymentGateway::create([
    'name' => 'Worldpay',
    'driver' => 'worldpay',
    'is_enabled' => false,
    'sort_order' => 3,
    'credentials' => [],
    'settings' => [
        'payment_methods' => ['card'],
    ],
]);
```

3. Update the admin view to include credential fields for the new gateway.

## Routes

### Public Routes
- `GET /checkout` - Checkout page
- `POST /checkout/process` - Process payment
- `GET /checkout/complete` - Payment completion page

### Admin Routes
- `GET /admin/payment-gateways` - Manage gateways
- `PATCH /admin/payment-gateways/{gateway}` - Update gateway
- `PATCH /admin/payment-gateways/{gateway}/toggle` - Toggle gateway on/off

## Security

- All credentials are encrypted in the database
- HTTPS is required for production
- PCI compliance is handled by payment providers (Stripe, PayPal)

## License

MIT
