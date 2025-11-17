<x-app pageTitle="Currencies" title="Currencies - Admin" description="Manage store currencies and exchange rates">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Currencies</h1>
                <p class="text-gray-600 mt-1">Manage currencies, exchange rates, and currency display settings</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Settings
                </a>
                <a href="{{ route('admin.settings.currencies.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Currency
                </a>
            </div>
        </div>

        {{-- Table --}}
        <x-table 
            :data="$tableData" 
            :columns="$columns" 
            :paginator="$currencies">
            
            <x-slot name="filters">
                <form method="GET" class="flex flex-wrap gap-4">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search currencies..." 
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    {{-- Enabled Filter --}}
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg bg-white cursor-pointer hover:bg-gray-50">
                            <input 
                                type="checkbox" 
                                name="enabled" 
                                value="1"
                                {{ request('enabled') ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="text-sm text-gray-700">Enabled Only</span>
                        </label>
                    </div>

                    {{-- Buttons --}}
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.settings.currencies.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                        Clear
                    </a>
                </form>
            </x-slot>
        </x-table>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="delete-currency-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Currency</h3>
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete this currency? This action cannot be undone.</p>
                <div class="flex gap-3 justify-center mt-6">
                    <button onclick="hideModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button onclick="performDelete()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                        Delete Currency
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for table actions --}}
    <script>
        let pendingDeleteCurrencyId = null;
        
        function confirmDelete(id) {
            pendingDeleteCurrencyId = id;
            document.getElementById('delete-currency-modal').classList.remove('hidden');
        }
        
        function hideModal() {
            document.getElementById('delete-currency-modal').classList.add('hidden');
            pendingDeleteCurrencyId = null;
        }
        
        function performDelete() {
            if (!pendingDeleteCurrencyId) {
                return false;
            }
            
            hideModal();
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/settings/currencies') }}/${pendingDeleteCurrencyId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>

</x-app>
