<x-app pageTitle="Discounts" title="Discounts - Admin">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Discounts</h1>
                <p class="text-gray-600 mt-1">Manage discount codes and promotional offers</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Settings
                </a>
                <a href="{{ route('admin.settings.discounts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Discount
                </a>
            </div>
        </div>

        {{-- Table --}}
        <x-table 
            :data="$discounts['data']" 
            :columns="$discounts['columns']" 
            :paginator="$discounts['paginator']">
            
            <x-slot name="filters">
                <form method="GET" class="flex flex-wrap gap-4">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search discounts..." 
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    {{-- Status Filter --}}
                    <div class="w-40">
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Type Filter --}}
                    <div class="w-48">
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Types</option>
                            <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed_amount" {{ request('type') === 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                            <option value="free_shipping" {{ request('type') === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                            <option value="buy_x_get_y" {{ request('type') === 'buy_x_get_y' ? 'selected' : '' }}>Buy X Get Y</option>
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                        Filter
                    </button>

                    {{-- Clear Button --}}
                    @if(request()->hasAny(['search', 'status', 'type']))
                        <a href="{{ route('admin.settings.discounts.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Clear
                        </a>
                    @endif
                </form>
            </x-slot>
            
        </x-table>
    </div>

    {{-- JavaScript for table actions --}}
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this discount? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/settings/discounts') }}/${id}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app>