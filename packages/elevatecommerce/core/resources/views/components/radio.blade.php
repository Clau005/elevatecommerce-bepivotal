@props([
    'label' => null,
    'description' => null,
])

<div class="flex items-start">
    <div class="flex items-center h-5">
        <input 
            {{ $attributes->merge([
                'type' => 'radio',
                'class' => 'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300'
            ]) }}
        >
    </div>
    @if($label || $description)
        <div class="ml-3">
            @if($label)
                <label for="{{ $attributes->get('id') }}" class="text-xs font-medium text-gray-700">
                    {{ $label }}
                </label>
            @endif
            @if($description)
                <p class="text-xs text-gray-500">{{ $description }}</p>
            @endif
        </div>
    @endif
</div>
