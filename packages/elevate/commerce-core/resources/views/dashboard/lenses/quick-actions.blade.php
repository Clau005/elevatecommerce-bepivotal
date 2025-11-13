<div class="grid grid-cols-1 gap-3">
    @foreach($actions as $action)
        @php
            $colorClasses = [
                'blue' => 'bg-blue-50 text-blue-700 hover:bg-blue-100',
                'green' => 'bg-green-50 text-green-700 hover:bg-green-100',
                'purple' => 'bg-purple-50 text-purple-700 hover:bg-purple-100',
                'indigo' => 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
            ];
            $colorClass = $colorClasses[$action['color']] ?? 'bg-gray-50 text-gray-700 hover:bg-gray-100';
        @endphp
        
        <a href="{{ $action['url'] }}" class="flex items-center gap-3 p-3 rounded-lg {{ $colorClass }} transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
            </svg>
            <span class="font-medium text-sm">{{ $action['label'] }}</span>
        </a>
    @endforeach
</div>
