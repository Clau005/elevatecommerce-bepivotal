# Order & Payment Status Architecture

## Overview
Separate order fulfillment status from payment status for better clarity and workflow management.

## Order Status (Fulfillment Workflow)

### Status Values
1. **created** - Order placed, awaiting payment confirmation
2. **confirmed** - Payment received, order ready to process
3. **processing** - Order being prepared/packed
4. **shipped** - Order dispatched to customer
5. **delivered** - Order received by customer
6. **cancelled** - Order cancelled (before shipping)
7. **refunded** - Order refunded (after payment)

### Status Flow
```
created → confirmed → processing → shipped → delivered
   ↓          ↓
cancelled  refunded
```

## Transaction/Payment Status

### Status Values
1. **pending** - Payment initiated, awaiting confirmation from gateway
2. **authorized** - Payment authorized but not yet captured
3. **completed** - Payment successful and money captured
4. **failed** - Payment attempt failed
5. **refunded** - Full refund issued
6. **partially_refunded** - Partial refund issued
7. **cancelled** - Payment cancelled before completion

### Status Flow
```
pending → completed
   ↓          ↓
failed    refunded / partially_refunded

OR

pending → authorized → completed
   ↓
cancelled
```

## Combined Workflow Example

### Successful Order
1. Customer places order
   - Order: `created`
   - Transaction: `pending`

2. Stripe confirms payment (webhook)
   - Transaction: `completed`
   - Order: `confirmed` (auto-updated)

3. Staff processes order
   - Order: `processing`
   - Transaction: `completed`

4. Order shipped
   - Order: `shipped`
   - Transaction: `completed`

5. Customer receives
   - Order: `delivered`
   - Transaction: `completed`

### Failed Payment
1. Customer places order
   - Order: `created`
   - Transaction: `pending`

2. Payment fails
   - Transaction: `failed`
   - Order: `cancelled` (auto-updated)

### Refund Scenario
1. Order delivered
   - Order: `delivered`
   - Transaction: `completed`

2. Customer requests refund
   - Transaction: `refunded`
   - Order: `refunded`

## Database Changes Needed

### Orders Table
```php
// Current: 'status' column
// Update to use new values:
'created', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'
```

### Transactions Table
```php
// Current: 'status' column  
// Already has: 'pending', 'completed', 'failed'
// Add: 'authorized', 'refunded', 'partially_refunded', 'cancelled'
```

## Implementation Tasks

### 1. Update Order Creation
```php
// CheckoutController
$order->status = 'created'; // Instead of 'pending'
```

### 2. Update Transaction Status on Payment Success
```php
// StripeGateway webhook handler
$transaction->update(['status' => 'completed']);
$order->update(['status' => 'confirmed']);
```

### 3. Update Admin UI
- Order status dropdown: created, confirmed, processing, shipped, delivered, cancelled, refunded
- Show transaction status separately
- Display both statuses clearly

### 4. Add Status Helper Methods
```php
// Order model
public function isConfirmed(): bool
{
    return $this->status === 'confirmed';
}

public function canBeCancelled(): bool
{
    return in_array($this->status, ['created', 'confirmed']);
}

public function canBeShipped(): bool
{
    return in_array($this->status, ['confirmed', 'processing']);
}

// Transaction model
public function isPending(): bool
{
    return $this->status === 'pending';
}

public function isCompleted(): bool
{
    return $this->status === 'completed';
}

public function canBeRefunded(): bool
{
    return $this->status === 'completed';
}
```

## Display in Admin

### Order Header
```
Order #12345 | Created | Payment: Completed
```

### Order Details Card
```
Order Status: Confirmed
Payment Status: Completed
Payment Method: Stripe
Transaction ID: pi_xxx
```

## Benefits

1. **Clear Separation** - Order fulfillment separate from payment
2. **Better Workflow** - Staff knows exactly what to do
3. **Accurate Reporting** - Can track payment vs fulfillment metrics
4. **Flexible** - Can handle pre-auth, delayed capture, etc.
5. **Customer Clarity** - "Your order is confirmed" vs "Payment pending"

## Migration Strategy

### Option 1: Update Existing Orders
```sql
UPDATE orders SET status = 'created' WHERE status = 'pending';
UPDATE orders SET status = 'confirmed' WHERE status = 'processing';
```

### Option 2: Map Old to New
Keep backward compatibility with a status mapper:
```php
public function getLegacyStatus(): string
{
    return match($this->status) {
        'created' => 'pending',
        'confirmed' => 'payment-received',
        'processing' => 'processing',
        // ...
    };
}
```

## Next Steps

1. ✅ Design approved
2. Update CheckoutController to set status = 'created'
3. Update order show page with new statuses
4. Implement webhook to update transaction + order status
5. Add transaction status display to order page
6. Update order list to show both statuses
7. Add helper methods to models
8. Test complete flow
