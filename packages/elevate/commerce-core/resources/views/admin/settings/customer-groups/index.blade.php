<x-app pageTitle="Customer Groups" title="Customer Groups - Settings" description="Manage customer groups and classifications">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customer Groups</h1>
                <p class="text-gray-600 mt-1">Manage customer groups and classifications</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Settings
                </a>
                <a href="{{ route('admin.settings.customer-groups.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Customer Group
                </a>
            </div>
        </div>

        {{-- Table --}}
        <x-table 
            :data="$customerGroups['data']" 
            :columns="$customerGroups['columns']" 
            :paginator="$customerGroups['paginator']">
            
            <x-slot name="filters">
                <form method="GET" class="flex flex-wrap gap-4">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search groups..." 
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    {{-- Default Filter --}}
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg bg-white cursor-pointer hover:bg-gray-50">
                            <input 
                                type="checkbox" 
                                name="is_default" 
                                value="1"
                                {{ request('is_default') ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm font-medium text-gray-700">Default Only</span>
                        </label>
                    </div>

                    {{-- Filter Button --}}
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        Apply Filters
                    </button>

                    @if(request()->hasAny(['search', 'is_default']))
                        <a href="{{ route('admin.settings.show', 'customer-groups') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                            Clear
                        </a>
                    @endif
                </form>
            </x-slot>
            
        </x-table>
    </div>

    {{-- JavaScript for table actions --}}
    <script>
        function editCustomerGroup(id) {
            window.location.href = `{{ url('/admin/settings/customer-groups') }}/${id}/edit`;
        }
        
        function setAsDefault(id) {
            if (confirm('Are you sure you want to set this as the default customer group?')) {
                // Create a form and submit it for PATCH request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/admin/settings/customer-groups') }}/${id}/set-default`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method spoofing for PATCH
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function confirmDeleteCustomerGroup(id) {
            if (confirm('Are you sure you want to delete this customer group? This action cannot be undone.')) {
                // Create a form and submit it for DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/admin/settings/customer-groups') }}/${id}`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method spoofing for DELETE
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