@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
$classes = match($type) {
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error', 'danger' => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    default => 'bg-gray-50 border-gray-200 text-gray-800',
};

$icon = match($type) {
    'success' => 'fas fa-check-circle',
    'error', 'danger' => 'fas fa-exclamation-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'info' => 'fas fa-info-circle',
    default => 'fas fa-bell',
};
@endphp

<div {{ $attributes->merge(['class' => "border rounded-lg px-4 py-3 mb-6 flex items-start {$classes}"]) }} role="alert">
    <i class="{{ $icon }} mt-0.5 mr-3"></i>
    <div class="flex-1">
        {{ $slot }}
    </div>
    @if($dismissible)
    <button type="button" class="ml-3 -mr-1 flex-shrink-0" onclick="this.parentElement.remove()">
        <i class="fas fa-times text-sm opacity-50 hover:opacity-100"></i>
    </button>
    @endif
</div>
