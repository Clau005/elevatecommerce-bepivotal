<x-app pageTitle="Customer Details" title="Customer Details - Admin" description="View customer information and order history">

    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $customer->full_name }}</h1>
                        @if($customer->company_name)
                            <span class="text-sm text-gray-500">at {{ $customer->company_name }}</span>
                        @endif
                    </div>
                    <p class="text-gray-600 mt-1">Customer since {{ $customer->created_at->format('M j, Y') }}</p>
                </div>
                <div class="flex gap-3">
                    <x-bladewind::button 
                        color="blue" 
                        outline="true"
                        onclick="window.location.href='{{ route('admin.customers.edit', $customer) }}'">
                        Edit Customer
                    </x-bladewind::button>
                    <x-bladewind::button 
                        color="gray" 
                        outline="true"
                        onclick="window.location.href='{{ route('admin.customers.index') }}'">
                        Back to Customers
                    </x-bladewind::button>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_orders']) }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Spent</dt>
                            <dd class="text-lg font-medium text-gray-900">£{{ number_format($stats['total_spent'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Average Order</dt>
                            <dd class="text-lg font-medium text-gray-900">£{{ number_format($stats['average_order_value'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0V9a4 4 0 018 0v6z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Last Order</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $stats['last_order_date'] ? $stats['last_order_date']->diffForHumans() : 'Never' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Customer Information --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Customer Information</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        {{-- Personal Details --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Personal Details</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Name:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->full_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Email:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->email }}</dd>
                                </div>
                                @if($customer->title)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Title:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->title }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        {{-- Company Details --}}
                        @if($customer->company_name || $customer->tax_identifier)
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Company Details</h4>
                            <dl class="space-y-2">
                                @if($customer->company_name)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Company:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->company_name }}</dd>
                                </div>
                                @endif
                                @if($customer->tax_identifier)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Tax ID:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->tax_identifier }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                        @endif

                        {{-- Account Details --}}
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Account Details</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Account Ref:</dt>
                                    <dd class="text-sm text-gray-900 font-mono">{{ $customer->account_reference }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Customer Group:</dt>
                                    <dd class="text-sm text-gray-900">
                                        @switch($customer->customer_group_id)
                                            @case(1) Default @break
                                            @case(2) VIP @break
                                            @case(3) Wholesale @break
                                            @case(4) Retail @break
                                            @default Default
                                        @endswitch
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Joined:</dt>
                                    <dd class="text-sm text-gray-900">{{ $customer->created_at->format('M j, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Account Age:</dt>
                                    <dd class="text-sm text-gray-900">{{ $stats['account_age_days'] }} days</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order History --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Order History</h3>
                            <span class="text-sm text-gray-500">{{ $orders->total() }} total orders</span>
                        </div>
                    </div>
                    
                    @if($orders->count() > 0)
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Channel</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">#{{ $order->reference }}</div>
                                            @if($order->customer_reference)
                                                <div class="text-sm text-gray-500">{{ $order->customer_reference }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $order->created_at->format('M j, Y') }}
                                            <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                @switch($order->status)
                                                    @case('completed') bg-green-100 text-green-800 @break
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @case('processing') bg-blue-100 text-blue-800 @break
                                                    @case('cancelled') bg-red-100 text-red-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch
                                            ">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $order->channel->name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            £{{ number_format($order->total / 100, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                                View Order
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($orders->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $orders->links() }}
                        </div>
                        @endif
                    @else
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
                            <p class="mt-1 text-sm text-gray-500">This customer hasn't placed any orders yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
