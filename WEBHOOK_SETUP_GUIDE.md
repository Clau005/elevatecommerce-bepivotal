# Stripe Webhook Setup Guide

## ✅ What's Been Implemented

### 1. Webhook Handler
- ✅ `StripeGateway::handlePaymentSucceeded()` - Updates transaction & order when payment succeeds
- ✅ `StripeGateway::handlePaymentFailed()` - Handles failed payments
- ✅ Transaction status updates
- ✅ Order status auto-confirmation
- ✅ Timeline events added to orders
- ✅ CSRF protection excluded for webhook routes

### 2. Payment Flow
```
Customer places order
  ↓
Order Status: created
Transaction Status: pending
  ↓
Stripe processes payment
  ↓
Stripe sends webhook to your server
  ↓
Webhook handler updates:
  - Transaction Status: completed
  - Order Status: confirmed
  - Timeline: "Payment Completed"
```

## 🔧 Configuration Steps

### Step 1: Ensure Webhook Secret is Set

Add to your `.env` file:
```env
STRIPE_TEST_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

### Step 2: Configure Stripe Webhook

1. Go to [Stripe Dashboard](https://dashboard.stripe.com/test/webhooks)
2. Click "Add endpoint"
3. Enter your webhook URL:
   ```
   https://yourdomain.com/webhooks/payments/stripe
   ```
4. Select events to listen for:
   - ✅ `payment_intent.succeeded`
   - ✅ `payment_intent.payment_failed`
   - ✅ `charge.refunded` (optional, for refunds)

5. Copy the "Signing secret" (starts with `whsec_`)
6. Add it to your `.env` as `STRIPE_TEST_WEBHOOK_SECRET`

### Step 3: Update Payment Gateway in Database

Run this seeder again to update the webhook secret:
```bash
php artisan db:seed --class=PaymentGatewaySeeder
```

Or manually update in database:
```sql
UPDATE payment_gateways 
SET test_credentials = JSON_SET(
    test_credentials, 
    '$.webhook_secret', 
    'whsec_your_secret_here'
)
WHERE name = 'Stripe';
```

### Step 4: Test the Webhook

#### Option A: Use Stripe CLI (Recommended for Local Development)
```bash
# Install Stripe CLI
brew install stripe/stripe-cli/stripe

# Login
stripe login

# Forward webhooks to your local server
stripe listen --forward-to http://localhost:8000/webhooks/payments/stripe

# In another terminal, trigger a test payment
stripe trigger payment_intent.succeeded
```

#### Option B: Use Stripe Dashboard
1. Go to Webhooks in Stripe Dashboard
2. Click on your webhook
3. Click "Send test webhook"
4. Select `payment_intent.succeeded`
5. Click "Send test webhook"

## 📊 What Happens When Payment Succeeds

### Database Changes
```sql
-- Transaction updated
UPDATE transactions 
SET status = 'completed', 
    completed_at = NOW()
WHERE transaction_id = 'pi_xxx';

-- Order updated
UPDATE orders 
SET status = 'confirmed'
WHERE id = 123;

-- Timeline event created
INSERT INTO order_timelines (
    order_id, 
    type, 
    title, 
    content,
    is_system_event,
    is_visible_to_customer
) VALUES (
    123,
    'payment_completed',
    'Payment Completed',
    'Payment of £250.00 completed successfully via Stripe',
    1,
    1
);
```

### Admin View
- Order status badge changes from gray "Created" to blue "Confirmed"
- Timeline shows "Payment Completed" event
- Transaction shows status "completed"

## 🧪 Testing Checklist

### Local Testing
- [ ] Stripe CLI installed and logged in
- [ ] Webhook forwarding active (`stripe listen`)
- [ ] Place a test order
- [ ] Check logs for webhook received
- [ ] Verify order status changed to "confirmed"
- [ ] Verify transaction status is "completed"
- [ ] Check timeline for payment event

### Production Testing
- [ ] Webhook endpoint configured in Stripe Dashboard
- [ ] Webhook secret added to production `.env`
- [ ] SSL certificate valid (webhooks require HTTPS)
- [ ] Place a real test order
- [ ] Verify webhook received in Stripe Dashboard
- [ ] Check order confirmation

## 🔍 Debugging

### Check Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Look for these messages:
# "Handling Stripe webhook"
# "Stripe payment succeeded"
# "Transaction marked as completed"
# "Order confirmed after payment"
```

### Stripe Dashboard
1. Go to Webhooks
2. Click on your endpoint
3. View "Recent deliveries"
4. Check response codes:
   - ✅ 200 = Success
   - ❌ 4xx/5xx = Error

### Common Issues

**Webhook not received:**
- Check URL is correct
- Ensure HTTPS in production
- Verify CSRF exception is working

**Webhook received but order not updating:**
- Check `transaction_id` matches between database and Stripe
- Verify webhook secret is correct
- Check Laravel logs for errors

**Transaction not found:**
- Ensure transaction is created before payment completes
- Check `transaction_id` column matches Stripe payment intent ID

## 📝 Webhook Payload Example

```json
{
  "id": "evt_xxx",
  "type": "payment_intent.succeeded",
  "data": {
    "object": {
      "id": "pi_xxx",
      "amount": 25000,
      "currency": "gbp",
      "status": "succeeded",
      "metadata": {
        "order_id": "123",
        "order_reference": "12345678"
      }
    }
  }
}
```

## 🚀 Next Steps

1. **Test locally** with Stripe CLI
2. **Deploy to staging** and test with Stripe test mode
3. **Configure production** webhook with live keys
4. **Monitor** webhook deliveries in Stripe Dashboard

## 📚 Additional Resources

- [Stripe Webhooks Documentation](https://stripe.com/docs/webhooks)
- [Stripe CLI Documentation](https://stripe.com/docs/stripe-cli)
- [Testing Webhooks](https://stripe.com/docs/webhooks/test)

## ⚠️ Security Notes

- ✅ Webhook signature verification implemented
- ✅ CSRF protection excluded for webhook routes
- ✅ Webhook secret stored encrypted in database
- ⚠️ Always use HTTPS in production
- ⚠️ Never commit webhook secrets to git
