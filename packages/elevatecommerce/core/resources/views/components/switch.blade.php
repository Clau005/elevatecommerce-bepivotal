@props([
    'label' => null,
    'description' => null,
])

<div class="flex items-center justify-between">
    @if($label || $description)
        <div class="flex-1">
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
    
    <button 
        type="button"
        role="switch"
        {{ $attributes->except(['checked'])->merge([
            'class' => 'relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 ' . ($attributes->get('checked') ? 'bg-blue-600' : 'bg-gray-200')
        ]) }}
        x-data="{ checked: {{ $attributes->get('checked') ? 'true' : 'false' }} }"
        @click="checked = !checked"
        :class="{ 'bg-blue-600': checked, 'bg-gray-200': !checked }"
    >
        <span 
            class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
            :class="{ 'translate-x-4': checked, 'translate-x-0': !checked }"
        ></span>
    </button>
</div>
