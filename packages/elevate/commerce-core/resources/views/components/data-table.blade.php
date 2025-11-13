@props([
    'title' => '',
    'description' => '',
    'data' => [],
    'columns' => [],
    'searchable' => true,
    'filterable' => true,
    'paginated' => true,
    'perPageOptions' => [10, 25, 50, 100],
    'defaultPerPage' => 25,
    'showBulkActions' => true,
    'addButton' => null,
    'emptyMessage' => 'No records found',
    'emptyIcon' => 'users',
    'pagination' => null,
    'total' => 0
])
{{-- @php
    dd($data);
@endphp --}}
<div class="space-y-6">
    {{-- Header --}}
    @if($title || $description || $addButton)
        <div class="flex flex-row justify-between items-center">
            <div>
                @if($title)
                    <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                @endif
                @if($description)
                    <p class="text-gray-600 mt-1">{{ $description }}</p>
                @endif
            </div>

            @if($addButton)
                {{-- <a href="{{ $addButton['url'] }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    @if(isset($addButton['icon']))
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    @endif
                    {{ $addButton['text'] ?? 'Add New' }}
                </a> --}}
                <x-bladewind::button icon="plus"  onClick="window.location.href = '{{ $addButton['url'] }}'">{{ $addButton['text'] ?? 'Add New' }}</x-bladewind::button>

                
            @endif
        </div>
    @endif

    {{-- Table Container --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        {{-- Top Bar --}}
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                {{-- Left: Bulk Actions --}}
                @if($showBulkActions)
                    <div class="flex items-center gap-3 w-1/2">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                                Bulk actions
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute left-0 z-50 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    {{ $bulkActions ?? '' }}
                                    @if(!isset($bulkActions))
                                        <button onclick="exportSelected()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export selected</button>
                                        <button onclick="deleteSelected()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Delete selected</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <span id="selection-count" class="text-sm text-gray-600">0 selected</span>
                    </div>
                @endif
                
                {{-- Right: Search and Filters --}}
                <div class="flex justify-end items-center space-x-2">
                    {{-- Search --}}
                    @if($searchable)
                        <form method="GET" class="">
                            {{-- Preserve other parameters --}}
                            @foreach(request()->except(['search', 'page']) as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            
                            <x-bladewind::input
                                name="search"
                                add_clearing="false" 
                                value="{{ request('search') }}"
                                placeholder="Search records..."
                                onkeyup="debouncedSearch(event)"
                                autocomplete="off" />
                        </form>
                    @endif
                    
                    {{-- Custom Filters Slot --}}
                    @if($filterable && isset($filters))
                        {{ $filters }}
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Active Filters Display --}}
        @php
            $activeFilters = collect(request()->except(['page', 'per_page', 'search']))->filter();
            $hasActiveFilters = request('search') || $activeFilters->count() > 0;
        @endphp
        
        @if($hasActiveFilters)
            <div class="px-4 py-2 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm text-gray-600">Active filters:</span>
                    
                    {{-- Search Filter --}}
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            Search: "{{ request('search') }}"
                            <button type="button" onclick="removeFilter('search')" class="ml-1 text-blue-600 hover:text-blue-800">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    
                    {{-- Other Filters --}}
                    @foreach($activeFilters as $key => $value)
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $key)) }}: "{{ $value }}"
                            <button type="button" onclick="removeFilter('{{ $key }}')" class="ml-1 text-blue-600 hover:text-blue-800">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    @endforeach
                    
                    <button type="button" onclick="clearAllFilters()" class="text-xs text-gray-500 hover:text-gray-700 ml-2">
                        Clear all
                    </button>
                </div>
            </div>
        @endif

        {{-- Table --}}
        @if(count($data) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if($showBulkActions)
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleSelectAll(this)">
                                </th>
                            @endif
                            
                            @foreach($columns as $key => $column)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ isset($column['sortable']) && $column['sortable'] ? 'cursor-pointer hover:bg-gray-100' : '' }}" 
                                    @if(isset($column['sortable']) && $column['sortable']) onclick="sortBy('{{ $key }}')" @endif>
                                    <div class="flex items-center gap-1">
                                        {{ $column['label'] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                        @if(isset($column['sortable']) && $column['sortable'])
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                            
                            {{-- Actions column is now handled through columns array --}}
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data as $row)
                            <tr class="hover:bg-gray-50">
                                @if($showBulkActions)
                                    <td class="px-6 py-4">
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $row['id'] ?? '' }}" onchange="updateBulkActions()">
                                    </td>
                                @endif
                                
                                @foreach($columns as $key => $column)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if(isset($column['render']))
                                            {!! $column['render']($row) !!}
                                        @else
                                            {{ $row[$key] ?? '-' }}
                                        @endif
                                    </td>
                                @endforeach
                                
                                {{-- Actions are now handled through columns array with render functions --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination Footer --}}
            @if($paginated && $pagination)
                <div class="px-6 py-3 border-t border-gray-200 bg-white">
                    <div class="flex items-center justify-between">
                        {{-- Results Info --}}
                        <div class="text-sm text-gray-700">
                            @php
                                $from = ($pagination['current_page'] - 1) * $pagination['per_page'] + 1;
                                $to = min($from + count($data) - 1, $pagination['total']);
                            @endphp
                            Showing {{ $from }} to {{ $to }} of {{ number_format($pagination['total']) }} results
                        </div>
                        
                        {{-- Per Page Selector --}}
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Per page</span>
                            <select onchange="changePerPage(this.value)" class="rounded border-gray-300 text-sm">
                                @foreach($perPageOptions as $size)
                                    <option value="{{ $size }}" {{ request('per_page', $defaultPerPage) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Page Numbers --}}
                        <div class="flex items-center gap-1">
                            {{-- Previous --}}
                            @if($pagination['current_page'] > 1)
                                <button onclick="goToPage({{ $pagination['current_page'] - 1 }})" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">
                                    Previous
                                </button>
                            @endif
                            
                            {{-- Page Numbers --}}
                            @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++)
                                <button onclick="goToPage({{ $i }})" class="px-3 py-1 text-sm border {{ $i == $pagination['current_page'] ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-50' }} rounded">
                                    {{ $i }}
                                </button>
                            @endfor
                            
                            {{-- Next --}}
                            @if($pagination['current_page'] < $pagination['last_page'])
                                <button onclick="goToPage({{ $pagination['current_page'] + 1 }})" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">
                                    Next
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="p-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239" />
                    </svg>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No data found</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $emptyMessage }}</p>
                @if(isset($addButton) && $addButton)
                    <div class="mt-6">
                        {{-- <a href="{{ $addButton['url'] }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            @if(isset($addButton['icon']))
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            @endif
                            {{ $addButton['text'] ?? 'Add New' }}
                        </a> --}}
                        <x-bladewind::button icon="plus">{{ $addButton['text'] ?? 'Add New' }}</x-bladewind::button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- JavaScript --}}
<script>
    let searchTimeout;
    
    function debouncedSearch(event) {
        const searchTerm = event.target.value.trim();
        
        // Clear the previous timeout
        clearTimeout(searchTimeout);
        
        // Set a new timeout to search after 800ms of inactivity
        searchTimeout = setTimeout(() => {
            performSearch(searchTerm);
        }, 800);
    }
    
    function performSearch(searchTerm) {
        // Option 1: Livewire
        if (typeof Livewire !== 'undefined') {
            Livewire.emit('search', searchTerm);
            return;
        }
        
        // Option 2: Alpine.js
        if (typeof Alpine !== 'undefined' && window.searchData) {
            window.searchData.search = searchTerm;
            window.searchData.submit();
            return;
        }
        
        // Option 3: URL-based search (prevents session issues)
        const url = new URL(window.location);
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        url.searchParams.delete('page'); // Reset to first page
        
        // Use replaceState to avoid adding to browser history for every keystroke
        window.history.replaceState({}, '', url.toString());
        
        // Then navigate to the URL
        window.location.href = url.toString();
    }
</script>


<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        updateBulkActions();
    }
    
    function updateBulkActions() {
        const selected = document.querySelectorAll('.row-checkbox:checked').length;
        const total = document.querySelectorAll('.row-checkbox').length;
        const selectionCount = document.getElementById('selection-count');
        const selectAllCheckbox = document.getElementById('select-all');
        
        if (selectionCount) {
            if (selected === 0) {
                selectionCount.textContent = '0 selected';
                selectionCount.className = 'text-sm text-gray-600';
            } else {
                selectionCount.textContent = `${selected} selected`;
                selectionCount.className = 'text-sm text-blue-600 font-medium';
            }
        }
        
        if (selectAllCheckbox) {
            if (selected === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (selected === total) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
    }
    
    function sortBy(column) {
        const url = new URL(window.location);
        const currentSort = url.searchParams.get('sort_by');
        const currentDirection = url.searchParams.get('sort_direction');
        
        if (currentSort === column && currentDirection === 'asc') {
            url.searchParams.set('sort_direction', 'desc');
        } else {
            url.searchParams.set('sort_direction', 'asc');
        }
        url.searchParams.set('sort_by', column);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }
    
    function goToPage(page) {
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }
    
    function changePerPage(value) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }
    
    function removeFilter(filterName) {
        const url = new URL(window.location);
        url.searchParams.delete(filterName);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }
    
    function clearAllFilters() {
        const url = new URL(window.location);
        url.search = '';
        window.location.href = url.toString();
    }
    
    function exportSelected() {
        const selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select items to export');
            return;
        }
        console.log('Exporting:', selected);
    }
    
    function deleteSelected() {
        const selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select items to delete');
            return;
        }
        if (confirm(`Are you sure you want to delete ${selected.length} selected items?`)) {
            console.log('Deleting:', selected);
        }
    }
    
    // Default action functions (can be overridden)
    function viewCustomer(id) {
        console.log('View customer:', id);
        // Override this function in your page
    }
    
    function editCustomer(id) {
        console.log('Edit customer:', id);
        // Override this function in your page
    }
    
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            console.log('Delete customer:', id);
            // Override this function in your page
        }
    }
</script>
