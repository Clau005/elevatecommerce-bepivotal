@props([
    'title' => 'Stat',
    'value' => '0',
    'icon' => 'fas fa-chart-line',
    'iconBg' => 'bg-blue-100',
    'iconColor' => 'text-blue-600',
    'change' => null,
    'changeLabel' => 'from last month',
])

<div class="bg-white rounded-lg shadow p-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-600">{{ $title }}</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $value }}</p>
        </div>
        <div class="p-2 {{ $iconBg }} rounded-lg">
            <i class="{{ $icon }} text-lg {{ $iconColor }}"></i>
        </div>
    </div>
    @if($change !== null)
        <div class="mt-3 flex items-center text-xs">
            <span class="{{ $change >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                {{ $change >= 0 ? '+' : '' }}{{ $change }}%
            </span>
            <span class="ml-2 text-gray-600">{{ $changeLabel }}</span>
        </div>
    @endif
</div>
