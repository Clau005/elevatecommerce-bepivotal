@props([
    'title' => 'Filters',
    'buttonText' => 'Filters',
    'slideoverId' => 'filter-slideover'
])

{{-- Filter Button --}}
{{-- <button onclick="openSlideover('{{ $slideoverId }}')"
        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
    </svg>
    {{ $buttonText }}
    @php
        $activeCount = collect(request()->except(['page', 'per_page', 'search']))->filter()->count();
    @endphp
    @if($activeCount > 0)
        <span class="ml-1 bg-blue-100 text-blue-800 text-xs rounded-full px-2 py-1">{{ $activeCount }}</span>
    @endif
</button> --}}

<x-bladewind::button onclick="openSlideover('{{ $slideoverId }}')" icon="wrench" outline="true" color="slate"  >
    {{ $buttonText }}
</x-bladewind::button>

{{-- Slideover --}}
<div id="{{ $slideoverId }}" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50" onclick="closeSlideover('{{ $slideoverId }}')"></div>
    
    {{-- Slideover Panel --}}
    <div class="fixed right-0 top-0 h-full w-96 bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col" id="{{ $slideoverId }}-panel">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <h2 class="text-lg font-medium text-gray-900">{{ $title }}</h2>
            <button onclick="closeSlideover('{{ $slideoverId }}')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        {{-- Scrollable Content --}}
        <div class="flex-1 overflow-y-auto">
            <form method="GET" id="{{ $slideoverId }}-form" class="h-full flex flex-col" onsubmit="return submitFilters(event, '{{ $slideoverId }}')">
                {{-- Preserve search --}}
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                
                {{-- Filter Content - Scrollable Area --}}
                <div class="flex-1 p-6 space-y-6 overflow-y-auto">
                    {{ $slot }}
                </div>
                
                {{-- Actions - Fixed at bottom --}}
                <div class="flex justify-between p-6 border-t border-gray-200 bg-white flex-shrink-0">
                    {{-- <button type="button" onclick="clearFilters('{{ $slideoverId }}')" 
                            class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50">
                        Clear All
                    </button> --}}
                    <x-bladewind::button size="small" color="red" outline="true" onClick="openSlideover('{{ $slideoverId }}')" >
                        Clear All
                    </x-bladewind::button>
                    <div class="flex gap-3">
                        {{-- <button type="button" onclick="closeSlideover('{{ $slideoverId }}')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button> --}}
                        <x-bladewind::button size="small" color="gray" outline="true" onClick="closeSlideover('{{ $slideoverId }}')" >
                            Cancel
                        </x-bladewind::button>
                        {{-- <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            Apply Filters
                        </button> --}}
                        <x-bladewind::button size="small" color="blue" onClick="submitFilters(event, '{{ $slideoverId }}')" >
                            Apply Filters
                        </x-bladewind::button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openSlideover(id) {
        const slideover = document.getElementById(id);
        const panel = document.getElementById(id + '-panel');
        
        slideover.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Trigger animation
        setTimeout(() => {
            panel.classList.remove('translate-x-full');
        }, 10);
    }
    
    function closeSlideover(id) {
        const slideover = document.getElementById(id);
        const panel = document.getElementById(id + '-panel');
        
        panel.classList.add('translate-x-full');
        document.body.style.overflow = '';
        
        setTimeout(() => {
            slideover.classList.add('hidden');
        }, 300);
    }
    
    function submitFilters(event, id) {
        event.preventDefault();
        
        const form = document.getElementById(id + '-form');
        const formData = new FormData(form);
        const url = new URL(window.location);
        
        // Clear existing filter parameters (except page and per_page)
        const paramsToKeep = ['page', 'per_page'];
        const currentParams = new URLSearchParams(url.search);
        
        // Start with a clean URL
        url.search = '';
        
        // Re-add parameters we want to keep
        paramsToKeep.forEach(param => {
            if (currentParams.has(param)) {
                url.searchParams.set(param, currentParams.get(param));
            }
        });
        
        // Add form data, but only if it has a value
        for (let [key, value] of formData.entries()) {
            if (value && value.trim() !== '') {
                url.searchParams.set(key, value);
            }
        }
        
        // Reset to first page when applying filters
        url.searchParams.delete('page');
        
        // Navigate to the new URL
        window.location.href = url.toString();
        
        return false;
    }
    
    function clearFilters(id) {
        const form = document.getElementById(id + '-form');
        
        // Clear all inputs except search
        const inputs = form.querySelectorAll('input:not([name="search"]), select, textarea');
        inputs.forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });
        
        // Navigate to clean URL (keeping only search if it exists)
        const url = new URL(window.location);
        const search = url.searchParams.get('search');
        url.search = '';
        if (search) {
            url.searchParams.set('search', search);
        }
        window.location.href = url.toString();
    }
</script>
