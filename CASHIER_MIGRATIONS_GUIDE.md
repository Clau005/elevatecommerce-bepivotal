# Cashier Migrations & Models Guide

## 📋 **What Cashier Provides**

### **Cashier Migrations (Automatic):**

Cashier comes with **3 migrations** that will be published:

#### **1. `create_customer_columns.php`**
Adds columns to your **existing `users` table**:
```php
$table->string('stripe_id')->nullable()->index();
$table->string('pm_type')->nullable();
$table->string('pm_last_four', 4)->nullable();
$table->timestamp('trial_ends_at')->nullable();
```

**Purpose:**
- `stripe_id` - Stripe customer ID (e.g., `cus_ABC123`)
- `pm_type` - Payment method type (e.g., `card`, `sepa_debit`)
- `pm_last_four` - Last 4 digits of card
- `trial_ends_at` - For subscription trials

#### **2. `create_subscriptions_table.php`**
Creates a new **`subscriptions`** table:
```php
id
user_id (foreign key to users)
type (e.g., 'default', 'premium')
stripe_id (Stripe subscription ID)
stripe_status (active, canceled, etc.)
stripe_price (Stripe price ID)
quantity
trial_ends_at
ends_at
timestamps
```

**Purpose:** Track user subscriptions

#### **3. `create_subscription_items_table.php`**
Creates a new **`subscription_items`** table:
```php
id
subscription_id (foreign key to subscriptions)
stripe_id (Stripe subscription item ID)
stripe_product (Stripe product ID)
stripe_price (Stripe price ID)
quantity
timestamps
```

**Purpose:** Track individual items in a subscription (for metered billing, multiple products)

---

## ✅ **What You Already Have (Perfect!)**

### **Your Existing Tables:**

#### **`orders` table** ✅
```php
✅ stripe_checkout_session_id - For one-time payments
✅ user_id
✅ total
✅ status
✅ meta (can store payment_intent, customer_id, etc.)
```

**You don't need to change this!** It's perfect for one-time payments.

#### **`users` table** ✅
Will be **extended** by Cashier migration to add:
- `stripe_id`
- `pm_type`
- `pm_last_four`
- `trial_ends_at`

---

## 🎯 **What You Need to Do**

### **Step 1: Publish Cashier Migrations**
```bash
php artisan vendor:publish --tag="cashier-migrations"
```

This will copy 3 migration files to your `database/migrations/` folder.

### **Step 2: Run Migrations**
```bash
php artisan migrate
```

This will:
- ✅ Add Stripe columns to `users` table
- ✅ Create `subscriptions` table
- ✅ Create `subscription_items` table

### **Step 3: Add Billable Trait to User Model**
```php
// app/Models/User.php
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable;
    
    // Your existing code...
}
```

---

## 📊 **Database Structure After Cashier**

### **For One-Time Payments (Your Current Use Case):**

```
┌─────────────┐
│   users     │
├─────────────┤
│ id          │
│ stripe_id   │ ← Added by Cashier
│ pm_type     │ ← Added by Cashier
│ pm_last_four│ ← Added by Cashier
└─────────────┘
       │
       │ has many
       ▼
┌─────────────┐
│   orders    │
├─────────────┤
│ id          │
│ user_id     │
│ stripe_checkout_session_id │ ← You already have this!
│ total       │
│ status      │
│ meta        │ ← Store payment_intent, etc.
└─────────────┘
```

**How it works:**
1. User clicks checkout
2. Create Stripe Checkout Session via Cashier
3. User pays at Stripe
4. Stripe redirects back with `session_id`
5. You create Order with `stripe_checkout_session_id`
6. Cashier automatically creates/updates Stripe customer

### **For Subscriptions (Future):**

```
┌─────────────┐
│   users     │
├─────────────┤
│ id          │
│ stripe_id   │
└─────────────┘
       │
       │ has many
       ▼
┌─────────────────┐
│ subscriptions   │
├─────────────────┤
│ id              │
│ user_id         │
│ stripe_id       │
│ stripe_status   │
│ stripe_price    │
└─────────────────┘
       │
       │ has many
       ▼
┌─────────────────────┐
│ subscription_items  │
├─────────────────────┤
│ id                  │
│ subscription_id     │
│ stripe_product      │
│ stripe_price        │
└─────────────────────┘
```

---

## 🔍 **Do You Need Custom Models?**

### **NO! Cashier provides everything:**

✅ **User model** - Just add `Billable` trait  
✅ **Subscription model** - Provided by Cashier  
✅ **SubscriptionItem model** - Provided by Cashier  

### **You keep your existing models:**

✅ **Order model** - For order tracking  
✅ **OrderLine model** - For order items  
✅ **Cart model** - For shopping cart  
✅ **Wishlist model** - For wishlists  

---

## 💡 **Payment Data Storage Strategy**

### **One-Time Payments:**

**Option 1: Store in Order meta (Recommended)**
```php
$order->update([
    'stripe_checkout_session_id' => $session->id,
    'meta' => [
        'stripe_payment_intent' => $session->payment_intent,
        'stripe_customer' => $session->customer,
        'stripe_charge' => $charge->id,
        'card_brand' => $charge->payment_method_details->card->brand,
        'card_last4' => $charge->payment_method_details->card->last4,
    ],
]);
```

**Option 2: Query Stripe when needed**
```php
// Cashier provides helper methods
$user = auth()->user();
$paymentMethods = $user->paymentMethods();
$defaultPaymentMethod = $user->defaultPaymentMethod();
```

### **Subscriptions:**

**Cashier handles everything automatically:**
```php
// Check if user has subscription
if ($user->subscribed('default')) {
    // User has active subscription
}

// Get subscription
$subscription = $user->subscription('default');

// Check status
$subscription->active();
$subscription->canceled();
$subscription->onTrial();
```

---

## ✅ **Summary: What You Need**

### **Migrations:**
- ✅ Publish Cashier migrations (3 files)
- ✅ Run `php artisan migrate`

### **Models:**
- ✅ Add `Billable` trait to User model
- ✅ Keep all your existing models (Order, Cart, etc.)

### **Custom Tables:**
- ❌ **NO** - Cashier provides everything
- ✅ Your existing `orders` table is perfect

### **Payment Tracking:**
- ✅ Store `stripe_checkout_session_id` in orders (you already have this!)
- ✅ Store payment details in `order->meta` (optional)
- ✅ Cashier tracks customer data in `users` table

---

## 🚀 **Next Steps**

1. ✅ Run `php artisan vendor:publish --tag="cashier-migrations"`
2. ✅ Run `php artisan migrate`
3. ✅ Add `Billable` trait to User model
4. ✅ Implement checkout controller
5. ✅ Test checkout flow

**You don't need any custom payment models!** Cashier + your existing Order model = Perfect! 🎉
