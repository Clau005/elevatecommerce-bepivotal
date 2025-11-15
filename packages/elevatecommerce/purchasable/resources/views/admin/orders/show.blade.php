@extends('core::admin.layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <x-core::heading level="1" subtitle="Order placed {{ $order->created_at->format('M d, Y \a\t h:i A') }}">
                Order #{{ $order->order_number }}
            </x-core::heading>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
        </div>
    </div>

    @if(session('success'))
    <x-core::alert type="success">
        {{ session('success') }}
    </x-core::alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                </x-slot>

                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <div class="py-4 flex items-start space-x-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md flex items-center justify-center">
                            @if($item->metadata['image'] ?? null)
                                <img src="{{ $item->metadata['image'] }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded-md">
                            @else
                                <i class="fas fa-box text-gray-400 text-2xl"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $item->name }}</p>
                            <p class="text-sm text-gray-500">SKU: {{ $item->sku }}</p>
                            @if($item->options)
                                <p class="text-xs text-gray-500 mt-1">
                                    @foreach($item->options as $key => $value)
                                        <span class="inline-block mr-2">{{ ucfirst($key) }}: {{ $value }}</span>
                                    @endforeach
                                </p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">@currency($item->price) × {{ $item->quantity }}</p>
                            <p class="text-sm text-gray-500">@currency($item->line_total)</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Order Totals -->
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-900">@currency($order->subtotal)</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-medium text-green-600">-@currency($order->discount)</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium text-gray-900">@currency($order->shipping)</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium text-gray-900">@currency($order->tax)</span>
                    </div>
                    <div class="flex justify-between text-base font-semibold pt-2 border-t border-gray-200">
                        <span class="text-gray-900">Total</span>
                        <span class="text-gray-900">@currency($order->total) {{ $order->currency_code }}</span>
                    </div>
                </div>
            </x-core::card>

            <!-- Shipping Address -->
            @if($order->shippingAddress())
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Shipping Address</h3>
                </x-slot>

                @php $shipping = $order->shippingAddress(); @endphp
                <div class="text-sm text-gray-900">
                    <p class="font-medium">{{ $shipping->full_name }}</p>
                    @if($shipping->company)
                        <p>{{ $shipping->company }}</p>
                    @endif
                    @foreach($shipping->formatted_address_lines as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                </div>
            </x-core::card>
            @endif

            <!-- Billing Address -->
            @if($order->billingAddress())
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Billing Address</h3>
                </x-slot>

                @php $billing = $order->billingAddress(); @endphp
                <div class="text-sm text-gray-900">
                    <p class="font-medium">{{ $billing->full_name }}</p>
                    @if($billing->company)
                        <p>{{ $billing->company }}</p>
                    @endif
                    @foreach($billing->formatted_address_lines as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                </div>
            </x-core::card>
            @endif

            <!-- Order Timeline -->
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Order Timeline</h3>
                </x-slot>

                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($order->timeline as $index => $event)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-circle text-white text-xs"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $event->formatted_event }}</p>
                                            <p class="text-sm text-gray-500">{{ $event->description }}</p>
                                            @if($event->note)
                                                <p class="mt-1 text-sm text-gray-600 italic">Note: {{ $event->note }}</p>
                                            @endif
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $event->actor_name }} • {{ $event->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </x-core::card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Status -->
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Order Status</h3>
                </x-slot>

                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div>
                        <label for="status_note" class="block text-sm font-medium text-gray-700 mb-1">Note (optional)</label>
                        <textarea name="note" id="status_note" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Add a note about this status change..."></textarea>
                    </div>
                    <x-core::button type="submit" variant="primary" class="w-full">
                        Update Status
                    </x-core::button>
                </form>
            </x-core::card>

            <!-- Payment Status -->
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Payment</h3>
                </x-slot>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Status</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($order->payment_status ?? 'pending') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Method</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                    </div>
                    @if($order->payment_transaction_id)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Transaction ID</span>
                        <span class="font-mono text-xs text-gray-900">{{ $order->payment_transaction_id }}</span>
                    </div>
                    @endif
                </div>

                <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST" class="space-y-4 pt-4 border-t border-gray-200">
                    @csrf
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Update Payment Status</label>
                        <select name="payment_status" id="payment_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <x-core::button type="submit" variant="secondary" class="w-full">
                        Update Payment
                    </x-core::button>
                </form>
            </x-core::card>

            <!-- Shipping Info -->
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Shipping</h3>
                </x-slot>

                @if($order->tracking_number)
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tracking Number</span>
                        <span class="font-mono text-xs text-gray-900">{{ $order->tracking_number }}</span>
                    </div>
                    @if($order->shipping_method)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Method</span>
                        <span class="font-medium text-gray-900">{{ $order->shipping_method }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <form action="{{ route('admin.orders.update-tracking', $order) }}" method="POST" class="space-y-4 {{ $order->tracking_number ? 'pt-4 border-t border-gray-200' : '' }}">
                    @csrf
                    <div>
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                        <x-core::input 
                            id="tracking_number"
                            name="tracking_number"
                            value="{{ $order->tracking_number }}"
                            placeholder="Enter tracking number"
                        />
                    </div>
                    <div>
                        <label for="shipping_method" class="block text-sm font-medium text-gray-700 mb-1">Shipping Method</label>
                        <x-core::input 
                            id="shipping_method"
                            name="shipping_method"
                            value="{{ $order->shipping_method }}"
                            placeholder="e.g., UPS Ground"
                        />
                    </div>
                    <x-core::button type="submit" variant="secondary" class="w-full">
                        Update Tracking
                    </x-core::button>
                </form>
            </x-core::card>

            <!-- Customer Info -->
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Customer</h3>
                </x-slot>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Name</span>
                        <span class="font-medium text-gray-900">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Email</span>
                        <span class="font-medium text-gray-900">{{ $order->customer_email }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Type</span>
                        <span class="font-medium text-gray-900">{{ $order->isGuest() ? 'Guest' : 'Registered' }}</span>
                    </div>
                </div>

                @if($order->customer_note)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-1">Customer Note</p>
                    <p class="text-sm text-gray-600 italic">{{ $order->customer_note }}</p>
                </div>
                @endif
            </x-core::card>

            <!-- Admin Notes -->
            <x-core::card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium text-gray-900">Admin Notes</h3>
                </x-slot>

                @if($order->admin_note)
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-gray-700">{{ $order->admin_note }}</p>
                </div>
                @endif

                <form action="{{ route('admin.orders.add-note', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <textarea name="note" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Add internal notes about this order...">{{ $order->admin_note }}</textarea>
                    </div>
                    <x-core::button type="submit" variant="secondary" class="w-full">
                        {{ $order->admin_note ? 'Update Note' : 'Add Note' }}
                    </x-core::button>
                </form>
            </x-core::card>
        </div>
    </div>
</div>
@endsection
