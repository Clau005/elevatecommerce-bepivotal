<?php

use Illuminate\Support\Facades\Route;

// CHECKOUT MOVED TO elevate/commerce-core
// The payments package should NOT have checkout routes
// Checkout is now handled by: packages/elevate/commerce-core/src/Http/Controllers/CheckoutController.php

// No public routes needed for payments package
// All payment processing is done via the commerce-core checkout
