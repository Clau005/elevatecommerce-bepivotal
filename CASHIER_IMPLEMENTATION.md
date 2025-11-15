# Laravel Cashier Implementation Plan

## Overview
We're implementing Laravel Cashier for both **one-time payments** and **subscriptions** to future-proof the platform.

---

## Phase 1: Setup (Now)

### 1. Install Cashier ✅
```bash
composer require laravel/cashier
```

### 2. Publish Cashier Migrations
```bash
php artisan vendor:publish --tag="cashier-migrations"
php artisan migrate
```

### 3. Add Billable Trait to User Model
```php
// app/Models/User.php
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable;
}
```

### 4. Configure Stripe Keys
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

---

## Phase 2: One-Time Payments (Quick-Pay for Launch)

### Checkout Flow with Cashier

**Cart → Stripe Checkout → Success**

```php
// CheckoutController.php
public function checkout(Request $request)
{
    $cart = Cart::getCart(session()->getId(), auth()->id());
    
    // Build line items
    $lineItems = $cart->lines->map(fn($line) => [
        'price_data' => [
            'currency' => 'gbp',
            'product_data' => [
                'name' => $line->purchasable->getName(),
            ],
            'unit_amount' => $line->purchasable->getUnitPrice(),
        ],
        'quantity' => $line->quantity,
    ])->toArray();
    
    // Create Stripe Checkout Session via Cashier
    return auth()->user()->checkout($lineItems, [
        'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => route('checkout.cancel'),
        'shipping_address_collection' => [
            'allowed_countries' => ['GB', 'US'],
        ],
        'metadata' => [
            'cart_id' => $cart->id,
        ],
    ]);
}
```

### Success Handler
```php
public function success(Request $request)
{
    $sessionId = $request->session_id;
    $session = auth()->user()->findCheckoutSession($sessionId);
    
    // Create Order
    $order = Order::create([
        'user_id' => auth()->id(),
        'stripe_checkout_session_id' => $session->id,
        'total' => $session->amount_total,
        // ...
    ]);
    
    // Store payment info
    $order->update([
        'meta' => [
            'stripe_payment_intent' => $session->payment_intent,
            'stripe_customer' => $session->customer,
        ],
    ]);
    
    // Clear cart
    $cart->delete();
    
    return view('checkout.success', compact('order'));
}
```

---

## Phase 3: Subscriptions (Future)

### Product Model with Subscription Support
```php
class Product extends Model
{
    use Purchasable;
    
    // One-time purchase
    public function getUnitPrice(): int
    {
        return $this->price;
    }
    
    // Subscription
    public function getStripePriceId(): ?string
    {
        return $this->stripe_price_id; // e.g., price_1ABC...
    }
    
    public function isSubscription(): bool
    {
        return !empty($this->stripe_price_id);
    }
}
```

### Subscribe to Product
```php
// For subscription products
public function subscribe(Request $request, Product $product)
{
    if (!$product->isSubscription()) {
        abort(400, 'This product is not a subscription');
    }
    
    return $request->user()
        ->newSubscription('default', $product->getStripePriceId())
        ->checkout([
            'success_url' => route('subscription.success'),
            'cancel_url' => route('subscription.cancel'),
        ]);
}
```

### Manage Subscriptions
```php
// Customer portal
public function billingPortal(Request $request)
{
    return $request->user()->redirectToBillingPortal(
        route('account.billing')
    );
}

// Check subscription status
if ($user->subscribed('default')) {
    // Has active subscription
}

// Cancel subscription
$user->subscription('default')->cancel();

// Resume subscription
$user->subscription('default')->resume();
```

---

## Database Schema

### Orders Table (Existing)
```php
$table->string('stripe_checkout_session_id')->nullable();
$table->json('meta'); // Store payment details
```

### Cashier Tables (Auto-created)
- `subscriptions` - User subscriptions
- `subscription_items` - Subscription line items
- `customers` - Stripe customer IDs (polymorphic)

---

## Benefits of Cashier

✅ **One-Time Payments**
- Stripe Checkout hosted page
- Automatic customer creation
- Payment intent tracking
- Webhook handling

✅ **Subscriptions**
- Easy subscription management
- Billing portal
- Prorated upgrades/downgrades
- Trial periods
- Metered billing

✅ **Unified API**
- Same user model for both
- Consistent checkout flow
- Built-in invoice generation

---

## Migration Path

### Week 1 (Launch)
- ✅ Install Cashier
- ✅ One-time payments via Checkout
- ✅ Order creation
- ✅ Basic webhooks

### Week 2+
- Add subscription products
- Customer billing portal
- Invoice management
- Subscription analytics

---

## Webhooks to Handle

```php
// routes/web.php
Route::post(
    'stripe/webhook',
    [WebhookController::class, 'handleWebhook']
);

// WebhookController.php
public function handleWebhook(Request $request)
{
    $payload = $request->getContent();
    
    // Cashier handles most webhooks automatically
    // Custom handling for:
    // - checkout.session.completed → Create Order
    // - payment_intent.succeeded → Update Order
    // - customer.subscription.updated → Sync subscription
}
```

---

## Next Steps

1. ✅ Run `composer update` to install Cashier
2. Run `php artisan vendor:publish --tag="cashier-migrations"`
3. Run `php artisan migrate`
4. Add `Billable` trait to User model
5. Update CheckoutController to use Cashier
6. Test checkout flow
7. Configure webhooks in Stripe Dashboard

---

## Resources

- [Laravel Cashier Docs](https://laravel.com/docs/11.x/billing)
- [Stripe Checkout](https://stripe.com/docs/payments/checkout)
- [Stripe Subscriptions](https://stripe.com/docs/billing/subscriptions/overview)
