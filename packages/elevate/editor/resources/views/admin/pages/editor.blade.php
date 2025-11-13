<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Visual Editor - {{ $page->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="h-screen flex flex-col">
    {{-- Editor Header --}}
    <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.pages.edit', $page) }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">{{ $page->title }}</h1>
                <p class="text-sm text-gray-500">Visual Editor</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Device Preview Toggle --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                <button type="button" class="device-toggle px-3 py-1 text-sm rounded active" data-device="desktop">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </button>
                <button type="button" class="device-toggle px-3 py-1 text-sm rounded" data-device="tablet">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </button>
                <button type="button" class="device-toggle px-3 py-1 text-sm rounded" data-device="mobile">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>

            {{-- Save Draft --}}
            <button type="button" id="save-draft" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Save Draft
            </button>

            {{-- Publish --}}
            <button type="button" id="publish" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Publish
            </button>
        </div>
    </div>

    {{-- Editor Content --}}
    <div class="flex-1 flex overflow-hidden">
        {{-- Sections Panel --}}
        <div class="w-80 bg-white border-r border-gray-200 overflow-y-auto">
            <div class="p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Available Sections</h3>
                
                <div id="available-sections" class="space-y-2">
                    @foreach($availableSections as $section)
                        <div class="section-item p-3 bg-gray-50 rounded-lg cursor-move hover:bg-gray-100 border border-gray-200"
                             data-section-type="{{ $section['type'] }}"
                             draggable="true">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $section['name'] }}</p>
                                    @if(isset($section['description']))
                                        <p class="text-xs text-gray-500">{{ $section['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Preview Area --}}
        <div class="flex-1 bg-gray-100 overflow-auto">
            <div id="preview-container" class="preview-desktop mx-auto bg-white shadow-lg" style="min-height: 100%;">
                <iframe id="preview-frame" 
                        src="/{{ $page->slug }}?preview=1" 
                        class="w-full h-full border-0"
                        style="min-height: 800px;">
                </iframe>
            </div>
        </div>

        {{-- Settings Panel --}}
        <div class="w-80 bg-white border-l border-gray-200 overflow-y-auto">
            <div class="p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Section Settings</h3>
                
                <div id="section-settings" class="space-y-4">
                    <p class="text-sm text-gray-500">Select a section to edit its settings</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Page configuration
let pageConfiguration = @json($configuration ?? []);
let pageId = {{ $page->id }};

// Device toggle
document.querySelectorAll('.device-toggle').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('.device-toggle').forEach(b => b.classList.remove('active', 'bg-blue-600', 'text-white'));
        this.classList.add('active', 'bg-blue-600', 'text-white');
        
        const device = this.dataset.device;
        const container = document.getElementById('preview-container');
        container.className = 'preview-' + device + ' mx-auto bg-white shadow-lg';
    });
});

// Drag and drop sections
const availableSections = document.getElementById('available-sections');
const previewFrame = document.getElementById('preview-frame');

availableSections.addEventListener('dragstart', function(e) {
    if (e.target.classList.contains('section-item')) {
        e.dataTransfer.effectAllowed = 'copy';
        e.dataTransfer.setData('text/plain', e.target.dataset.sectionType);
    }
});

// Save draft
document.getElementById('save-draft').addEventListener('click', async function() {
    const button = this;
    button.disabled = true;
    button.textContent = 'Saving...';
    
    try {
        const response = await fetch('{{ route('api.editor.pages.save-draft', $page) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                configuration: pageConfiguration
            })
        });
        
        if (response.ok) {
            button.textContent = 'Saved!';
            setTimeout(() => {
                button.textContent = 'Save Draft';
                button.disabled = false;
            }, 2000);
        }
    } catch (error) {
        console.error('Save failed:', error);
        button.textContent = 'Save Failed';
        button.disabled = false;
    }
});

// Publish
document.getElementById('publish').addEventListener('click', async function() {
    if (!confirm('Are you sure you want to publish this page?')) return;
    
    const button = this;
    button.disabled = true;
    button.textContent = 'Publishing...';
    
    try {
        const response = await fetch('{{ route('admin.pages.publish', $page) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                configuration: pageConfiguration
            })
        });
        
        if (response.ok) {
            window.location.href = '{{ route('admin.pages.index') }}';
        }
    } catch (error) {
        console.error('Publish failed:', error);
        button.textContent = 'Publish Failed';
        button.disabled = false;
    }
});

// Auto-save every 30 seconds
setInterval(() => {
    document.getElementById('save-draft').click();
}, 30000);
</script>

<style>
.preview-desktop { max-width: 100%; }
.preview-tablet { max-width: 768px; }
.preview-mobile { max-width: 375px; }

.device-toggle.active {
    @apply bg-blue-600 text-white;
}
</style>

</body>
</html>
