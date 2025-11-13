<?php

namespace Elevate\CommerceCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Elevate\CommerceCore\Models\Cart;
use Elevate\CommerceCore\Models\Order;
use Elevate\CommerceCore\Models\OrderLine;
use Elevate\CommerceCore\Models\OrderAddress;
use Elevate\Payments\Services\PaymentService;
use Elevate\Shipping\Services\ShippingService;
use Elevate\Payments\Models\PaymentGateway;
use Elevate\Shipping\Models\ShippingCarrier;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected ShippingService $shippingService
    ) {}

    /**
     * Display the checkout page.
     */
    public function index(Request $request)
    {
        $cart = $this->getCurrentCart();
        
        if (!$cart || $cart->lines->isEmpty()) {
            return redirect()->route('storefront.cart.index')->with('error', 'Your cart is empty');
        }

        // Check if any items require shipping
        $requiresShipping = $this->cartRequiresShipping($cart);

        // Get enabled payment gateways
        $paymentGateways = PaymentGateway::where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();

        // Get shipping rates if needed
        $shippingRates = [];
        $shippingCarriers = [];
        if ($requiresShipping) {
            $shippingCarriers = ShippingCarrier::where('is_enabled', true)
                ->orderBy('sort_order')
                ->get();

            // If shipping address is provided, calculate rates
            if ($request->has('shipping_address')) {
                $shippingRates = $this->calculateShippingRates($cart, $request->input('shipping_address'));
            }
        }

        return view('commerce::checkout.index', [
            'cart' => $cart,
            'requiresShipping' => $requiresShipping,
            'paymentGateways' => $paymentGateways,
            'shippingCarriers' => $shippingCarriers,
            'shippingRates' => $shippingRates,
        ]);
    }

    /**
     * Calculate shipping rates for the cart.
     */
    public function calculateRates(Request $request)
    {
        $cart = $this->getCurrentCart();
        
        if (!$cart || !$this->cartRequiresShipping($cart)) {
            return response()->json(['rates' => []]);
        }

        $validated = $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.address_line1' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.country' => 'required|string',
        ]);

        $rates = $this->calculateShippingRates($cart, $validated['shipping_address']);

        return response()->json(['rates' => $rates]);
    }

    /**
     * Process the checkout and create an order.
     */
    public function process(Request $request)
    {
        $cart = $this->getCurrentCart();
        
        if (!$cart || $cart->lines->isEmpty()) {
            return redirect()->route('storefront.cart.index')->with('error', 'Your cart is empty');
        }

        $validated = $request->validate([
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'shipping_carrier_id' => 'nullable|exists:shipping_carriers,id',
            'billing_address' => 'required|array',
            'shipping_address' => 'nullable|array',
            'shipping_rate_id' => 'nullable|string',
        ]);

        // Check if shipping is required
        $requiresShipping = $this->cartRequiresShipping($cart);
        if ($requiresShipping && !$validated['shipping_carrier_id']) {
            return back()->withErrors(['shipping_carrier_id' => 'Please select a shipping method']);
        }

        try {
            // Create the order
            $order = $this->createOrder($cart, $validated);

            // Process payment
            $payment = $this->processPayment($order, $validated['payment_gateway_id']);

            if ($payment->isSuccessful()) {
                // Create shipping label if needed
                if ($requiresShipping && $validated['shipping_carrier_id']) {
                    $this->createShippingLabel($order, $validated['shipping_carrier_id'], $validated['shipping_rate_id'] ?? null);
                }

                // Clear the cart
                $cart->clear();

                return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully!');
            }

            return back()->withErrors(['payment' => 'Payment failed: ' . $payment->failure_message]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Show order success page.
     */
    public function success(Order $order)
    {
        // Verify the order belongs to the current user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('commerce::checkout.success', [
            'order' => $order->load(['lines.purchasable', 'addresses', 'payments']),
        ]);
    }

    /**
     * Get the current cart for the user.
     */
    protected function getCurrentCart(): ?Cart
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())
                ->with(['lines.purchasable'])
                ->first();
        }

        // For guest users, use session
        $sessionId = session()->getId();
        return Cart::where('session_id', $sessionId)
            ->with(['lines.purchasable'])
            ->first();
    }

    /**
     * Check if the cart requires shipping.
     */
    protected function cartRequiresShipping(Cart $cart): bool
    {
        return $cart->lines->some(function ($line) {
            return $line->purchasable && $line->purchasable->requiresShipping();
        });
    }

    /**
     * Calculate shipping rates for the cart.
     */
    protected function calculateShippingRates(Cart $cart, array $shippingAddress): array
    {
        // Get only items that require shipping
        $shippableItems = $cart->lines->filter(fn($line) => 
            $line->purchasable && $line->purchasable->requiresShipping()
        );

        if ($shippableItems->isEmpty()) {
            return [];
        }

        // Build packages for shipping calculation
        $packages = $this->buildPackages($shippableItems);

        // Get warehouse/ship-from address from config
        $shipFrom = config('commerce.warehouse_address', [
            'name' => config('app.name'),
            'address_line1' => '123 Warehouse St',
            'city' => 'London',
            'state' => 'England',
            'postal_code' => 'SW1A 1AA',
            'country' => 'GB',
        ]);

        try {
            return $this->shippingService->getRates([
                'ship_to' => $shippingAddress,
                'ship_from' => $shipFrom,
                'packages' => $packages,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to calculate shipping rates: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Build packages array for shipping calculation.
     */
    protected function buildPackages($shippableItems): array
    {
        $totalWeight = $shippableItems->sum(function ($line) {
            $weight = $line->purchasable->getWeight() ?? 500; // Default 500g
            return $weight * $line->quantity;
        });

        // For now, create a single package
        // TODO: Implement smart package splitting logic
        return [[
            'weight' => [
                'value' => $totalWeight,
                'unit' => 'gram',
            ],
            'dimensions' => [
                'length' => 30,
                'width' => 20,
                'height' => 10,
                'unit' => 'centimeter',
            ],
        ]];
    }

    /**
     * Create an order from the cart.
     */
    protected function createOrder(Cart $cart, array $data): Order
    {
        // Calculate totals
        $subTotal = $cart->lines->sum('sub_total');
        $total = $subTotal; // Add shipping/tax later

        $order = Order::create([
            'user_id' => auth()->id(),
            'channel_id' => $cart->channel_id,
            'reference' => Order::generateReference(),
            'status' => 'pending',
            'sub_total' => $subTotal,
            'total' => $total,
            'currency_code' => $cart->currency_code ?? 'GBP',
            'payment_gateway_id' => $data['payment_gateway_id'],
            'shipping_carrier_id' => $data['shipping_carrier_id'] ?? null,
            'placed_at' => now(),
        ]);

        // Create order lines
        foreach ($cart->lines as $cartLine) {
            OrderLine::create([
                'order_id' => $order->id,
                'purchasable_type' => $cartLine->purchasable_type,
                'purchasable_id' => $cartLine->purchasable_id,
                'quantity' => $cartLine->quantity,
                'unit_price' => $cartLine->unit_price,
                'description' => $cartLine->description,
                'identifier' => $cartLine->identifier,
                'preview' => $cartLine->preview,
                'meta' => $cartLine->meta,
            ]);
        }

        // Create billing address
        OrderAddress::create([
            'order_id' => $order->id,
            'type' => 'billing',
            'first_name' => $data['billing_address']['first_name'] ?? '',
            'last_name' => $data['billing_address']['last_name'] ?? '',
            'address_line1' => $data['billing_address']['address_line1'],
            'address_line2' => $data['billing_address']['address_line2'] ?? null,
            'city' => $data['billing_address']['city'],
            'state' => $data['billing_address']['state'] ?? null,
            'postcode' => $data['billing_address']['postal_code'],
            'country' => $data['billing_address']['country'],
        ]);

        // Create shipping address if provided
        if (!empty($data['shipping_address'])) {
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'shipping',
                'first_name' => $data['shipping_address']['first_name'] ?? '',
                'last_name' => $data['shipping_address']['last_name'] ?? '',
                'address_line1' => $data['shipping_address']['address_line1'],
                'address_line2' => $data['shipping_address']['address_line2'] ?? null,
                'city' => $data['shipping_address']['city'],
                'state' => $data['shipping_address']['state'] ?? null,
                'postcode' => $data['shipping_address']['postal_code'],
                'country' => $data['shipping_address']['country'],
            ]);
        }

        return $order;
    }

    /**
     * Process payment for the order.
     */
    protected function processPayment(Order $order, int $gatewayId)
    {
        $gateway = PaymentGateway::findOrFail($gatewayId);

        return $this->paymentService->charge(
            gateway: $gateway,
            amount: $order->total,
            currency: $order->currency_code,
            description: "Order #{$order->reference}",
            metadata: [
                'order_id' => $order->id,
                'order_reference' => $order->reference,
            ]
        );
    }

    /**
     * Create shipping label for the order.
     */
    protected function createShippingLabel(Order $order, int $carrierId, ?string $rateId = null)
    {
        $carrier = ShippingCarrier::findOrFail($carrierId);
        $shippingAddress = $order->shippingAddress();

        if (!$shippingAddress) {
            return;
        }

        try {
            $label = $this->shippingService->createLabel(
                carrier: $carrier,
                shipment: [
                    'ship_to' => [
                        'name' => $shippingAddress->first_name . ' ' . $shippingAddress->last_name,
                        'address_line1' => $shippingAddress->address_line1,
                        'address_line2' => $shippingAddress->address_line2,
                        'city' => $shippingAddress->city,
                        'state' => $shippingAddress->state,
                        'postal_code' => $shippingAddress->postcode,
                        'country' => $shippingAddress->country,
                    ],
                    'ship_from' => config('commerce.warehouse_address'),
                    'packages' => $this->buildPackages($order->lines),
                ],
                rateId: $rateId
            );

            // Update order with tracking info
            $order->update([
                'meta' => array_merge($order->meta ?? [], [
                    'tracking_number' => $label['tracking_number'] ?? null,
                    'label_url' => $label['label_url'] ?? null,
                    'carrier_tracking_url' => $label['tracking_url'] ?? null,
                ]),
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to create shipping label: ' . $e->getMessage());
            // Don't fail the order, just log the error
        }
    }
}
