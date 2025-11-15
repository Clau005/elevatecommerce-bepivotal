# ElevateCommerce - Getting Started Guide

## Step 1: Create a Fresh Laravel Project

```bash
composer create-project laravel/laravel your-project-name
cd your-project-name
```

## Step 2: Modify the Default Users Migration

Open `database/migrations/0001_01_01_000000_create_users_table.php` and **remove** the `name` field:

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    // Remove this line: $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

**Note:** The `name` field will be replaced by `title`, `first_name`, `last_name`, `company_name`, `account_reference`, and `tax_identifier` from the core package. Everything else stays the same.

## Step 3: Install ElevateCommerce Core Package

Add the package repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/elevatecommerce/core"
        }
    ]
}
```

Then require the package:

```bash
composer require elevatecommerce/core
```

## Step 4: Run Migrations

The core package will automatically add customer fields to the `users` table and create the `admins` table:

```bash
php artisan migrate
```

This will:
- Add `title`, `first_name`, `last_name`, `company_name`, `account_reference`, `tax_identifier` to the `users` table
- Create the `admins` table for admin authentication

## Step 5: Update Your User Model

In `app/Models/User.php`, extend the Customer model:

```php
<?php

namespace App\Models;

use ElevateCommerce\Core\Models\Customer;

class User extends Customer
{
    // Your custom user logic here
}
```

## Authentication Setup

The package provides two authentication guards:

### 1. **Web Guard (Customers)**
- Uses the `users` table
- Model: `ElevateCommerce\Core\Models\Customer`
- Default Laravel authentication

### 2. **Admin Guard**
- Uses the `admins` table  
- Model: `ElevateCommerce\Core\Models\Admin`
- Guard: `admin`

### Usage Examples

**Customer Login:**
```php
Auth::guard('web')->attempt($credentials);
// or simply
Auth::attempt($credentials);
```

**Admin Login:**
```php
Auth::guard('admin')->attempt($credentials);
```

**Protecting Routes:**
```php
// Customer routes
Route::middleware('auth:web')->group(function () {
    // Customer dashboard, orders, etc.
});

// Admin routes
Route::middleware('auth:admin')->group(function () {
    // Admin panel
});
```

## Models Overview

### Customer Model
- Table: `users`
- Fields: `title`, `first_name`, `last_name`, `company_name`, `account_reference`, `tax_identifier`, `email`, `password`
- Accessor: `$customer->name` returns full name

### Admin Model
- Table: `admins`
- Fields: `first_name`, `last_name`, `email`, `password`, `is_super_admin`
- Accessor: `$admin->name` returns full name

## Next Steps

1. Create seeders for default admin users
2. Set up admin and customer authentication routes
3. Build your admin panel
4. Customize the Customer model in `app/Models/User.php` as needed