<x-app pageTitle="States & Regions - {{ $country->name }}" title="States - Admin" description="Manage states and regions">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $country->name }} - States & Regions</h1>
                <p class="text-gray-600 mt-1">Manage states and regions for {{ $country->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings.states.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Countries
                </a>
                <a href="{{ route('admin.settings.states.create', $country) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add State/Region
                </a>
            </div>
        </div>

        {{-- Table --}}
        <x-table 
            :data="$tableData" 
            :columns="$columns" 
            :paginator="$states">
        </x-table>
    </div>

    {{-- Delete Confirmation Modal --}}
    <x-bladewind::modal 
        name="delete-state-modal"
        title="Confirm Delete"
        size="md"
        show_close_icon="true"
        ok_button_action="performDelete()"
        ok_button_label="Delete State"
        cancel_button_label="Cancel"
        close_after_action="false">
        
        <div class="text-center py-4">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete State/Region</h3>
            <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete this state/region? This action cannot be undone.</p>
        </div>
    </x-bladewind::modal>

    <script>
        let pendingDeleteStateId = null;
        
        function confirmDelete(id) {
            pendingDeleteStateId = id;
            showModal('delete-state-modal');
        }
        
        function performDelete() {
            if (!pendingDeleteStateId) return false;
            
            hideModal('delete-state-modal');
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/settings/states/'.$country->id) }}/${pendingDeleteStateId}`;
            
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
