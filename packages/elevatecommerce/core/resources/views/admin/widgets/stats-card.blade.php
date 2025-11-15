@props([
    'title' => 'Stat',
    'value' => '0',
    'icon' => 'fas fa-chart-line',
    'iconBg' => 'bg-blue-100',
    'iconColor' => 'text-blue-600',
    'change' => null,
    'changeLabel' => 'from last month',
])

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $value }}</p>
        </div>
        <div class="p-3 {{ $iconBg }} rounded-lg">
            <i class="{{ $icon }} text-2xl {{ $iconColor }}"></i>
        </div>
    </div>
    @if($change !== null)
        <div class="mt-4 flex items-center text-sm">
            <span class="{{ $change >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                {{ $change >= 0 ? '+' : '' }}{{ $change }}%
            </span>
            <span class="ml-2 text-gray-600">{{ $changeLabel }}</span>
        </div>
    @endif
</div>
