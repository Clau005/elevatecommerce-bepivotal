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
                                @case('awaiting-payment') bg-yellow-100 text-yellow-800 @break
                                @case('payment-received') bg-blue-100 text-blue-800 @break
                                @case('processing') bg-purple-100 text-purple-800 @break
                                @case('shipped') bg-indigo-100 text-indigo-800 @break
                                @case('delivered') bg-green-100 text-green-800 @break
                                @case('cancelled') bg-red-100 text-red-800 @break
                                @case('refunded') bg-gray-100 text-gray-800 @break
                                @default bg-gray-100 text-gray-800
                            @endswitch
                        ">
                            {{ ucfirst(str_replace('-', ' ', $order->status)) }}
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
                        <x-bladewind::button 
                            color="blue" 
                            outline="true"
                            onclick="window.location.href='{{ route('admin.customers.show', $order->user) }}'">
                            View Customer
                        </x-bladewind::button>
                    @endif
                    <x-bladewind::button 
                        color="gray" 
                        outline="true"
                        onclick="window.location.href='{{ route('admin.orders.index') }}'">
                        Back to Orders
                    </x-bladewind::button>
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
                            <dd class="text-lg font-medium text-gray-900">£{{ number_format($stats['total'], 2) }}</dd>
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
                            <dd class="text-lg font-medium text-gray-900">{{ $order->channel->name }}</dd>
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
                                        £{{ number_format($line->unit_price / 100, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        £{{ number_format($line->total / 100, 2) }}
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
                                    <span class="font-medium text-green-600">-£{{ number_format($discount['amount'] / 100, 2) }}</span>
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
                                        <span class="text-xs text-gray-500 ml-1">(Value: £{{ number_format($voucher['amount'] / 100, 2) }})</span>
                                        @endif
                                    </div>
                                    <span class="font-medium text-purple-600">-£{{ number_format($voucher['amount'] / 100, 2) }}</span>
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
                            <x-bladewind::button 
                                size="small"
                                onclick="showModal('add-comment-modal')">
                                Add Comment
                            </x-bladewind::button>
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
                                    <x-bladewind::button 
                                        size="small" 
                                        color="blue" 
                                        outline="true"
                                        onclick="window.location.href='{{ route('admin.customers.show', $order->user) }}'">
                                        View Customer Profile
                                    </x-bladewind::button>
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
                                <x-bladewind::select 
                                    name="status"
                                    label="Status"
                                    selected_value="{{ $order->status }}"
                                    :data="[
                                        ['label' => 'Awaiting Payment', 'value' => 'awaiting-payment'],
                                        ['label' => 'Payment Received', 'value' => 'payment-received'],
                                        ['label' => 'Processing', 'value' => 'processing'],
                                        ['label' => 'Shipped', 'value' => 'shipped'],
                                        ['label' => 'Delivered', 'value' => 'delivered'],
                                        ['label' => 'Cancelled', 'value' => 'cancelled'],
                                        ['label' => 'Refunded', 'value' => 'refunded']
                                    ]" />
                            </div>

                            <div>
                                <x-bladewind::input 
                                    name="customer_reference"
                                    label="Customer Reference"
                                    placeholder="Optional customer reference"
                                    value="{{ old('customer_reference', $order->customer_reference) }}" />
                            </div>

                            <div>
                                <x-bladewind::textarea 
                                    name="notes"
                                    label="Order Notes"
                                    placeholder="Internal notes about this order"
                                    rows="3">{{ old('notes', $order->notes) }}</x-bladewind::textarea>
                            </div>

                            <x-bladewind::button 
                                type="primary" 
                                can_submit="true"
                                size="small">
                                Update Order
                            </x-bladewind::button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Comment Modal --}}
    <x-bladewind::modal 
        name="add-comment-modal" 
        title="Add Comment to Order"
        size="medium">
        <form method="POST" action="{{ route('admin.orders.timeline.store', $order) }}" class="space-y-4">
            @csrf
            <div>
                <x-bladewind::textarea 
                    name="content"
                    label="Comment"
                    placeholder="Enter your comment..."
                    rows="4"
                    required="true" />
            </div>
            
            <div>
                <x-bladewind::checkbox 
                    name="is_customer_visible"
                    label="Visible to customer"
                    value="1"
                    checked="true" />
            </div>

            <div class="flex justify-end gap-3">
                <x-bladewind::button 
                    color="gray" 
                    outline="true"
                    onclick="hideModal('add-comment-modal')">
                    Cancel
                </x-bladewind::button>
                <x-bladewind::button 
                    type="primary" 
                    can_submit="true">
                    Add Comment
                </x-bladewind::button>
            </div>
        </form>
    </x-bladewind::modal>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-bladewind::notification 
            type="success"
            title="Success!"
            message="{{ session('success') }}"
            show_close_icon="true" />
    @endif

    @if(session('error'))
        <x-bladewind::notification 
            type="error"
            title="Error!"
            message="{{ session('error') }}"
            show_close_icon="true" />
    @endif

</x-app>
