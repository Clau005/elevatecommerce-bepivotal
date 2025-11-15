@extends('core::admin.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <x-core::heading level="1" subtitle="View and manage your notifications">
            Notifications
        </x-core::heading>
        @if(auth('admin')->user()->unreadNotifications->count() > 0)
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                @csrf
                <x-core::button type="submit" variant="primary" icon="fas fa-check-double">
                    Mark All as Read
                </x-core::button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow">
        @forelse(auth('admin')->user()->notifications()->paginate(20) as $notification)
            <div class="p-3 border-b border-gray-200 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3 flex-1">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-8 h-8 {{ $notification->read_at ? 'bg-gray-100' : 'bg-blue-100' }} rounded-full flex items-center justify-center">
                            <i class="fas fa-bell text-xs {{ $notification->read_at ? 'text-gray-600' : 'text-blue-600' }}"></i>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-1.5 mb-0.5">
                                <h3 class="text-xs font-semibold text-gray-900">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h3>
                                @if(!$notification->read_at)
                                    <span class="inline-block w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-1 ml-3">
                        @if(!$notification->read_at)
                            <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST">
                                @csrf
                                <button 
                                    type="submit"
                                    class="p-1 text-gray-400 hover:text-blue-600 rounded hover:bg-blue-50"
                                    title="Mark as read"
                                >
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.notifications.delete', $notification->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit"
                                class="p-1 text-gray-400 hover:text-red-600 rounded hover:bg-red-50"
                                title="Delete"
                            >
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center">
                <i class="fas fa-bell-slash text-3xl text-gray-400 mb-3"></i>
                <h3 class="text-sm font-medium text-gray-900 mb-1">No notifications</h3>
                <p class="text-xs text-gray-600">You're all caught up!</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(auth('admin')->user()->notifications()->count() > 20)
        <div class="flex justify-center">
            {{ auth('admin')->user()->notifications()->paginate(20)->links() }}
        </div>
    @endif
</div>
@endsection
