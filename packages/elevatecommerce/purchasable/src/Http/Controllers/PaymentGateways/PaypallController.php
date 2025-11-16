<?php

namespace ElevateCommerce\Purchasable\Http\Controllers\PaymentGateways;

use ElevateCommerce\Purchasable\Models\Cart;
use ElevateCommerce\Purchasable\Models\Order;
use ElevateCommerce\Purchasable\Models\OrderItem;
use ElevateCommerce\Purchasable\Models\OrderAddress;
use ElevateCommerce\Purchasable\Models\OrderTimeline;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\Core\Support\Helpers\CurrencyHelper;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypallController extends Controller
{
   public function paypalCheckout(Request $request)
   {
        // Get checkout data from session
        $checkoutData = session('checkout_data');
        if (!$checkoutData || !isset($checkoutData['cart_id'])) {
            return redirect()->route('purchasable.cart.index')
                ->with('error', 'Checkout session expired. Please try again.');
        }

        // Load cart with items
        $cart = Cart::with('items.purchasable')->find($checkoutData['cart_id']);
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('purchasable.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Get default currency
        $defaultCurrency = CurrencyHelper::getDefault();

        // Initialize PayPal provider
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        
        // Set currency to match our default
        $provider->setCurrency($defaultCurrency->code);
        
        // Get access token
        $accessToken = $provider->getAccessToken();
        
        if (!$accessToken) {
            return redirect()->route('purchasable.checkout.index')
                ->with('error', 'Unable to connect to PayPal. Please try again or use a different payment method.');
        }

        // Build line items for PayPal
        $items = [];
        foreach ($cart->items as $item) {
            $items[] = [
                'name' => $item->purchasable->getPurchasableName(),
                'description' => Str::limit($item->purchasable->description ?? '', 100),
                'sku' => $item->purchasable->getPurchasableSku(),
                'unit_amount' => [
                    'currency_code' => $defaultCurrency->code,
                    'value' => number_format($item->price / 100, 2, '.', ''),
                ],
                'quantity' => (string) $item->quantity,
            ];
        }

        // Calculate amounts
        $subtotal = number_format($cart->subtotal / 100, 2, '.', '');
        $tax = number_format($cart->tax / 100, 2, '.', '');
        $shipping = number_format($cart->shipping / 100, 2, '.', '');
        $discount = number_format($cart->discount / 100, 2, '.', '');
        $total = number_format($cart->total / 100, 2, '.', '');

        // Create PayPal order data (matching docs format)
        $data = [
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('purchasable.purchase.paypall.return'),
                "cancel_url" => route('purchasable.checkout.index'),
                "brand_name" => config('app.name'),
                "user_action" => "PAY_NOW",
            ],
            "purchase_units" => [
                [
                    "reference_id" => (string) ($checkoutData['order_id'] ?? 'ORDER'),
                    "description" => "Order from " . config('app.name'),
                    "amount" => [
                        "currency_code" => $defaultCurrency->code,
                        "value" => $total,
                        "breakdown" => [
                            "item_total" => [
                                "currency_code" => $defaultCurrency->code,
                                "value" => $subtotal,
                            ],
                            "tax_total" => [
                                "currency_code" => $defaultCurrency->code,
                                "value" => $tax,
                            ],
                            "shipping" => [
                                "currency_code" => $defaultCurrency->code,
                                "value" => $shipping,
                            ],
                            "discount" => [
                                "currency_code" => $defaultCurrency->code,
                                "value" => $discount,
                            ],
                        ],
                    ],
                    "items" => $items,
                ]
            ]
        ];

        try {
            $response = $provider->createOrder($data);
            
            // Check if order was created successfully
            if (isset($response['id']) && $response['id'] != null) {
                // Find the approval URL and redirect user to PayPal
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        // Store PayPal order ID in session for later verification
                        session(['paypal_order_id' => $response['id']]);
                        
                        // Redirect to PayPal for payment
                        return redirect()->away($link['href']);
                    }
                }
                
                // If no approval link found
                return redirect()->route('purchasable.checkout.index')
                    ->with('error', 'Unable to process PayPal payment. Please try again.');
            } else {
                // Order creation failed
                return redirect()->route('purchasable.checkout.index')
                    ->with('error', 'PayPal order creation failed. Please try again or use a different payment method.');
            }
        } catch (\Exception $e) {
            return redirect()->route('purchasable.checkout.index')
                ->with('error', 'PayPal Error: ' . $e->getMessage());
        }
    }

    public function paypallReturn(Request $request)
    {

        // Get PayPal order ID from session
        $paypalOrderId = session('paypal_order_id') ?? $request->get('token');
        
        if (!$paypalOrderId) {
            return redirect()->route('home')
                ->with('error', 'Payment session expired.');
        }

        try {
            // Initialize PayPal provider
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // Capture the payment
            $result = $provider->capturePaymentOrder($paypalOrderId);

            // Check if payment was successful
            if (isset($result['status']) && $result['status'] === 'COMPLETED') {
                // Get checkout data from session
                $checkoutData = session('checkout_data');
                $order = null;
                $cart = null;

                // Update order status if order exists
                if ($checkoutData && isset($checkoutData['order_id'])) {
                    $order = Order::with('items.purchasable')->find($checkoutData['order_id']);
                    
                    if ($order) {
                        // Get payment details from PayPal response
                        $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? $paypalOrderId;
                        $amount = $result['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? null;
                        $currency = $result['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'] ?? null;

                        // Update order with payment details
                        $order->update([
                            'status' => 'processing',
                            'payment_status' => 'paid',
                            'payment_transaction_id' => $captureId,
                            'paid_at' => now(),
                        ]);

                        // Add timeline entry for payment
                        OrderTimeline::create([
                            'order_id' => $order->id,
                            'event' => 'payment_received',
                            'description' => 'Payment was successfully processed via PayPal.',
                            'user_type' => 'system',
                            'metadata' => [
                                'payment_method' => 'paypal',
                                'transaction_id' => $captureId,
                                'paypal_order_id' => $paypalOrderId,
                                'amount' => $amount,
                                'currency' => $currency,
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

                        // Clear session data
                        session()->forget(['checkout_data', 'paypal_order_id']);
                    }
                }

                // Get default currency for display
                $defaultCurrency = CurrencyHelper::getDefault();

                return view('purchasable::payment.success', [
                    'session' => (object) [
                        'payment_status' => 'paid',
                        'amount_total' => ($amount ?? 0) * 100, // Convert to cents
                        'currency' => strtolower($currency ?? $defaultCurrency->code),
                    ],
                    'metadata' => (object) [],
                    'amount_total' => ($amount ?? 0) * 100,
                    'currency' => strtoupper($currency ?? $defaultCurrency->code),
                    'order' => $order,
                    'cart' => $cart,
                    'lineItems' => [],
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
                            'description' => 'PayPal payment attempt failed or was cancelled.',
                            'user_type' => 'system',
                            'metadata' => [
                                'paypal_order_id' => $paypalOrderId,
                                'status' => $result['status'] ?? 'unknown',
                            ],
                        ]);
                    }
                }

                return redirect()->route('home')
                    ->with('error', 'Payment was not completed. Status: ' . ($result['status'] ?? 'unknown'));
            }

        } catch (\Exception $e) {
            return redirect()->route('home')
                ->with('error', 'Error processing PayPal payment: ' . $e->getMessage());
        }
    }
}
