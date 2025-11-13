<x-app pageTitle="Gift Voucher Details" title="Gift Voucher Details - Admin" description="View gift voucher information and usage history">

    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $giftVoucher->title }}</h1>
                    <p class="text-gray-600 mt-1">Gift Voucher • Created {{ $giftVoucher->created_at->format('M d, Y') }}</p>
                </div>
                <div class="flex gap-3">
                    <x-bladewind::button 
                        color="blue" 
                        outline="true"
                        icon="pencil"
                        icon_left="true"
                        onclick="window.location.href='{{ route('admin.settings.gift-vouchers.edit', $giftVoucher) }}'">
                        Edit Gift Voucher
                    </x-bladewind::button>
                    <x-bladewind::button 
                        color="gray" 
                        outline="true"
                        icon="arrow-left"
                        icon_left="true"
                        onclick="window.location.href='{{ route('admin.settings.show', 'gift-vouchers') }}'">
                        Back to Gift Vouchers
                    </x-bladewind::button>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            {{-- Total Uses --}}
            <x-bladewind::statistic 
                number="{{ number_format($giftVoucher->usages_count ?? 0) }}"
                label="Total Uses"
                color="blue" >
                <x-slot name="icon">
                    <x-bladewind::icon name="ticket" />
                </x-slot>
            </x-bladewind::statistic>

            {{-- Remaining Uses --}}
            <x-bladewind::statistic 
                number="{{ $giftVoucher->usage_limit ? number_format($giftVoucher->remaining_uses) : 'Unlimited' }}"
                label="Remaining Uses"
                color="green" >
                <x-slot name="icon">
                    <x-bladewind::icon name="check-circle" />
                </x-slot>
            </x-bladewind::statistic>

            {{-- Voucher Value --}}
            <x-bladewind::statistic 
                number="{{ $giftVoucher->formatted_value }}"
                label="Voucher Value"
                color="purple" />

            {{-- Status --}}
            <x-bladewind::statistic 
                number="{{ ucfirst($giftVoucher->status) }}"
                label="Status"
                color="{{ $giftVoucher->is_valid ? 'green' : 'red' }}" >
                <x-slot name="icon">
                    <x-bladewind::icon name="{{ $giftVoucher->is_valid ? 'check-circle' : 'x-circle' }}" />
                </x-slot>
            </x-bladewind::statistic>

            {{-- Featured --}}
            <x-bladewind::statistic 
                number="{{ $giftVoucher->is_featured ? 'Yes' : 'No' }}"
                label="Featured"
                color="{{ $giftVoucher->is_featured ? 'yellow' : 'gray' }}" >
                <x-slot name="icon">
                    <x-bladewind::icon name="{{ $giftVoucher->is_featured ? 'star' : 'star-outline' }}" />
                </x-slot>
            </x-bladewind::statistic>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Gift Voucher Information --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Gift Voucher Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Voucher Image --}}
                        <div>
                            @if($giftVoucher->image_url)
                                <div class="mb-4">
                                    <img src="{{ $giftVoucher->image_url }}" 
                                         alt="{{ $giftVoucher->title }}" 
                                         class="w-full h-48 object-cover rounded-lg border border-gray-200">
                                </div>
                            @else
                                <div class="mb-4 flex items-center justify-center h-48 bg-gray-100 rounded-lg border border-gray-200">
                                    <x-bladewind::icon name="gift" class="w-16 h-16 text-gray-400" />
                                </div>
                            @endif
                        </div>

                        {{-- Basic Details --}}
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Voucher Code</dt>
                                <dd class="mt-1 text-lg font-mono font-semibold text-gray-900 bg-gray-100 px-3 py-2 rounded">{{ $giftVoucher->code }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Value</dt>
                                <dd class="mt-1 text-lg font-semibold text-green-600">{{ $giftVoucher->formatted_value }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $giftVoucher->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $giftVoucher->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $giftVoucher->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($giftVoucher->status) }}
                                    </span>
                                </dd>
                            </div>

                            @if($giftVoucher->is_featured)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Featured</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <x-bladewind::icon name="star" class="w-3 h-3 mr-1" />
                                            Featured
                                        </span>
                                    </dd>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($giftVoucher->description)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $giftVoucher->description }}</dd>
                        </div>
                    @endif
                </div>

                {{-- Validity & Limits --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Validity & Limits</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Valid From</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $giftVoucher->valid_from ? $giftVoucher->valid_from->format('M j, Y H:i') : 'Immediately' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Valid Until</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $giftVoucher->valid_until ? $giftVoucher->valid_until->format('M j, Y H:i') : 'No expiry' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Usage Limit</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $giftVoucher->usage_limit ? number_format($giftVoucher->usage_limit) : 'Unlimited' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Per Customer Limit</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $giftVoucher->per_customer_limit ? number_format($giftVoucher->per_customer_limit) : 'Unlimited' }}
                            </dd>
                        </div>
                    </div>
                </div>

                {{-- Usage History --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Usage History</h3>
                    
                    @if($giftVoucher->usages->count() > 0)
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Used</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount Applied</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($giftVoucher->usages as $usage)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $usage->used_at->format('M j, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($usage->usedByUser)
                                                    <div>
                                                        <div class="font-medium">{{ $usage->usedByUser->first_name }} {{ $usage->usedByUser->last_name }}</div>
                                                        <div class="text-gray-500">{{ $usage->usedByUser->email }}</div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">Guest</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($usage->usedInOrder)
                                                    <a href="{{ route('admin.orders.show', $usage->usedInOrder->id) }}" 
                                                       class="text-blue-600 hover:text-blue-900">
                                                        #{{ $usage->usedInOrder->order_number }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                                {{ $usage->formatted_discount_applied }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-bladewind::icon name="document-text" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No usage history</h3>
                            <p class="mt-1 text-sm text-gray-500">This gift voucher has not been used yet.</p>
                        </div>
                    @endif
                </div>

                @if($giftVoucher->meta_title || $giftVoucher->meta_description || $giftVoucher->meta_keywords)
                    {{-- SEO Information --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">SEO Information</h3>
                        
                        <div class="space-y-4">
                            @if($giftVoucher->meta_title)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Meta Title</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $giftVoucher->meta_title }}</dd>
                                </div>
                            @endif
                            @if($giftVoucher->meta_description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $giftVoucher->meta_description }}</dd>
                                </div>
                            @endif
                            @if($giftVoucher->meta_keywords)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Meta Keywords</dt>
                                    <dd class="mt-1">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($giftVoucher->meta_keywords as $keyword)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $keyword }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </dd>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Quick Stats --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Quick Stats</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $giftVoucher->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $giftVoucher->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $giftVoucher->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($giftVoucher->status) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Validity:</span>
                            <span class="text-sm font-medium {{ $giftVoucher->is_valid ? 'text-green-600' : 'text-red-600' }}">
                                {{ $giftVoucher->is_valid ? 'Valid' : 'Invalid' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Uses:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $giftVoucher->usages_count ?? 0 }}</span>
                        </div>
                        
                        @if($giftVoucher->usage_limit)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Remaining Uses:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $giftVoucher->remaining_uses }}</span>
                            </div>
                            
                            @if($giftVoucher->usage_percentage !== null)
                                <div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Usage Progress:</span>
                                        <span class="font-medium text-gray-900">{{ number_format($giftVoucher->usage_percentage, 1) }}%</span>
                                    </div>
                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($giftVoucher->usage_percentage, 100) }}%"></div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Sort Order:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $giftVoucher->sort_order }}</span>
                        </div>
                    </div>
                </div>

                {{-- Timestamps --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Timestamps</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Created:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $giftVoucher->created_at->format('M j, Y H:i') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Last Updated:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $giftVoucher->updated_at->format('M j, Y H:i') }}</span>
                        </div>
                        
                        @if($giftVoucher->usages->count() > 0)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Last Used:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $giftVoucher->usages->sortByDesc('used_at')->first()->used_at->format('M j, Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Actions</h3>
                    
                    <div class="space-y-3">
                        <x-bladewind::button 
                            color="blue"
                            size="small"
                            class="w-full"
                            onclick="window.location.href='{{ route('admin.settings.gift-vouchers.edit', $giftVoucher) }}'">
                            Edit Gift Voucher
                        </x-bladewind::button>
                        
                        @if($giftVoucher->usages_count == 0)
                            <x-bladewind::button 
                                color="red"
                                size="small"
                                class="w-full"
                                onclick="confirmDelete()">
                                Delete Gift Voucher
                            </x-bladewind::button>
                        @endif
                        
                        <x-bladewind::button 
                            color="gray"
                            size="small"
                            class="w-full"
                            onclick="copyCode()">
                            Copy Voucher Code
                        </x-bladewind::button>
                    </div>
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

    <script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this gift voucher? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.settings.gift-vouchers.destroy", $giftVoucher->id) }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = '{{ csrf_token() }}';
            
            form.appendChild(methodInput);
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function copyCode() {
        const code = '{{ $giftVoucher->code }}';
        navigator.clipboard.writeText(code).then(function() {
            // Show success notification
            showNotification('Voucher code copied to clipboard!', 'success');
        }).catch(function() {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = code;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showNotification('Voucher code copied to clipboard!', 'success');
        });
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg font-bold">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }
    </script>
</x-app>