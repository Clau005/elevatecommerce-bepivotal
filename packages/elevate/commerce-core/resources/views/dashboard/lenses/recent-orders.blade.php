@if($orders->isEmpty())
    <div class="text-center py-8 text-gray-500">No recent orders</div>
@else
    <div class="space-y-4">
        @foreach($orders as $order)
            @php
                $statusColors = [
                    'awaiting-payment' => 'bg-yellow-100 text-yellow-800',
                    'payment-received' => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-purple-100 text-purple-800',
                    'shipped' => 'bg-indigo-100 text-indigo-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'refunded' => 'bg-gray-100 text-gray-800',
                ];
                $statusColor = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
            @endphp
            
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.orders.show', $order['id']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            {{ $order['reference'] }}
                        </a>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                            {{ ucfirst(str_replace('-', ' ', $order['status'])) }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600 mt-1">{{ $order['customer_name'] }}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-semibold text-gray-900">£{{ number_format($order['total'] / 100, 2) }}</div>
                    <div class="text-xs text-gray-500">{{ $order['created_at']->diffForHumans() }}</div>
                </div>
            </div>
        @endforeach
    </div>
@endif
