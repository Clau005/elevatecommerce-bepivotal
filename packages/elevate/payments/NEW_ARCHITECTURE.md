# Payment Package - New Architecture

## Overview

The payment package has been completely refactored to follow a modern, extensible multi-gateway architecture pattern. This new structure provides:

- **Unified Interface** - Consistent API across all payment gateways
- **Easy Gateway Switching** - Change providers without code changes
- **Better Testability** - Mock payment gateways easily
- **Improved Maintainability** - Gateway-specific code is isolated
- **Gateway Fallback** - Automatic fallback to alternative gateways
- **Comprehensive Logging** - Track every step of payment processing

## Architecture

```
┌─────────────────────┐
│  CheckoutController │
└──────────┬──────────┘
           │
    ┌──────▼──────────┐
    │ PaymentService  │
    └──────┬──────────┘
           │
    ┌──────▼────────────────┐
    │ PaymentGatewayManager │
    └──────┬────────────────┘
           │
    ┌──────▼──────────────────┐
    │ PaymentGatewayInterface │
    └──┬──────┬──────┬────────┘
       │      │      │
   ┌───▼──┐ ┌▼────┐ ┌▼──────┐
   │Stripe│ │PayPal│ │Future │
   │      │ │      │ │Gateway│
   └──────┘ └──────┘ └───────┘
```

## Key Components

### 1. PaymentGatewayInterface
**Location:** `src/Contracts/PaymentGatewayInterface.php`

Defines the contract that all payment gateways must implement:
- `createPayment()` - Initiate a new payment
- `capturePayment()` - Capture an authorized payment
- `refundPayment()` - Process a refund
- `getPaymentStatus()` - Check payment status
- `verifyWebhook()` - Verify webhook signatures
- `handleWebhook()` - Process webhook events

### 2. Data Transfer Objects (DTOs)

**PaymentRequest** (`src/DTOs/PaymentRequest.php`)
```php
new PaymentRequest(
    amount: 100.00,
    currency: 'GBP',
    metadata: ['order_id' => 123],
    returnUrl: 'https://...',
    cancelUrl: 'https://...'
)
```

**PaymentResponse** (`src/DTOs/PaymentResponse.php`)
```php
new PaymentResponse(
    success: true,
    paymentId: 'pi_123',
    redirectUrl: null,
    status: 'succeeded',
    data: ['client_secret' => '...'],
    error: null
)
```

### 3. PaymentGatewayManager
**Location:** `src/Services/PaymentGatewayManager.php`

Centralized gateway management:
- Registers all available gateways
- Provides gateway instances by name or model
- Lists active gateways from database
- Returns default gateway

### 4. Gateway Implementations

**StripeGateway** (`src/Services/Gateways/StripeGateway.php`)
- Implements PaymentGatewayInterface
- Loads configuration from database
- Handles Stripe-specific logic
- Comprehensive logging

**Future Gateways:**
- PayPalGateway
- WorldpayGateway
- etc.

### 5. PaymentService
**Location:** `src/Services/PaymentService.php`

High-level payment operations:
- `initiatePayment()` - Start a payment
- `capturePayment()` - Capture authorized payment
- `refundPayment()` - Process refunds
- `getPaymentStatus()` - Check status
- `initiatePaymentWithFallback()` - Try multiple gateways

### 6. WebhookController
**Location:** `src/Http/Controllers/WebhookController.php`

Unified webhook handling:
- `handleStripe()` - Stripe webhooks
- `handlePayPal()` - PayPal webhooks
- `handle()` - Generic webhook router

## Usage Examples

### Basic Payment

```php
use Elevate\Payments\Models\PaymentGateway;
use Elevate\Payments\Services\PaymentService;

$gateway = PaymentGateway::where('name', 'Stripe')->first();
$paymentService = app(PaymentService::class);

$response = $paymentService->initiatePayment(
    gatewayModel: $gateway,
    amount: 100.00,
    currency: 'GBP',
    metadata: [
        'order_id' => $order->id,
        'customer_id' => $customer->id,
    ]
);

if ($response->isSuccessful()) {
    // Payment initiated successfully
    $clientSecret = $response->data['client_secret'];
    // Use client secret for Stripe Elements
}
```

### Payment with Fallback

```php
$response = $paymentService->initiatePaymentWithFallback(
    amount: 100.00,
    currency: 'GBP',
    metadata: ['order_id' => $order->id]
);

// Automatically tries all enabled gateways in order
// Returns first successful response
```

### Capture Payment

```php
$response = $paymentService->capturePayment(
    gatewayModel: $gateway,
    paymentId: 'pi_123'
);
```

### Refund Payment

```php
$response = $paymentService->refundPayment(
    gatewayModel: $gateway,
    paymentId: 'pi_123',
    amount: 50.00 // Partial refund, or null for full refund
);
```

## Backward Compatibility

The old `charge()` method is still available for backward compatibility:

```php
$response = $paymentService->charge(
    gateway: $gateway,
    amount: 100.00,
    currency: 'GBP',
    description: 'Order #123',
    metadata: ['order_id' => 123]
);
```

This method internally calls `initiatePayment()` and returns a `PaymentResponse`.

## Adding a New Gateway

1. **Create Gateway Class**
```php
namespace Elevate\Payments\Services\Gateways;

use Elevate\Payments\Contracts\PaymentGatewayInterface;
use Elevate\Payments\DTOs\{PaymentRequest, PaymentResponse};

class PayPalGateway implements PaymentGatewayInterface
{
    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        // Implement PayPal-specific logic
    }
    
    // Implement other interface methods...
}
```

2. **Register in PaymentGatewayManager**
```php
protected function registerGateways(): void
{
    $this->gateways = [
        'stripe' => app(StripeGateway::class),
        'paypal' => app(PayPalGateway::class), // Add here
    ];
}
```

3. **Add Database Record**
```php
PaymentGateway::create([
    'name' => 'PayPal',
    'driver' => 'paypal',
    'is_enabled' => true,
    'test_mode' => true,
    'credentials' => [...],
    'test_credentials' => [...],
]);
```

## Webhook Setup

### Routes
Add to your routes file:
```php
Route::post('/webhooks/payments/stripe', [WebhookController::class, 'handleStripe']);
Route::post('/webhooks/payments/paypal', [WebhookController::class, 'handlePayPal']);
Route::post('/webhooks/payments/{gateway}', [WebhookController::class, 'handle']);
```

### Webhook URLs
- Stripe: `https://yourdomain.com/webhooks/payments/stripe`
- PayPal: `https://yourdomain.com/webhooks/payments/paypal`

## Logging

All payment operations are comprehensively logged:

```
[INFO] Initiating payment
[INFO] Payment gateway loaded
[INFO] Creating Stripe payment intent
[INFO] Stripe payment intent created
[INFO] Payment initiation result
```

Check `storage/logs/laravel.log` for detailed payment flow tracking.

## Testing

```php
use Tests\TestCase;
use Elevate\Payments\Services\PaymentService;
use Elevate\Payments\Models\PaymentGateway;

class PaymentTest extends TestCase
{
    public function test_stripe_payment()
    {
        $gateway = PaymentGateway::factory()->create(['name' => 'Stripe']);
        $service = app(PaymentService::class);
        
        $response = $service->initiatePayment(
            gatewayModel: $gateway,
            amount: 100.00,
            currency: 'GBP'
        );
        
        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->paymentId);
    }
}
```

## Benefits

1. **Consistency** - All gateways use the same interface
2. **Flexibility** - Easy to switch or add gateways
3. **Reliability** - Automatic fallback support
4. **Maintainability** - Clear separation of concerns
5. **Testability** - Easy to mock and test
6. **Observability** - Comprehensive logging throughout

## Migration from Old Architecture

The old Omnipay-based system is deprecated but still works through the backward-compatible `charge()` method. New code should use `initiatePayment()`.

**Old Way:**
```php
$result = $paymentService->charge($gatewayId, $amount, $paymentData);
// Returns array
```

**New Way:**
```php
$response = $paymentService->initiatePayment($gateway, $amount, $currency, $metadata);
// Returns PaymentResponse DTO
```

## Next Steps

1. ✅ Core architecture implemented
2. ✅ Stripe gateway implemented
3. ⏳ Add PayPal gateway
4. ⏳ Add webhook route registration
5. ⏳ Create admin UI for gateway management
6. ⏳ Add payment transaction tracking
7. ⏳ Implement retry logic
8. ⏳ Add payment analytics

## Questions?

Check the article reference: `payment-gateway-article.md`
