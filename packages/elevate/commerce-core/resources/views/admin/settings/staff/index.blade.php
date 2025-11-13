<x-app pageTitle="Staff Management" title="Staff - Admin" description="Manage staff members and their permissions">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Staff Members</h1>
                <p class="text-gray-600 mt-1">Manage staff members and their access permissions</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Settings
                </a>
                <a href="{{ route('admin.settings.staff.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Staff Member
                </a>
            </div>
        </div>

        {{-- Table --}}
        <x-table 
            :data="$staff['data']" 
            :columns="$staff['columns']" 
            :paginator="$staff['paginator']">
            
            <x-slot name="filters">
                <form method="GET" class="flex flex-wrap gap-4">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search staff..." 
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    {{-- Super Admin Filter --}}
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg bg-white cursor-pointer hover:bg-gray-50">
                            <input 
                                type="checkbox" 
                                name="is_super_admin" 
                                value="1"
                                {{ request('is_super_admin') ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm font-medium text-gray-700">Super Admins Only</span>
                        </label>
                    </div>

                    {{-- Filter Button --}}
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        Apply Filters
                    </button>

                    @if(request()->hasAny(['search', 'is_super_admin']))
                        <a href="{{ route('admin.settings.show', 'staff') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                            Clear
                        </a>
                    @endif
                </form>
            </x-slot>
            
        </x-table>
    </div>

    {{-- Delete Confirmation Modal --}}
    <x-bladewind::modal 
        name="delete-staff-modal"
        title="Confirm Delete"
        size="md"
        show_close_icon="true"
        ok_button_action="performDelete()"
        ok_button_label="Delete Staff Member"
        cancel_button_label="Cancel"
        close_after_action="false">
        
        <div class="text-center py-4">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Staff Member</h3>
            <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete this staff member? This action cannot be undone.</p>
        </div>
    </x-bladewind::modal>

    {{-- JavaScript for table actions --}}
    <script>
        // Variables to store the staff ID for deletion
        let pendingDeleteStaffId = null;
        
        function editStaff(id) {
            window.location.href = `{{ url('admin/settings/staff') }}/${id}/edit`;
        }
        
        function confirmDelete(id) {
            pendingDeleteStaffId = id;
            showModal('delete-staff-modal');
        }
        
        function performDelete() {
            if (!pendingDeleteStaffId) {
                return false;
            }
            
            hideModal('delete-staff-modal');
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/settings/staff') }}/${pendingDeleteStaffId}`;
            
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
            
            pendingDeleteStaffId = null;
            
            return true;
        }
    </script>

</x-app>