<x-app pageTitle="Notification History" title="Notification History - Admin" description="View all sent notifications">

<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Notification History</h1>
        <p class="mt-2 text-gray-600">View all sent notifications</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($notifications->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($notifications as $notification)
                @php
                    $data = json_decode($notification->data, true);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $notification->type }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $notification->notifiable_type }} #{{ $notification->notifiable_id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $data['subject'] ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($notification->read_at)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Read
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Unread
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
            <p class="text-gray-500">Notifications will appear here once they are sent</p>
        </div>
        @endif
    </div>
</div>

</x-app>
