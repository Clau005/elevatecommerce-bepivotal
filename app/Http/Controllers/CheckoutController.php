<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Elevate\CommerceCore\Models\Cart;
use Elevate\CommerceCore\Models\Order;
use Elevate\CommerceCore\Models\OrderLine;
use Elevate\CommerceCore\Models\OrderAddress;

class CheckoutController extends Controller
{
    /**
     * Initiate Stripe Checkout using Laravel Cashier
     */
    public function checkout(Request $request)
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('storefront.login')
                ->with('error', 'Please login to checkout');
        }

        // Get user's cart
        $cart = Cart::getCart(session()->getId(), auth()->id());
        
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('storefront.cart.index')
                ->with('error', 'Your cart is empty');
        }

        // Validate stock for all items
        foreach ($cart->lines as $line) {
            if (!$line->purchasable) {
                return back()->with('error', 'Some items in your cart are no longer available');
            }
            
            if (!$line->purchasable->isAvailableForPurchase()) {
                return back()->with('error', "{$line->purchasable->getName()} is no longer available");
            }
            
            if (!$line->purchasable->hasStock($line->quantity)) {
                $stock = $line->purchasable->getStockLevel();
                return back()->with('error', "Only {$stock} units of {$line->purchasable->getName()} available");
            }
        }

        // Build Stripe line items from cart
        $lineItems = $cart->lines->map(function($line) {
            $productData = [
                'name' => $line->description,
            ];
            
            // Add image if available
            if ($line->preview) {
                $productData['images'] = [url($line->preview)];
            }
            
            return [
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => $productData,
                    'unit_amount' => $line->unit_price, // Already in cents!
                ],
                'quantity' => $line->quantity,
            ];
        })->toArray();

        // Check if cart requires shipping
        $requiresShipping = $cart->lines->contains(function($line) {
            return $line->purchasable && $line->purchasable->requiresShipping();
        });

        // Build Stripe Checkout session options
        $checkoutOptions = [
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'cart_id' => $cart->id,
                'user_id' => auth()->id(),
            ],
        ];

        // Add shipping address collection if needed
        if ($requiresShipping) {
            $checkoutOptions['shipping_address_collection'] = [
                'allowed_countries' => ['GB', 'US', 'CA', 'AU', 'NZ', 'IE'],
            ];
            $checkoutOptions['phone_number_collection'] = [
                'enabled' => true,
            ];
        }

        try {
            // Create Stripe Checkout Session via Cashier
            return auth()->user()->checkout($lineItems, $checkoutOptions);
            
        } catch (\Exception $e) {
            Log::error('Stripe checkout failed', [
                'error' => $e->getMessage(),
                'cart_id' => $cart->id,
                'user_id' => auth()->id(),
            ]);
            
            return back()->with('error', 'Unable to process checkout. Please try again.');
        }
    }

    /**
     * Handle successful checkout
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return redirect()->route('storefront.cart.index')
                ->with('error', 'Invalid checkout session');
        }

        try {
            // Retrieve the Stripe Checkout Session directly from Stripe
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $session = $stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['customer', 'payment_intent'],
            ]);
            
            // Verify payment was successful
            if ($session->payment_status !== 'paid') {
                return redirect()->route('checkout.cancel')
                    ->with('error', 'Payment was not completed');
            }

            // Get cart from metadata
            $cartId = $session->metadata->cart_id ?? null;
            $cart = Cart::find($cartId);
            
            if (!$cart) {
                Log::error('Cart not found for successful payment', [
                    'session_id' => $sessionId,
                    'cart_id' => $cartId,
                ]);
                return redirect()->route('home')
                    ->with('error', 'Cart not found. Please contact support with reference: ' . $sessionId);
            }

            // Check if order already exists (prevent duplicate)
            $existingOrder = Order::where('stripe_checkout_session_id', $session->id)->first();
            
            if ($existingOrder) {
                // Order already processed, just show it
                return view('checkout.success', ['order' => $existingOrder]);
            }

            // Create order from cart
            $order = $cart->createOrder();
            
            // Update order with Stripe details
            $order->update([
                'stripe_checkout_session_id' => $session->id,
                'stripe_payment_intent' => $session->payment_intent,
                'status' => 'paid',
                'placed_at' => now(),
            ]);

            // Create order addresses from Stripe session
            if ($session->customer_details) {
                // Billing address
                OrderAddress::create([
                    'order_id' => $order->id,
                    'type' => 'billing',
                    'first_name' => $session->customer_details->name ?? '',
                    'last_name' => '',
                    'line_one' => $session->customer_details->address->line1 ?? '',
                    'line_two' => $session->customer_details->address->line2 ?? null,
                    'city' => $session->customer_details->address->city ?? '',
                    'state' => $session->customer_details->address->state ?? null,
                    'postcode' => $session->customer_details->address->postal_code ?? '',
                    'country' => $session->customer_details->address->country ?? 'GB',
                    'contact_email' => $session->customer_details->email ?? auth()->user()->email,
                    'contact_phone' => $session->customer_details->phone ?? null,
                ]);
            }

            // Shipping address (if collected)
            if (isset($session->shipping_details) && $session->shipping_details) {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'type' => 'shipping',
                    'first_name' => $session->shipping_details->name ?? '',
                    'last_name' => '',
                    'line_one' => $session->shipping_details->address->line1 ?? '',
                    'line_two' => $session->shipping_details->address->line2 ?? null,
                    'city' => $session->shipping_details->address->city ?? '',
                    'state' => $session->shipping_details->address->state ?? null,
                    'postcode' => $session->shipping_details->address->postal_code ?? '',
                    'country' => $session->shipping_details->address->country ?? 'GB',
                    'contact_phone' => $session->customer_details->phone ?? null,
                ]);
            }

            // Deduct stock for all items
            foreach ($cart->lines as $line) {
                if ($line->purchasable && $line->purchasable->tracksInventory()) {
                    $purchasable = $line->purchasable;
                    $currentStock = $purchasable->getStockLevel();
                    
                    if ($currentStock !== null) {
                        $newStock = max(0, $currentStock - $line->quantity);
                        
                        // Update stock (assuming stock_quantity property)
                        if (property_exists($purchasable, 'stock_quantity')) {
                            $purchasable->update(['stock_quantity' => $newStock]);
                        } elseif (property_exists($purchasable, 'stock')) {
                            $purchasable->update(['stock' => $newStock]);
                        }
                    }
                }
            }

            // Record discount and gift voucher usage
            Cart::recordDiscountUsageForOrder($order);

            // Clear the cart
            $cart->delete();

            // Show success page
            return view('checkout.success', [
                'order' => $order->load(['lines.purchasable', 'addresses']),
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout success handler failed', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('home')
                ->with('error', 'An error occurred processing your order. Please contact support.');
        }
    }

    /**
     * Handle cancelled checkout
     */
    public function cancel(Request $request)
    {
        return redirect()->route('storefront.cart.index')
            ->with('info', 'Checkout was cancelled. Your cart is still available.');
    }
}
