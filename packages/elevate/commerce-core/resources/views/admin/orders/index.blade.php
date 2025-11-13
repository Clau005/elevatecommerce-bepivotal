<x-app pageTitle="Orders" title="Orders - Admin" description="Manage customer orders and transactions">

    <x-table
        title="Orders"
        description="Manage customer orders and transactions"
        :data="$data"
        :columns="$columns"
        :paginator="$orders"
        emptyMessage="No orders found. Try adjusting your filters."
    >
        <x-slot name="filters">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Search --}}
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                placeholder="Order #, customer..." 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        {{-- Status Filter --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="awaiting-payment" {{ request('status') === 'awaiting-payment' ? 'selected' : '' }}>Awaiting Payment</option>
                                <option value="payment-received" {{ request('status') === 'payment-received' ? 'selected' : '' }}>Payment Received</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        {{-- Channel Filter --}}
                        <div>
                            <label for="channel_id" class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                            <select name="channel_id" id="channel_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">All Channels</option>
                                @foreach($channels ?? [] as $channel)
                                    <option value="{{ $channel->id }}" {{ request('channel_id') == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Customer Type Filter --}}
                        <div>
                            <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-1">Customer Type</label>
                            <select name="customer_type" id="customer_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">All Customers</option>
                                <option value="new" {{ request('customer_type') === 'new' ? 'selected' : '' }}>New Customers</option>
                                <option value="returning" {{ request('customer_type') === 'returning' ? 'selected' : '' }}>Returning Customers</option>
                            </select>
                        </div>
                    </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Clear Filters
                    </a>
                </div>
            </form>
        </x-slot>
    </x-table>

</x-app>
