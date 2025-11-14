# Payment Package Setup Instructions

## ✅ What's Been Completed

### 1. Core Architecture
- ✅ `PaymentGatewayInterface` - Contract for all gateways
- ✅ `PaymentRequest` & `PaymentResponse` DTOs
- ✅ `PaymentGatewayManager` - Centralized gateway management
- ✅ `StripeGateway` - Full Stripe implementation
- ✅ `PaymentService` - Refactored to use new architecture
- ✅ `WebhookController` - Unified webhook handling

### 2. Database Schema
- ✅ `payment_gateways` table migration
- ✅ `transactions` table migration
- ✅ `refunds` table migration
- ✅ `Transaction` model with relationships
- ✅ `Refund` model with relationships

### 3. Documentation
- ✅ `NEW_ARCHITECTURE.md` - Complete architecture guide
- ✅ `payment-gateway-article.md` - Reference article

## 🔧 Setup Steps Required

### Step 1: Run Migrations

```bash
php artisan migrate
```

This will create three new tables:
- `payment_gateways` - Gateway configuration
- `transactions` - Payment transaction records
- `refunds` - Refund records

### Step 2: Install Stripe SDK

```bash
composer require stripe/stripe-php
```

### Step 3: Seed Payment Gateway Data

You need to create a PaymentGateway record for Stripe:

```php
use Elevate\Payments\Models\PaymentGateway;

PaymentGateway::create([
    'name' => 'Stripe',
    'display_name' => 'Credit Card (Stripe)',
    'driver' => 'stripe',
    'is_enabled' => true,
    'test_mode' => true,
    'test_credentials' => [
        'publishable_key' => 'pk_test_YOUR_KEY',
        'secret_key' => 'sk_test_YOUR_KEY',
        'webhook_secret' => 'whsec_YOUR_SECRET',
    ],
    'credentials' => [
        'publishable_key' => 'pk_live_YOUR_KEY',
        'secret_key' => 'sk_live_YOUR_KEY',
        'webhook_secret' => 'whsec_YOUR_SECRET',
    ],
    'settings' => [],
    'sort_order' => 1,
]);
```

Or create a seeder:

```php
// database/seeders/PaymentGatewaySeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Elevate\Payments\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        PaymentGateway::updateOrCreate(
            ['name' => 'Stripe'],
            [
                'display_name' => 'Credit Card (Stripe)',
                'driver' => 'stripe',
                'is_enabled' => true,
                'test_mode' => env('STRIPE_TEST_MODE', true),
                'test_credentials' => [
                    'publishable_key' => env('STRIPE_TEST_PUBLISHABLE_KEY'),
                    'secret_key' => env('STRIPE_TEST_SECRET_KEY'),
                    'webhook_secret' => env('STRIPE_TEST_WEBHOOK_SECRET'),
                ],
                'credentials' => [
                    'publishable_key' => env('STRIPE_LIVE_PUBLISHABLE_KEY'),
                    'secret_key' => env('STRIPE_LIVE_SECRET_KEY'),
                    'webhook_secret' => env('STRIPE_LIVE_WEBHOOK_SECRET'),
                ],
                'settings' => [],
                'sort_order' => 1,
            ]
        );
    }
}
```

Then run:
```bash
php artisan db:seed --class=PaymentGatewaySeeder
```

### Step 4: Add Environment Variables

Add to your `.env`:

```env
# Stripe Test Mode
STRIPE_TEST_MODE=true
STRIPE_TEST_PUBLISHABLE_KEY=pk_test_...
STRIPE_TEST_SECRET_KEY=sk_test_...
STRIPE_TEST_WEBHOOK_SECRET=whsec_...

# Stripe Live Mode
STRIPE_LIVE_PUBLISHABLE_KEY=pk_live_...
STRIPE_LIVE_SECRET_KEY=sk_live_...
STRIPE_LIVE_WEBHOOK_SECRET=whsec_...
```

### Step 5: Register Webhook Routes

Add to your routes file (or create a new one):

```php
// routes/webhooks.php
use Elevate\Payments\Http\Controllers\WebhookController;

Route::post('/webhooks/payments/stripe', [WebhookController::class, 'handleStripe'])
    ->name('webhooks.payments.stripe');

Route::post('/webhooks/payments/paypal', [WebhookController::class, 'handlePayPal'])
    ->name('webhooks.payments.paypal');

// Generic webhook handler
Route::post('/webhooks/payments/{gateway}', [WebhookController::class, 'handle'])
    ->name('webhooks.payments.generic');
```

**Important:** Exclude webhook routes from CSRF protection in `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'webhooks/*',
];
```

### Step 6: Configure Stripe Webhooks

1. Go to Stripe Dashboard → Developers → Webhooks
2. Add endpoint: `https://yourdomain.com/webhooks/payments/stripe`
3. Select events to listen for:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.refunded`
4. Copy the webhook signing secret to your `.env`

### Step 7: Update CheckoutController (Already Done!)

Your `CheckoutController` already uses the `charge()` method which is backward compatible. The new architecture works seamlessly!

## 🧪 Testing the Setup

### Test Payment Creation

```php
use Elevate\Payments\Models\PaymentGateway;
use Elevate\Payments\Services\PaymentService;

$gateway = PaymentGateway::where('name', 'Stripe')->first();
$paymentService = app(PaymentService::class);

$response = $paymentService->initiatePayment(
    gatewayModel: $gateway,
    amount: 10.00,
    currency: 'GBP',
    metadata: [
        'order_id' => 1,
        'customer_email' => 'test@example.com',
    ]
);

if ($response->isSuccessful()) {
    echo "Payment ID: " . $response->paymentId;
    echo "Client Secret: " . $response->data['client_secret'];
}
```

### Check Logs

After placing an order, check `storage/logs/laravel.log` for:
- Payment initiation logs
- Transaction creation logs
- Gateway-specific logs

### Verify Database

After a payment, check:
```sql
SELECT * FROM transactions WHERE order_id = YOUR_ORDER_ID;
```

## 📊 Database Schema Overview

### payment_gateways
- Stores gateway configuration (Stripe, PayPal, etc.)
- Encrypted credentials (test and live)
- Enable/disable per gateway
- Sort order for display

### transactions
- Records every payment attempt
- Links to orders
- Stores gateway response
- Tracks payment status
- Supports refunds

### refunds
- Records refund requests
- Links to transactions
- Tracks refund status
- Stores gateway response

## 🔄 Migration from Old System

The old `PaymentGateway` model structure is preserved for backward compatibility. The new migrations add:
- `display_name` field
- `sort_order` field
- Better indexing

Existing data should work without changes!

## 🚀 Next Steps

1. ✅ Run migrations
2. ✅ Install Stripe SDK
3. ✅ Seed gateway data
4. ✅ Add environment variables
5. ✅ Register webhook routes
6. ✅ Configure Stripe webhooks
7. ⏳ Test a payment
8. ⏳ Add PayPal gateway (when needed)
9. ⏳ Add admin UI for gateway management
10. ⏳ Implement payment analytics

## 🆘 Troubleshooting

### "Stripe gateway not configured"
- Check that PaymentGateway record exists with name "Stripe"
- Verify credentials are set in database
- Check `is_enabled` is true

### "Transaction not created"
- Ensure `order_id` is in metadata
- Check logs for errors
- Verify migrations ran successfully

### Webhook signature verification fails
- Check webhook secret matches Stripe dashboard
- Verify webhook URL is correct
- Check CSRF protection is disabled for webhooks

## 📝 Notes

- The old `charge()` method still works for backward compatibility
- New code should use `initiatePayment()` for better type safety
- All payment operations are logged comprehensively
- Transaction records are created automatically
- Gateway fallback is available via `initiatePaymentWithFallback()`
