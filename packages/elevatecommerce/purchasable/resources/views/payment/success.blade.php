<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full">
            <!-- Success Card -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Success Header -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4">
                        <i class="fas fa-check text-green-500 text-4xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Payment Successful!</h1>
                    <p class="text-green-100">Thank you for your purchase</p>
                </div>

                <!-- Payment Details -->
                <div class="p-8">
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Details</h2>
                        <div class="bg-gray-50 rounded-lg p-6 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Transaction ID</span>
                                <span class="font-mono text-sm text-gray-900">{{ $session->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount Paid</span>
                                <span class="font-semibold text-gray-900">@currency($amount_total) {{ $currency }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Status</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ ucfirst($session->payment_status) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Method</span>
                                <span class="text-gray-900">
                                    <i class="fab fa-cc-stripe mr-1"></i>
                                    Stripe
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                        @if($order)
                        <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600">Order Number: <span class="font-mono font-semibold text-gray-900">{{ $order->order_number }}</span></p>
                        </div>
                        @endif
                        <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                            @if($order)
                                {{-- Order-based display --}}
                                @foreach($order->items as $item)
                                <div class="p-4 flex justify-between items-start">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
                                            @if($item->purchasable->image_url ?? null)
                                                <img src="{{ $item->purchasable->image_url }}" alt="{{ $item->purchasable->getPurchasableName() }}" class="w-full h-full object-cover rounded">
                                            @else
                                                <i class="fas fa-box text-gray-400"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item->purchasable->getPurchasableName() }}</p>
                                            <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                            <p class="text-sm text-gray-500">@currency($item->price) each</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">@currency($item->line_total)</p>
                                    </div>
                                </div>
                                @endforeach
                            @elseif(isset($metadata->product_name))
                                {{-- Single product checkout (legacy) --}}
                                <div class="p-4 flex justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $metadata->product_name }}</p>
                                        <p class="text-sm text-gray-500">Quantity: {{ $metadata->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">@currency($metadata->product_price * $metadata->quantity)</p>
                                    </div>
                                </div>
                            @else
                                {{-- Fallback to Stripe line items --}}
                                @foreach($lineItems as $item)
                                <div class="p-4 flex justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item->description }}</p>
                                        <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">@currency($item->amount_total)</p>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- What's Next -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            What's Next?
                        </h3>
                        <ul class="space-y-2 text-blue-800">
                            <li class="flex items-start">
                                <i class="fas fa-envelope text-blue-600 mt-1 mr-2"></i>
                                <span>You'll receive a confirmation email shortly</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-box text-blue-600 mt-1 mr-2"></i>
                                <span>Your order will be processed within 24 hours</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-truck text-blue-600 mt-1 mr-2"></i>
                                <span>Shipping updates will be sent to your email</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('home') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition duration-200">
                            <i class="fas fa-home mr-2"></i>
                            Back to Home
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition duration-200">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            View Orders
                        </a>
                    </div>

                    <!-- Debug Info (Test Mode) -->
                    <div class="mt-8 bg-gray-800 text-gray-100 rounded-lg p-4">
                        <h3 class="text-sm font-semibold mb-2 flex items-center">
                            <i class="fas fa-code mr-2"></i>
                            Debug Information (Test Mode)
                        </h3>
                        <div class="space-y-1 text-xs font-mono">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Session ID:</span>
                                <span class="text-green-400">{{ $session->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Payment Intent:</span>
                                <span class="text-green-400">{{ $session->payment_intent ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Customer:</span>
                                <span class="text-green-400">{{ $session->customer ?? 'Guest' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Mode Notice -->
            <div class="mt-6 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-lg text-yellow-800 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>This was a test payment. No real charges were made.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
