@props([
    'title' => 'Recent Activity',
    'activities' => [],
])

<div class="bg-white rounded-lg shadow">
    <div class="px-4 py-3 border-b border-gray-200">
        <h2 class="text-sm font-semibold text-gray-900">{{ $title }}</h2>
    </div>
    <div class="p-4">
        @if(empty($activities))
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-3xl mb-3"></i>
                <p class="text-sm">No recent activity</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($activities as $activity)
                    <div class="flex items-start space-x-2.5">
                        <div class="flex-shrink-0">
                            <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="{{ $activity['icon'] ?? 'fas fa-circle' }} text-blue-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['description'] ?? '' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $activity['time'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
