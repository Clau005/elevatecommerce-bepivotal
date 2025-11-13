<x-app pageTitle="Roles" title="Roles - Settings" description="Manage user roles and permissions">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Roles</h1>
                <p class="text-gray-600 mt-1">Manage user roles and permissions</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Settings
                </a>
                <a href="{{ route('admin.settings.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Role
                </a>
            </div>
        </div>

        {{-- Table --}}
        <x-table 
            :data="$roles['data']" 
            :columns="$roles['columns']" 
            :paginator="$roles['paginator']">
            
            <x-slot name="filters">
                <form method="GET" class="flex flex-wrap gap-4">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search roles..." 
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    {{-- Guard Filter --}}
                    <div class="w-48">
                        <select name="guard_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Guards</option>
                            <option value="web" {{ request('guard_name') === 'web' ? 'selected' : '' }}>Web</option>
                            <option value="api" {{ request('guard_name') === 'api' ? 'selected' : '' }}>API</option>
                            <option value="staff" {{ request('guard_name') === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                        Filter
                    </button>

                    {{-- Clear Button --}}
                    @if(request()->hasAny(['search', 'guard_name']))
                        <a href="{{ route('admin.settings.roles.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Clear
                        </a>
                    @endif
                </form>
            </x-slot>
            
        </x-table>
    </div>

    {{-- JavaScript for table actions --}}
    <script>
        function editRole(id) {
            window.location.href = `{{ url('/admin/settings/roles') }}/${id}/edit`;
        }
        
        function managePermissions(id) {
            window.location.href = `{{ url('/admin/settings/roles') }}/${id}/permissions`;
        }
        
        function confirmDeleteRole(id) {
            if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
                // Create a form and submit it for DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/admin/settings/roles') }}/${id}`;
                
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