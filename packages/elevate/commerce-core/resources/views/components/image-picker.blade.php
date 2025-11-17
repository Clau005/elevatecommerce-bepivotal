@props(['name', 'value' => '', 'label' => 'Image', 'required' => false])

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>
    
    <div class="space-y-3">
        {{-- Preview --}}
        <div id="{{ $name }}_preview" class="{{ $value ? '' : 'hidden' }}">
            <div class="relative inline-block">
                <img id="{{ $name }}_preview_img" src="{{ $value }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                <button type="button" onclick="clearImage_{{ $name }}()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Hidden Input --}}
        <input type="hidden" name="{{ $name }}" id="{{ $name }}_input" value="{{ $value }}">

        {{-- Picker Button --}}
        <button type="button" onclick="openMediaPicker_{{ $name }}()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Choose from Media Library
        </button>
    </div>
</div>

{{-- Media Picker Modal --}}
<div id="{{ $name }}_modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border w-full max-w-5xl shadow-lg rounded-lg bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Select Image</h3>
            <button type="button" onclick="closeMediaPicker_{{ $name }}()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <input type="text" id="{{ $name }}_search" placeholder="Search images..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>

        {{-- Media Grid --}}
        <div id="{{ $name }}_grid" class="grid grid-cols-4 gap-4 max-h-96 overflow-y-auto mb-4">
            <div class="col-span-4 text-center py-8 text-gray-500">Loading...</div>
        </div>

        <div class="flex justify-end">
            <button type="button" onclick="closeMediaPicker_{{ $name }}()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    function openMediaPicker_{{ $name }}() {
        document.getElementById('{{ $name }}_modal').classList.remove('hidden');
        loadMedia_{{ $name }}();
    }

    function closeMediaPicker_{{ $name }}() {
        document.getElementById('{{ $name }}_modal').classList.add('hidden');
    }

    function clearImage_{{ $name }}() {
        document.getElementById('{{ $name }}_input').value = '';
        document.getElementById('{{ $name }}_preview').classList.add('hidden');
    }

    function selectImage_{{ $name }}(url) {
        document.getElementById('{{ $name }}_input').value = url;
        document.getElementById('{{ $name }}_preview_img').src = url;
        document.getElementById('{{ $name }}_preview').classList.remove('hidden');
        closeMediaPicker_{{ $name }}();
    }

    async function loadMedia_{{ $name }}(search = '') {
        const grid = document.getElementById('{{ $name }}_grid');
        grid.innerHTML = '<div class="col-span-4 text-center py-8 text-gray-500">Loading...</div>';

        try {
            const response = await fetch(`/admin/media?type=images&search=${search}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            if (data.data && data.data.length > 0) {
                grid.innerHTML = data.data.map(media => `
                    <div class="relative group cursor-pointer border-2 border-transparent hover:border-blue-500 rounded-lg overflow-hidden" onclick="selectImage_{{ $name }}('${media.url}')">
                        <img src="${media.url}" alt="${media.original_filename || media.filename}" class="w-full h-32 object-cover">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 truncate">
                            ${media.original_filename || media.filename}
                        </div>
                    </div>
                `).join('');
            } else {
                grid.innerHTML = '<div class="col-span-4 text-center py-8 text-gray-500">No images found</div>';
            }
        } catch (error) {
            console.error('Error loading media:', error);
            grid.innerHTML = '<div class="col-span-4 text-center py-8 text-red-500">Error loading images. Check console for details.</div>';
        }
    }

    // Search functionality
    document.getElementById('{{ $name }}_search')?.addEventListener('input', function(e) {
        loadMedia_{{ $name }}(e.target.value);
    });

    // Close modal on outside click
    document.getElementById('{{ $name }}_modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMediaPicker_{{ $name }}();
        }
    });
</script>
