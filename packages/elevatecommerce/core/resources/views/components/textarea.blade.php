@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'rows' => 3,
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block text-xs font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <textarea 
        {{ $attributes->except('class')->merge([
            'rows' => $rows,
            'class' => 'w-full px-3 py-1.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500 ' . ($error ? 'border-red-300' : 'border-gray-300')
        ]) }}
    >{{ $slot }}</textarea>
    
    @if($hint && !$error)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="mt-0.5 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
