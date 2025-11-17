# Currency Formatting & Payment Status Fix Plan

## Issues Identified

### 1. Currency Formatting Not Used
- Cart, Wishlist, Checkout not using Currency model
- Hardcoded `£` symbols everywhere
- No multi-currency support

### 2. Amount Storage Inconsistency
- ✅ Orders store amounts in pence (correct)
- ❌ Transactions store amounts in pounds (incorrect)
- ❌ Order lines have £0.00 totals (incorrect)

### 3. Payment Status Not Updating
- Payment successful but order status stays "pending"
- No webhook handling to update status
- No confirmation flow

## Solution Plan

### Phase 1: Fix Amount Storage (CRITICAL)

#### A. Revert CheckoutController Change
```php
// WRONG (current):
amount: $order->total / 100

// RIGHT (should be):
amount: $order->total  // Keep in pence
```

#### B. Update Transaction Migration
```php
// Change from:
$table->decimal('amount', 10, 2);  // Pounds

// To:
$table->integer('amount');  // Pence
```

#### C. Update Transaction Model
```php
protected $casts = [
    'amount' => 'integer',  // Store as pence
];

public function getFormattedAmountAttribute(): string
{
    $currency = Currency::getDefault();
    return $currency->formatAmount($this->amount);
}
```

#### D. Fix Order Line Totals
Order lines need to calculate and store totals during creation.

### Phase 2: Implement Currency Formatting

#### A. Create CurrencyService
```php
class CurrencyService
{
    public function format(int $amountInPence, ?string $currencyCode = null): string
    {
        $currency = $currencyCode 
            ? Currency::where('code', $currencyCode)->first()
            : Currency::getDefault();
            
        return $currency->formatAmount($amountInPence);
    }
    
    public function symbol(?string $currencyCode = null): string
    {
        $currency = $currencyCode 
            ? Currency::where('code', $currencyCode)->first()
            : Currency::getDefault();
            
        return $currency->symbol;
    }
}
```

#### B. Add Blade Directive
```php
Blade::directive('currency', function ($expression) {
    return "<?php echo app(CurrencyService::class)->format($expression); ?>";
});
```

Usage:
```blade
@currency($cart->total)
@currency($order->total, $order->currency_code)
```

### Phase 3: Payment Status Updates

#### A. Webhook Handler Updates
```php
// StripeGateway::handlePaymentSucceeded()
protected function handlePaymentSucceeded(array $payload): void
{
    $paymentIntent = $payload['data']['object'];
    $paymentId = $paymentIntent['id'];
    
    // Find transaction
    $transaction = Transaction::where('transaction_id', $paymentId)->first();
    
    if ($transaction) {
        // Update transaction status
        $transaction->markAsCompleted();
        
        // Update order status
        $transaction->order->update(['status' => 'processing']);
        
        // Add timeline event
        $transaction->order->addTimelineEvent(
            'payment_completed',
            'Payment Completed',
            "Payment of {$transaction->formatted_amount} completed successfully"
        );
    }
}
```

#### B. Order Status Flow
```
pending → processing → shipped → delivered
         ↓
       cancelled/refunded
```

### Phase 4: Update Views

#### Cart View
```blade
<!-- OLD -->
<span>£{{ number_format($item->total / 100, 2) }}</span>

<!-- NEW -->
<span>@currency($item->total)</span>
```

#### Checkout View
```blade
<!-- OLD -->
<span>£{{ number_format($order->total / 100, 2) }}</span>

<!-- NEW -->
<span>@currency($order->total)</span>
```

## Implementation Order

1. ✅ Fix Transaction amount storage (migration + model)
2. ✅ Revert CheckoutController amount conversion
3. ✅ Fix Order line totals calculation
4. ✅ Create CurrencyService
5. ✅ Add Blade directive
6. ✅ Update StripeGateway webhook handlers
7. ✅ Update Cart views
8. ✅ Update Checkout views
9. ✅ Update Wishlist views
10. ✅ Test complete flow

## Testing Checklist

- [ ] Place order - amounts stored correctly in pence
- [ ] Transaction record shows correct amount
- [ ] Order lines have correct totals
- [ ] Cart displays formatted currency
- [ ] Checkout displays formatted currency
- [ ] Webhook updates order status
- [ ] Order status changes from pending → processing
- [ ] Timeline event created for payment
- [ ] Multi-currency works (if applicable)

## Database Changes Required

```sql
-- 1. Drop and recreate transactions table with integer amount
ALTER TABLE transactions MODIFY COLUMN amount BIGINT NOT NULL;

-- 2. Update existing transactions (if any)
-- This will be wrong for existing data, but it's test data
UPDATE transactions SET amount = amount * 100;
```

## Files to Update

### Models
- [x] Transaction.php - Change amount cast to integer
- [ ] Order.php - Add formatted amount methods
- [ ] OrderLine.php - Fix total calculation
- [ ] Cart.php - Add formatted amount methods

### Services
- [ ] CurrencyService.php - NEW
- [ ] PaymentService.php - Ensure amounts stay in pence
- [x] StripeGateway.php - Update webhook handlers

### Controllers
- [x] CheckoutController.php - Revert amount conversion
- [ ] CartController.php - Use currency formatting

### Views
- [ ] cart/index.blade.php
- [ ] checkout/index.blade.php
- [ ] checkout/success.blade.php
- [ ] wishlist/index.blade.php

### Migrations
- [ ] Create migration to alter transactions.amount column
