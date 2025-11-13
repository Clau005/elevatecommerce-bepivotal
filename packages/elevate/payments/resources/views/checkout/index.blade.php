<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Payment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>

                    <form id="payment-form" action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $total }}">
                        <input type="hidden" name="currency" value="GBP">
                        <input type="hidden" name="payment_token" id="payment-token">

                        <!-- Gateway Selection -->
                        <div class="space-y-3 mb-6">
                            @foreach($gateways as $gateway)
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-blue-500 transition-colors {{ $loop->first ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}" 
                                   data-gateway="{{ $gateway->driver }}">
                                <input type="radio" name="gateway_id" value="{{ $gateway->id }}" 
                                       class="w-4 h-4 text-blue-600" 
                                       {{ $loop->first ? 'checked' : '' }}
                                       onchange="switchGateway('{{ $gateway->driver }}', {{ json_encode($gateway->credentials) }})">
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900">{{ $gateway->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ implode(', ', array_map('ucfirst', $gateway->getAvailablePaymentMethods())) }}
                                    </div>
                                </div>
                                @if($gateway->driver === 'stripe')
                                    <svg class="w-12 h-8" viewBox="0 0 60 25" fill="#635BFF"><path d="M32.744 11.438c-5.43-2.015-8.39-3.565-8.39-6.023 0-2.077 1.708-3.263 4.753-3.263 5.57 0 11.288 2.145 16.725 4.078l2.225-13.738C43.13 2.438 38.174 0 30.413 0c-6.248 0-11.81 1.635-15.76 4.68C10.4 7.868 8.393 12.48 8.393 18.045c0 10.098 6.168 14.4 16.19 18.048 6.463 2.3 8.613 3.935 8.613 6.458 0 2.45-2.1 3.863-5.885 3.863-4.688 0-12.413-2.303-17.475-5.273l-2.25 13.888C11.294 57.475 20.963 60 29.285 60c6.603 0 12.108-1.56 15.82-4.533 4.16-3.263 6.313-8.09 6.313-14.33 0-10.32-6.31-14.628-16.478-18.263z"/></svg>
                                @elseif($gateway->driver === 'paypal')
                                    <svg class="w-12 h-8" viewBox="0 0 101 32" fill="#00457C"><path d="M12.237 2.8H4.437A1.2 1.2 0 003.2 3.9L.1 26.4c-.1.5.3 1 .8 1h5.5c.5 0 1-.4 1.1-.9l1.1-7c.1-.5.5-.9 1.1-.9h2.5c5.1 0 8.1-2.5 8.9-7.4.3-2.1 0-3.8-1-5-1.1-1.3-3.1-2-5.8-2zm.9 7.3c-.5 2.8-2.9 2.8-5.2 2.8h-1.3l.9-5.7c.1-.3.3-.5.6-.5h.5c1.3 0 2.5 0 3.1.7.4.4.5 1 .4 1.7zM35.1 10h-5.5c-.3 0-.6.2-.6.5l-.2 1-.3-.4c-.9-1.3-2.9-1.7-4.9-1.7-4.6 0-8.5 3.5-9.3 8.3-.4 2.4.2 4.7 1.5 6.3 1.2 1.5 3 2.1 5 2.1 3.5 0 5.5-2.3 5.5-2.3l-.2 1c-.1.5.3 1 .8 1h5c.5 0 1-.4 1.1-.9l2-12.7c.1-.5-.3-1-.8-1zm-7.9 7.3c-.4 2.3-2.1 3.9-4.4 3.9-1.1 0-2-.4-2.6-1.1-.6-.7-.8-1.7-.6-2.8.4-2.3 2.2-4 4.4-4 1.1 0 2 .4 2.6 1.1.6.7.8 1.7.6 2.9z"/></svg>
                                @endif
                            </label>
                            @endforeach
                        </div>

                        <input type="hidden" name="payment_method" id="payment-method" value="card">

                        <!-- Stripe Payment Element -->
                        <div id="stripe-payment-element" class="mb-6"></div>

                        <!-- PayPal Container -->
                        <div id="paypal-button-container" class="hidden mb-6"></div>

                        <button type="submit" id="submit-button" 
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            Pay £{{ number_format($total, 2) }}
                        </button>

                        <div id="payment-message" class="hidden mt-4 text-red-600 text-sm"></div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-4">
                        @foreach($cart as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $item['name'] ?? 'Item' }}</span>
                            <span class="font-medium">£{{ number_format($item['total'] ?? 0, 2) }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total</span>
                            <span>£{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stripe = null;
        let elements = null;
        let paymentElement = null;

        // Initialize with first gateway
        const firstGateway = @json($gateways->first());
        if (firstGateway && firstGateway.driver === 'stripe') {
            initializeStripe(firstGateway.credentials);
        }

        function switchGateway(driver, credentials) {
            // Hide all payment containers
            document.getElementById('stripe-payment-element').classList.add('hidden');
            document.getElementById('paypal-button-container').classList.add('hidden');

            if (driver === 'stripe') {
                document.getElementById('stripe-payment-element').classList.remove('hidden');
                if (!stripe) {
                    initializeStripe(credentials);
                }
            } else if (driver === 'paypal') {
                document.getElementById('paypal-button-container').classList.remove('hidden');
                initializePayPal(credentials);
            }
        }

        function initializeStripe(credentials) {
            stripe = Stripe(credentials.publishable_key);
            
            const options = {
                mode: 'payment',
                amount: {{ $total * 100 }},
                currency: 'gbp',
                appearance: {
                    theme: 'stripe',
                },
            };

            elements = stripe.elements(options);
            paymentElement = elements.create('payment');
            paymentElement.mount('#stripe-payment-element');
        }

        function initializePayPal(credentials) {
            // PayPal SDK integration would go here
            console.log('PayPal initialization', credentials);
        }

        // Handle form submission
        document.getElementById('payment-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitButton = document.getElementById('submit-button');
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            const selectedGateway = document.querySelector('input[name="gateway_id"]:checked');
            const gatewayDriver = selectedGateway.closest('label').dataset.gateway;

            if (gatewayDriver === 'stripe') {
                const {error, paymentIntent} = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: '{{ route("checkout.complete") }}',
                    },
                    redirect: 'if_required'
                });

                if (error) {
                    document.getElementById('payment-message').textContent = error.message;
                    document.getElementById('payment-message').classList.remove('hidden');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Pay £{{ number_format($total, 2) }}';
                } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                    window.location.href = '{{ route("checkout.complete") }}';
                }
            } else {
                // Submit form for other gateways
                e.target.submit();
            }
        });
    </script>
</body>
</html>
