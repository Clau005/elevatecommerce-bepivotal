<div>
    <div class="text-sm font-medium text-gray-600">{{ $title }}</div>
    <div class="mt-2 flex items-baseline justify-between">
        <div class="text-3xl font-bold text-gray-900">{{ $value }}</div>
        
        @if($change)
            @php
                $colorClass = match($changeType) {
                    'increase' => 'text-green-600',
                    'decrease' => 'text-red-600',
                    default => 'text-gray-600',
                };
                
                $iconPath = match($changeType) {
                    'increase' => 'M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z',
                    'decrease' => 'M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z',
                    default => '',
                };
            @endphp
            
            <div class="flex items-center gap-1 {{ $colorClass }} text-sm font-medium">
                @if($iconPath)
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="{{ $iconPath }}" clip-rule="evenodd"/>
                    </svg>
                @endif
                <span>{{ $change }}</span>
            </div>
        @endif
    </div>
</div>
