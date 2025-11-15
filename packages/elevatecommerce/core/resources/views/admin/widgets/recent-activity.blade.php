@props([
    'title' => 'Recent Activity',
    'activities' => [],
])

<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
    </div>
    <div class="p-6">
        @if(empty($activities))
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <p>No recent activity</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($activities as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="{{ $activity['icon'] ?? 'fas fa-circle' }} text-blue-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-sm text-gray-500">{{ $activity['description'] ?? '' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $activity['time'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
