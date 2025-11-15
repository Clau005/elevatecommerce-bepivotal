@props([
    'title' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow']) }}>
    @if($title)
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-900">{{ $title }}</h2>
        </div>
    @endif
    
    <div class="{{ $padding ? 'p-4' : '' }}">
        {{ $slot }}
    </div>
</div>
