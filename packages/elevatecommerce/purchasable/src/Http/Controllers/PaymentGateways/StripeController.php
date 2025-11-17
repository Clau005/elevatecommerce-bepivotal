<?php

namespace ElevateCommerce\Purchasable\Http\Controllers\PaymentGateways;

use ElevateCommerce\Purchasable\Events\OrderUpdated;
use ElevateCommerce\Purchasable\Models\Cart;
use ElevateCommerce\Purchasable\Models\Order;
use ElevateCommerce\Purchasable\Models\OrderItem;
use ElevateCommerce\Purchasable\Models\OrderAddress;
use ElevateCommerce\Purchasable\Models\OrderTimeline;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\Core\Support\Helpers\CurrencyHelper;
use Illuminate\Support\Str;

class StripeController extends Controller
{
    /**
     * Create Stripe checkout session from cart
     * This is called by CheckoutController after payment method selection
     */
    public function stripeCheckout(Request $request)
    {
        // Get checkout data from session
        $checkoutData = session('checkout_data');
        
        if (!$checkoutData) {
            return redirect()->route('purchasable.checkout.index')
                ->with('error', 'Checkout session expired. Please try again.');
        }

        // Get cart
        $cart = Cart::with('items.purchasable')->find($checkoutData['cart_id']);
        
        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('purchasable.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Initialize Stripe
        $stripe = new \Stripe\StripeClient(config('stripe.stripe_sk'));

        // Get default currency
        $defaultCurrency = CurrencyHelper::getDefaultCurrencyCode();

        try {
            // Build line items from cart
            $lineItems = [];
            foreach ($cart->items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => strtolower($defaultCurrency),
                        'product_data' => [
                            'name' => $item->purchasable->getPurchasableName(),
                            'description' => $item->purchasable->description ?? '',
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            // Create Stripe Checkout Session
            $session = $stripe->checkout->sessions->create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('purchasable.purchase.stripe.return') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('purchasable.checkout.index') . '?canceled=1',
                'customer_email' => $checkoutData['billing_email'],
                'metadata' => [
                    'cart_id' => $cart->id,
                    'billing_name' => $checkoutData['billing_name'],
                    'billing_email' => $checkoutData['billing_email'],
                ],
            ]);

            // Redirect to Stripe Checkout
            return redirect($session->url);

        } catch (\Exception $e) {
            return redirect()->route('purchasable.checkout.index')
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function stripeReturn(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('home')->with('error', 'Invalid payment session');
        }

        try {
            // Initialize Stripe
            $stripe = new \Stripe\StripeClient(config('stripe.stripe_sk'));

            // Retrieve the session with line items
            $session = $stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['line_items']
            ]);

            // Check payment status
            if ($session->payment_status === 'paid') {
                // Payment successful
                $metadata = $session->metadata;
                
                // Get checkout data from session
                $checkoutData = session('checkout_data');
                $order = null;
                $cart = null;
                
                // Update order status if order exists
                if ($checkoutData && isset($checkoutData['order_id'])) {
                    $order = Order::with('items.purchasable')->find($checkoutData['order_id']);
                    
                    if ($order) {
                        // Update order with payment details
                        $order->update([
                            'status' => 'processing',
                            'payment_status' => 'paid',
                            'payment_transaction_id' => $session->payment_intent ?? $session->id,
                            'paid_at' => now(),
                        ]);

                        // Dispatch OrderUpdated event
                        event(new OrderUpdated($order, 'pending', 'processing'));

                        // Add timeline entry for payment
                        OrderTimeline::create([
                            'order_id' => $order->id,
                            'event' => 'payment_received',
                            'description' => 'Payment was successfully processed via Stripe.',
                            'user_type' => 'system',
                            'metadata' => [
                                'payment_method' => 'stripe',
                                'transaction_id' => $session->payment_intent ?? $session->id,
                                'amount' => $session->amount_total,
                                'currency' => $session->currency,
                            ],
                        ]);

                        // Add timeline entry for status change
                        OrderTimeline::create([
                            'order_id' => $order->id,
                            'event' => 'status_changed',
                            'description' => 'Order status changed from pending to processing.',
                            'user_type' => 'system',
                            'metadata' => [
                                'old_status' => 'pending',
                                'new_status' => 'processing',
                            ],
                        ]);

                        // Get cart for display
                        if (isset($checkoutData['cart_id'])) {
                            $cart = Cart::with('items.purchasable')->find($checkoutData['cart_id']);
                            
                            // Clear the cart after successful payment
                            if ($cart) {
                                $cart->clear();
                            }
                        }

                        // Clear checkout session data
                        session()->forget('checkout_data');
                    }
                }

                return view('purchasable::payment.success', [
                    'session' => $session,
                    'metadata' => $metadata,
                    'amount_total' => $session->amount_total,
                    'currency' => strtoupper($session->currency),
                    'order' => $order,
                    'cart' => $cart,
                    'lineItems' => $session->line_items->data ?? [],
                ]);
            } else {
                // Payment not completed - update order status to failed
                $checkoutData = session('checkout_data');
                if ($checkoutData && isset($checkoutData['order_id'])) {
                    $order = Order::find($checkoutData['order_id']);
                    if ($order) {
                        $order->update([
                            'payment_status' => 'failed',
                        ]);

                        OrderTimeline::create([
                            'order_id' => $order->id,
                            'event' => 'payment_failed',
                            'description' => 'Payment attempt failed or was cancelled.',
                            'user_type' => 'system',
                            'metadata' => [
                                'payment_status' => $session->payment_status,
                            ],
                        ]);
                    }
                }

                return redirect()->route('home')->with('error', 'Payment was not completed');
            }

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }
}
