@props([
    'level' => 1, // 1, 2, 3
    'subtitle' => null,
])

<div>
    @if($level == 1)
        <h1 {{ $attributes->merge(['class' => 'text-xl font-bold text-gray-900']) }}>{{ $slot }}</h1>
    @elseif($level == 2)
        <h2 {{ $attributes->merge(['class' => 'text-sm font-semibold text-gray-900']) }}>{{ $slot }}</h2>
    @else
        <h3 {{ $attributes->merge(['class' => 'text-xs font-semibold text-gray-900']) }}>{{ $slot }}</h3>
    @endif
    
    @if($subtitle)
        <p class="mt-0.5 text-xs text-gray-600">{{ $subtitle }}</p>
    @endif
</div>
