<?php

namespace ElevateCommerce\Purchasable\Http\Controllers;

use ElevateCommerce\Core\Support\Helpers\CurrencyHelper;
use ElevateCommerce\Purchasable\Models\Cart;
use ElevateCommerce\Purchasable\Models\Order;
use ElevateCommerce\Purchasable\Models\OrderItem;
use ElevateCommerce\Purchasable\Models\OrderTimeline;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page
     */
    public function index(Request $request)
    {
        $cart = $this->getCart($request);

        // Check if cart is empty
        if ($cart->items()->count() === 0) {
            return redirect()->route('purchasable.cart.index')
                ->with('error', 'Your cart is empty. Add some items before checking out.');
        }

        // Load cart items with purchasable
        $items = $cart->items()->with('purchasable')->get();

        // Get available payment methods from config
        $paymentMethods = $this->getAvailablePaymentMethods();

        return view('purchasable::checkout.index', [
            'cart' => $cart,
            'items' => $items,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Process checkout with selected payment method
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:stripe,paypal',
            'billing_email' => 'required|email',
            'billing_name' => 'required|string|max:255',
        ]);

        $cart = $this->getCart($request);

        // Check if cart is empty
        if ($cart->items()->count() === 0) {
            return redirect()->route('purchasable.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Create order with 'pending' status
        $order = $this->createOrderFromCart($cart, $validated);

        // Store checkout data in session
        session([
            'checkout_data' => [
                'billing_email' => $validated['billing_email'],
                'billing_name' => $validated['billing_name'],
                'cart_id' => $cart->id,
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
            ]
        ]);

        // Redirect to payment gateway based on selected method
        switch ($validated['payment_method']) {
            case 'stripe':
                return redirect()->route('purchasable.purchase.stripe.checkout');
            
            case 'paypal':
                return redirect()->route('purchasable.purchase.paypall.checkout');
            
            default:
                return back()->with('error', 'Invalid payment method selected.');
        }
    }

    /**
     * Create order from cart
     */
    protected function createOrderFromCart(Cart $cart, array $billingData): Order
    {
        // Generate unique order number
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        $defaultCurrency = CurrencyHelper::getDefault();

        // Create order
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => auth()->check() ? auth()->id() : null,
            'guest_email' => !auth()->check() ? $billingData['billing_email'] : null,
            'status' => 'pending',
            'subtotal' => $cart->subtotal,
            'tax' => $cart->tax,
            'shipping' => $cart->shipping,
            'discount' => $cart->discount,
            'total' => $cart->total,
            'currency_code' => $defaultCurrency->code,
            'payment_method' => $billingData['payment_method'] ?? null,
            'payment_status' => 'pending',
            'metadata' => [
                'billing_name' => $billingData['billing_name'],
                'billing_email' => $billingData['billing_email'],
            ],
        ]);

        // Create order items from cart items
        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'purchasable_type' => $cartItem->purchasable_type,
                'purchasable_id' => $cartItem->purchasable_id,
                'name' => $cartItem->purchasable->getPurchasableName(),
                'sku' => $cartItem->purchasable->getPurchasableSku(),
                'price' => $cartItem->price,
                'quantity' => $cartItem->quantity,
                'line_total' => $cartItem->line_total,
            ]);
        }

        // Create initial timeline entry
        OrderTimeline::create([
            'order_id' => $order->id,
            'event' => 'order_created',
            'description' => 'Order was created and is awaiting payment.',
            'user_type' => 'system',
            'metadata' => [
                'status' => 'pending',
                'payment_method' => $billingData['payment_method'] ?? null,
            ],
        ]);

        return $order;
    }

    /**
     * Get available payment methods
     */
    protected function getAvailablePaymentMethods(): array
    {
        return [
            [
                'id' => 'stripe',
                'name' => 'Credit/Debit Card',
                'description' => 'Pay securely with your credit or debit card via Stripe',
                'icon' => 'fas fa-credit-card',
                'enabled' => true,
            ],
            [
                'id' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Pay securely with your PayPal account',
                'icon' => 'fab fa-paypal',
                'enabled' => true,
            ],
        ];
    }

    /**
     * Get or create cart for current user/session
     */
    protected function getCart(Request $request): Cart
    {
        if (auth()->check()) {
            return Cart::forUser(auth()->user());
        }

        return Cart::forSession($request->session()->getId());
    }
}
