@props([
    'variant' => 'default', // default, success, warning, danger, info
    'size' => 'md', // sm, md
])

@php
    $baseClasses = 'inline-flex items-center font-medium rounded-full';
    
    $sizeClasses = [
        'sm' => 'px-1.5 py-0.5 text-xs',
        'md' => 'px-2 py-0.5 text-xs',
    ];
    
    $variantClasses = [
        'default' => 'bg-gray-100 text-gray-800',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
    ];
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size] . ' ' . $variantClasses[$variant];
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
