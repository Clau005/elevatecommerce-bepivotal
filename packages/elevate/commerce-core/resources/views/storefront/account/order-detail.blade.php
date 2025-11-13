<x-customer-layout title="My Account" description="Customer Orders dashboard">
<div class="min-h-screen bg-gray-50">
    <!-- Order Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->reference }}</h1>
                        <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('storefront.orders') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Order Status</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Order Date</span>
                            <span class="text-gray-900">{{ $order->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        
                        @if($order->status === 'shipped' && isset($order->meta['tracking_number']))
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tracking Number</span>
                                <span class="text-orange-600 font-medium">{{ $order->meta['tracking_number'] }}</span>
                            </div>
                        @endif
                        
                        @if($order->status === 'completed')
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Completed Date</span>
                                <span class="text-gray-900">{{ $order->updated_at->format('M d, Y g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($order->lines as $line)
                            <div class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    
                                    <div class="ml-6 flex-1">
                                        <h3 class="text-base font-medium text-gray-900">{{ $line->description }}</h3>
                                        <p class="mt-1 text-sm text-gray-600">{{ class_basename($line->purchasable_type) }}</p>
                                        
                                        @if($line->meta && count($line->meta) > 0)
                                            <div class="mt-2">
                                                @foreach($line->meta as $key => $value)
                                                    @if(in_array($key, ['sku']))
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                            {{ $key }}: {{ $value }}
                                                        </span>
                                                    @endif
                                            @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Qty: {{ $line->quantity }}</p>
                                        <p class="text-base font-medium text-gray-900">£{{ number_format($line->unit_price / 100, 2) }} each</p>
                                        <p class="text-lg font-semibold text-gray-900">£{{ number_format($line->total / 100, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Order Totals -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900">£{{ number_format($order->sub_total / 100, 2) }}</span>
                            </div>
                            
                            @if($order->discount_total > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Discount</span>
                                    <span class="text-green-600">-£{{ number_format($order->discount_total / 100, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($order->tax_total > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="text-gray-900">£{{ number_format($order->tax_total / 100, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping</span>
                                <span class="text-gray-900">Free</span>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-2">
                                <div class="flex justify-between text-base font-medium">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-gray-900">£{{ number_format($order->total / 100, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                @if($order->timeline && $order->timeline->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Order Timeline</h2>
                        
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($order->timeline->sortBy('created_at') as $event)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-900">{{ $event->content }}</p>
                                                        @if($event->author)
                                                            <p class="text-xs text-gray-500">by {{ $event->author }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        {{ $event->created_at->format('M d, Y g:i A') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Shipping Address -->
                @if($order->addresses->where('type', 'shipping')->first())
                    @php $shippingAddress = $order->addresses->where('type', 'shipping')->first(); @endphp
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-base font-medium text-gray-900 mb-3">Shipping Address</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            @if($shippingAddress->company_name)
                                <p class="font-medium">{{ $shippingAddress->company_name }}</p>
                            @endif
                            <p>{{ $shippingAddress->line_one }}</p>
                            @if($shippingAddress->line_two)
                                <p>{{ $shippingAddress->line_two }}</p>
                            @endif
                            <p>{{ $shippingAddress->city }}, {{ $shippingAddress->state }} {{ $shippingAddress->postcode }}</p>
                            <p>{{ $shippingAddress->country }}</p>
                        </div>
                    </div>
                @endif

                <!-- Billing Address -->
                @if($order->addresses->where('type', 'billing')->first())
                    @php $billingAddress = $order->addresses->where('type', 'billing')->first(); @endphp
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-base font-medium text-gray-900 mb-3">Billing Address</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            @if($billingAddress->company_name)
                                <p class="font-medium">{{ $billingAddress->company_name }}</p>
                            @endif
                            <p>{{ $billingAddress->line_one }}</p>
                            @if($billingAddress->line_two)
                                <p>{{ $billingAddress->line_two }}</p>
                            @endif
                            <p>{{ $billingAddress->city }}, {{ $billingAddress->state }} {{ $billingAddress->postcode }}</p>
                            <p>{{ $billingAddress->country }}</p>
                        </div>
                    </div>
                @endif

                <!-- Payment Information -->
                @if($order->payments && $order->payments->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-base font-medium text-gray-900 mb-3">Payment Information</h3>
                        @foreach($order->payments as $payment)
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <span>Method</span>
                                    <span class="font-medium">{{ ucfirst($payment->type) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Amount</span>
                                    <span class="font-medium">£{{ number_format($payment->amount / 100, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Status</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($payment->status === 'paid') bg-green-100 text-green-800
                                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Order Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-base font-medium text-gray-900 mb-3">Order Actions</h3>
                    <div class="space-y-3">
                        @if($order->status === 'completed')
                            <button class="w-full bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600 text-sm font-medium">
                                Reorder Items
                            </button>
                        @endif
                        
                        @if(in_array($order->status, ['pending', 'processing']))
                            <button class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm font-medium">
                                Cancel Order
                            </button>
                        @endif
                        
                        <button class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm font-medium">
                            Download Invoice
                        </button>
                        
                        <a href="{{ route('storefront.orders') }}" 
                           class="w-full bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium text-center block">
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-customer-layout>
