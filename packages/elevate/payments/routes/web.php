<?php

use Illuminate\Support\Facades\Route;
use Elevate\Payments\Http\Controllers\WebhookController;

// CHECKOUT MOVED TO elevate/commerce-core
// The payments package should NOT have checkout routes
// Checkout is now handled by: packages/elevate/commerce-core/src/Http/Controllers/CheckoutController.php

// Webhook routes (excluded from CSRF protection)
Route::post('/webhooks/payments/stripe', [WebhookController::class, 'handleStripe'])
    ->name('webhooks.payments.stripe');

Route::post('/webhooks/payments/paypal', [WebhookController::class, 'handlePayPal'])
    ->name('webhooks.payments.paypal');

Route::post('/webhooks/payments/{gateway}', [WebhookController::class, 'handle'])
    ->name('webhooks.payments.generic');
