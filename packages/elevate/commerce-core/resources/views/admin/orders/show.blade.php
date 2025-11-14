<x-app pageTitle="Order #{{ $order->reference }}" title="Order Details - Admin" description="View order information, timeline, and manage order status">

    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-4">
                        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->reference }}</h1>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                            @switch($order->status)
                                @case('created') bg-gray-100 text-gray-800 @break
                                @case('confirmed') bg-blue-100 text-blue-800 @break
                                @case('processing') bg-purple-100 text-purple-800 @break
                                @case('shipped') bg-indigo-100 text-indigo-800 @break
                                @case('delivered') bg-green-100 text-green-800 @break
                                @case('cancelled') bg-red-100 text-red-800 @break
                                @case('refunded') bg-orange-100 text-orange-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch
                        ">
                            {{ ucfirst($order->status) }}
                        </span>
                        @if($order->new_customer)
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                New Customer
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-600 mt-1">
                        Placed {{ $order->created_at->format('M j, Y \a\t g:ia') }}
                        @if($order->customer_reference)
                            • Customer Ref: {{ $order->customer_reference }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-3">
                    @if($order->user)
                        <a href="{{ route('admin.customers.show', $order->user) }}" 
                           class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50">
                            View Customer
                        </a>
                    @endif
                    <a href="{{ route('admin.orders.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Items</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['items_count'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                            <dd class="text-lg font-medium text-gray-900">@currency($order->total)</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Channel</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $order->channel->name ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Timeline</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $order->timelines->count() }} events</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Order Items --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->lines as $line)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($line->preview)
                                            <div class="flex-shrink-0 h-16 w-16">
                                                <img class="h-16 w-16 rounded-lg object-cover shadow-sm border border-gray-200" 
                                                     src="{{ asset($line->preview) }}" 
                                                     alt="{{ $line->description }}"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="h-16 w-16 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200" style="display: none;">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        @else
                                            <div class="h-16 w-16 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $line->description }}</div>
                                        @if($line->option)
                                            <div class="text-sm text-gray-500">{{ $line->option }}</div>
                                        @endif
                                        @if($line->purchasable)
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ class_basename($line->purchasable_type) }} #{{ $line->purchasable_id }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $line->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @currency($line->unit_price)
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        @currency($line->total)
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Order Totals --}}
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <dl class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Subtotal:</dt>
                                <dd class="text-gray-900">£{{ number_format($stats['subtotal'], 2) }}</dd>
                            </div>
                            @if($stats['discount_amount'] > 0)
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Discounts:</dt>
                                <dd class="text-green-600">-£{{ number_format($stats['discount_amount'], 2) }}</dd>
                            </div>
                            @endif
                            @if($stats['gift_voucher_amount'] > 0)
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Gift Vouchers:</dt>
                                <dd class="text-purple-600">-£{{ number_format($stats['gift_voucher_amount'], 2) }}</dd>
                            </div>
                            @endif
                            @if($stats['shipping_amount'] > 0)
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Shipping:</dt>
                                <dd class="text-gray-900">£{{ number_format($stats['shipping_amount'], 2) }}</dd>
                            </div>
                            @endif
                            @if($stats['tax_amount'] > 0)
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Tax:</dt>
                                <dd class="text-gray-900">£{{ number_format($stats['tax_amount'], 2) }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between text-base font-medium pt-2 border-t border-gray-200">
                                <dt class="text-gray-900">Total:</dt>
                                <dd class="text-gray-900">£{{ number_format($stats['total'], 2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Discount & Gift Voucher Breakdown --}}
                    @if(($order->discount_breakdown && count($order->discount_breakdown) > 0) || ($order->gift_voucher_breakdown && count($order->gift_voucher_breakdown) > 0))
                    <div class="px-6 py-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Applied Discounts & Vouchers</h4>
                        
                        @if($order->discount_breakdown && count($order->discount_breakdown) > 0)
                        <div class="mb-4">
                            <h5 class="text-xs font-medium text-gray-700 uppercase tracking-wider mb-2">Discounts</h5>
                            <div class="space-y-1">
                                @foreach($order->discount_breakdown as $discount)
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                            {{ $discount['code'] }}
                                        </span>
                                        <span class="text-gray-600">{{ $discount['description'] ?? 'Discount Applied' }}</span>
                                    </div>
                                    <span class="font-medium text-green-600">-@currency($discount['amount'])</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($order->gift_voucher_breakdown && count($order->gift_voucher_breakdown) > 0)
                        <div>
                            <h5 class="text-xs font-medium text-gray-700 uppercase tracking-wider mb-2">Gift Vouchers</h5>
                            <div class="space-y-1">
                                @foreach($order->gift_voucher_breakdown as $voucher)
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-2">
                                            {{ $voucher['code'] }}
                                        </span>
                                        <span class="text-gray-600">{{ $voucher['description'] ?? 'Gift Voucher Applied' }}</span>
                                        @if(isset($voucher['voucher_value']))
                                        <span class="text-xs text-gray-500 ml-1">(Value: @currency($voucher['amount']))</span>
                                        @endif
                                    </div>
                                    <span class="font-medium text-purple-600">-@currency($voucher['amount'])</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Timeline --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Order Timeline</h3>
                            <button onclick="document.getElementById('add-comment-modal').classList.remove('hidden')" 
                                    class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-blue-600 text-sm font-medium rounded-md hover:bg-blue-50">
                                Add Comment
                            </button>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($order->timelines as $timeline)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                @if($timeline->is_system_event)
                                                    @switch($timeline->type)
                                                        @case('order_created')
                                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </span>
                                                            @break
                                                        @case('status_change')
                                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                </svg>
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </span>
                                                    @endswitch
                                                @else
                                                    <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        @if($timeline->title)
                                                            <p class="text-sm font-medium text-gray-900">{{ $timeline->title }}</p>
                                                        @endif
                                                        @if($timeline->content)
                                                            <p class="text-sm text-gray-700 mt-1">{{ $timeline->content }}</p>
                                                        @endif
                                                        <div class="flex items-center gap-2 mt-2">
                                                            <span class="text-xs text-gray-500">{{ $timeline->author_name }}</span>
                                                            <span class="text-xs text-gray-400">•</span>
                                                            <span class="text-xs text-gray-500">{{ $timeline->created_at->format('M j, Y g:ia') }}</span>
                                                            @if($timeline->is_visible_to_customer)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                    Visible to customer
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Customer Information --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Customer</h3>
                    </div>
                    <div class="px-6 py-4">
                        @if($order->user)
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $order->user->full_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                                </div>
                                @if($order->user->company_name)
                                    <div>
                                        <p class="text-xs text-gray-500">Company</p>
                                        <p class="text-sm text-gray-900">{{ $order->user->company_name }}</p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-xs text-gray-500">Account Reference</p>
                                    <p class="text-sm text-gray-900 font-mono">{{ $order->user->account_reference }}</p>
                                </div>
                                <div class="pt-3 border-t border-gray-200">
                                    <a href="{{ route('admin.customers.show', $order->user) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-blue-600 text-sm font-medium rounded-md hover:bg-blue-50">
                                        View Customer Profile
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Guest Order</h3>
                                <p class="mt-1 text-sm text-gray-500">This order was placed by a guest customer.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Status Management --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Order Status</h3>
                    </div>
                    <div class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="created" {{ $order->status == 'created' ? 'selected' : '' }}>Created</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Customer Reference</label>
                                <input type="text" name="customer_reference" 
                                       value="{{ old('customer_reference', $order->customer_reference) }}" 
                                       placeholder="Optional customer reference"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                                <textarea name="notes" rows="3" 
                                          placeholder="Internal notes about this order"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $order->notes) }}</textarea>
                            </div>

                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                Update Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Comment Modal --}}
    <div id="add-comment-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add Comment to Order</h3>
                <form method="POST" action="{{ route('admin.orders.timeline.store', $order) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                        <textarea name="content" rows="4" required
                                  placeholder="Enter your comment..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_customer_visible" value="1" checked
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label class="ml-2 text-sm text-gray-700">Visible to customer</label>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('add-comment-modal').classList.add('hidden')"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Add Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

</x-app>
