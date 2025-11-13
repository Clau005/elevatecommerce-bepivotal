@extends('themes.be-pivotal.layouts.default')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <!-- Success Icon -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
        <p class="text-gray-600 mb-6">Thank you for your purchase</p>

        <!-- Order Details -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="text-left">
                    <p class="text-gray-500">Order Number</p>
                    <p class="font-semibold text-gray-900">{{ $order->reference }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-500">Order Date</p>
                    <p class="font-semibold text-gray-900">{{ $order->placed_at->format('M d, Y') }}</p>
                </div>
                <div class="text-left">
                    <p class="text-gray-500">Total Amount</p>
                    <p class="font-semibold text-gray-900">£{{ number_format($order->total / 100, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-500">Payment Status</p>
                    <p class="font-semibold text-green-600">Paid</p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="border-t border-gray-200 pt-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 text-left">Order Items</h2>
            <div class="space-y-3">
                @foreach($order->lines as $line)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center space-x-3">
                            @if($line->preview)
                                <img src="{{ $line->preview }}" alt="{{ $line->description }}" 
                                    class="w-12 h-12 object-cover rounded">
                            @endif
                            <div class="text-left">
                                <p class="font-medium text-gray-900">{{ $line->description }}</p>
                                <p class="text-gray-500">Qty: {{ $line->quantity }}</p>
                            </div>
                        </div>
                        <span class="font-medium text-gray-900">£{{ number_format(($line->unit_price * $line->quantity) / 100, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Shipping Info (if applicable) -->
        @if($order->shippingCarrier)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                    </svg>
                    <div class="text-left">
                        <p class="text-sm font-medium text-blue-900">Shipping via {{ $order->shippingCarrier->name }}</p>
                        @if(isset($order->meta['tracking_number']))
                            <p class="text-sm text-blue-700 mt-1">
                                Tracking: <span class="font-mono">{{ $order->meta['tracking_number'] }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('storefront.account.orders.show', $order->id) }}" 
                class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                View Order Details
            </a>
            <a href="/" 
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Continue Shopping
            </a>
        </div>

        <!-- Email Confirmation -->
        <p class="text-sm text-gray-500 mt-6">
            A confirmation email has been sent to your email address
        </p>
    </div>
</div>
@endsection
