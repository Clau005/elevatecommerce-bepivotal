<x-customer-layout title="Checkout" description="Complete your purchase">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    @if($cart->lines->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">Your cart is empty. <a href="{{ route('storefront.cart.index') }}" class="underline">Go to cart</a></p>
        </div>
    @else
        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Forms -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Billing Address -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold mb-4">Billing Address</h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" name="billing_address[first_name]" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="billing_address[last_name]" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                                <input type="text" name="billing_address[address_line1]" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                                <input type="text" name="billing_address[address_line2]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="billing_address[city]" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State/County</label>
                                <input type="text" name="billing_address[state]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" name="billing_address[postal_code]" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <select name="billing_address[country]" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="GB">United Kingdom</option>
                                    <option value="US">United States</option>
                                    <!-- Add more countries -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address (if needed) -->
                    @if($requiresShipping)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold">Shipping Address</h2>
                                <label class="flex items-center">
                                    <input type="checkbox" id="same-as-billing" class="mr-2">
                                    <span class="text-sm text-gray-600">Same as billing</span>
                                </label>
                            </div>
                            
                            <div id="shipping-address-fields" class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text" name="shipping_address[first_name]" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text" name="shipping_address[last_name]" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                                    <input type="text" name="shipping_address[address_line1]" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                                    <input type="text" name="shipping_address[address_line2]" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                    <input type="text" name="shipping_address[city]" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">State/County</label>
                                    <input type="text" name="shipping_address[state]" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                    <input type="text" name="shipping_address[postal_code]" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                    <select name="shipping_address[country]" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="GB">United Kingdom</option>
                                        <option value="US">United States</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-xl font-semibold mb-4">Shipping Method</h2>
                            
                            <p class="text-sm text-gray-600 mb-4">
                                {{ $cart->lines->filter(fn($l) => $l->purchasable->requiresShipping())->count() }} 
                                item(s) require shipping
                            </p>

                            @if($shippingCarriers->isEmpty())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <p class="text-yellow-800 text-sm">No shipping carriers available. Please contact support.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($shippingCarriers as $carrier)
                                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 cursor-pointer">
                                            <input type="radio" name="shipping_carrier_id" value="{{ $carrier->id }}" required 
                                                class="mr-3">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <strong class="text-gray-900">{{ $carrier->name }}</strong>
                                                    @if($carrier->test_mode)
                                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Test Mode</span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1">Standard shipping</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-blue-800 text-sm">No shipping required - all items are digital or services</p>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
                        
                        @if($paymentGateways->isEmpty())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-red-800 text-sm">No payment methods available. Please contact support.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($paymentGateways as $gateway)
                                    <div class="border border-gray-200 rounded-lg">
                                        <label class="flex items-center p-4 cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="payment_gateway_id" value="{{ $gateway->id }}" required 
                                                class="mr-3 payment-gateway-radio" 
                                                data-gateway-type="{{ strtolower($gateway->name) }}"
                                                data-gateway-id="{{ $gateway->id }}">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <strong class="text-gray-900">{{ $gateway->name }}</strong>
                                                    @if($gateway->test_mode)
                                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Test Mode</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                        
                                        <!-- Payment Gateway Fields -->
                                        <div class="payment-fields hidden p-4 pt-0" data-gateway-id="{{ $gateway->id }}">
                                            @if(strtolower($gateway->name) === 'stripe')
                                                <!-- Stripe Elements Container -->
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Card Information</label>
                                                        <div id="stripe-card-element-{{ $gateway->id }}" class="p-3 border border-gray-300 rounded-md bg-white"></div>
                                                        <div id="stripe-card-errors-{{ $gateway->id }}" class="text-red-600 text-sm mt-2"></div>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                                                            <input type="text" name="cardholder_name" 
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                                placeholder="John Doe">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                                            <input type="text" name="card_postal_code" 
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                                placeholder="12345">
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Other payment gateways -->
                                                <p class="text-sm text-gray-600">Payment details will be collected on the next page.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                        
                        <!-- Cart Items -->
                        <div class="space-y-4 mb-6">
                            @foreach($cart->lines as $line)
                                <div class="flex items-start space-x-3">
                                    @if($line->preview)
                                        <img src="{{ $line->preview }}" alt="{{ $line->description }}" 
                                            class="w-16 h-16 object-cover rounded">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $line->description }}</p>
                                        <p class="text-sm text-gray-500">Qty: {{ $line->quantity }}</p>
                                        @if($line->purchasable && $line->purchasable->requiresShipping())
                                            <span class="inline-block text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded mt-1">Physical Item</span>
                                        @else
                                            <span class="inline-block text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded mt-1">Digital/Service</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $line->formatted_total }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">@currency($cart->lines->sum('sub_total'))</span>
                            </div>
                            
                            @if($requiresShipping)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="text-gray-900">Calculated at next step</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between text-base font-semibold pt-2 border-t border-gray-200">
                                <span>Total</span>
                                <span>@currency($cart->lines->sum('total'))</span>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <button type="submit" 
                            class="w-full mt-6 bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                            Place Order
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            By placing your order, you agree to our terms and conditions
                        </p>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>

<script>
// Initialize Stripe Elements
let stripe = null;
let cardElements = {};

@foreach($paymentGateways as $gateway)
    @if(strtolower($gateway->name) === 'stripe')
        @php
            $credentials = $gateway->getActiveCredentials();
            $publishableKey = $credentials['publishable_key'] ?? '';
        @endphp
        @if($publishableKey)
        // Initialize Stripe for gateway {{ $gateway->id }}
        if (!stripe) {
            stripe = Stripe('{{ $publishableKey }}');
        }
        
        if (stripe) {
            const elements{{ $gateway->id }} = stripe.elements();
            const cardElement{{ $gateway->id }} = elements{{ $gateway->id }}.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Figtree", sans-serif',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#dc2626',
                    iconColor: '#dc2626'
                }
            }
        });
        
            cardElements[{{ $gateway->id }}] = cardElement{{ $gateway->id }};
            
            // Handle real-time validation errors
            cardElement{{ $gateway->id }}.on('change', function(event) {
                const displayError = document.getElementById('stripe-card-errors-{{ $gateway->id }}');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
        }
        @else
        console.error('Stripe publishable key not configured for gateway {{ $gateway->id }}. Please add it in the admin panel.');
        @endif
    @endif
@endforeach

// Show/hide payment fields when gateway is selected
document.querySelectorAll('.payment-gateway-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        // Hide all payment fields
        document.querySelectorAll('.payment-fields').forEach(field => {
            field.classList.add('hidden');
        });
        
        // Show selected gateway's fields
        const gatewayId = this.dataset.gatewayId;
        const fieldsContainer = document.querySelector(`.payment-fields[data-gateway-id="${gatewayId}"]`);
        if (fieldsContainer) {
            fieldsContainer.classList.remove('hidden');
            
            // Mount Stripe Element if it's a Stripe gateway and not already mounted
            if (this.dataset.gatewayType === 'stripe' && cardElements[gatewayId]) {
                const cardElementContainer = document.getElementById(`stripe-card-element-${gatewayId}`);
                if (cardElementContainer && !cardElementContainer.hasChildNodes()) {
                    cardElements[gatewayId].mount(`#stripe-card-element-${gatewayId}`);
                }
            }
        }
    });
    
    // Trigger change on the first checked radio
    if (radio.checked) {
        radio.dispatchEvent(new Event('change'));
    }
});

// Auto-select first gateway if none selected
if (!document.querySelector('.payment-gateway-radio:checked')) {
    const firstRadio = document.querySelector('.payment-gateway-radio');
    if (firstRadio) {
        firstRadio.checked = true;
        firstRadio.dispatchEvent(new Event('change'));
    }
}

// Same as billing checkbox functionality
document.getElementById('same-as-billing')?.addEventListener('change', function(e) {
    const shippingFields = document.getElementById('shipping-address-fields');
    const billingInputs = document.querySelectorAll('[name^="billing_address"]');
    const shippingInputs = document.querySelectorAll('[name^="shipping_address"]');
    
    if (e.target.checked) {
        billingInputs.forEach((input, index) => {
            const fieldName = input.name.replace('billing_address', 'shipping_address');
            const shippingInput = document.querySelector(`[name="${fieldName}"]`);
            if (shippingInput) {
                shippingInput.value = input.value;
                shippingInput.disabled = true;
                shippingInput.classList.add('bg-gray-100');
            }
        });
    } else {
        shippingInputs.forEach(input => {
            input.disabled = false;
            input.classList.remove('bg-gray-100');
        });
    }
});

// Copy billing to shipping when billing changes (if checkbox is checked)
document.querySelectorAll('[name^="billing_address"]').forEach(input => {
    input.addEventListener('input', function() {
        if (document.getElementById('same-as-billing')?.checked) {
            const fieldName = this.name.replace('billing_address', 'shipping_address');
            const shippingInput = document.querySelector(`[name="${fieldName}"]`);
            if (shippingInput) {
                shippingInput.value = this.value;
            }
        }
    });
});
</script>
@endpush

</x-customer-layout>
