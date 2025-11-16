@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
])

@php
    $inputId = $attributes->get('id', 'richtext_' . uniqid());
    $trixId = $inputId . '_trix';
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $inputId }}" class="block text-xs font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input 
        id="{{ $inputId }}" 
        type="hidden" 
        {{ $attributes->except(['class', 'id'])->merge([
            'name' => $attributes->get('name', 'content')
        ]) }}
        value="{{ $slot }}"
    >
    
    <trix-editor 
        input="{{ $inputId }}"
        id="{{ $trixId }}"
        class="trix-content w-full border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $error ? 'border-red-300' : 'border-gray-300' }}"
    ></trix-editor>
    
    @if($hint && !$error)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="mt-0.5 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>

@once
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.10/dist/trix.css">
    <style>
        trix-toolbar .trix-button-group {
            margin-bottom: 0;
        }
        trix-editor {
            min-height: 150px;
            max-height: 500px;
            overflow-y: auto;
        }
        trix-editor:empty:not(:focus)::before {
            color: #9ca3af;
        }
        .trix-content {
            background: white;
        }
        .trix-content:focus-within {
            border-color: #3b82f6;
            ring: 2px;
            ring-color: #3b82f6;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/trix@2.0.10/dist/trix.umd.min.js"></script>
    @endpush
@endonce
