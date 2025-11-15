@extends('core::admin.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="mt-1 text-sm text-gray-600">View and manage your notifications</p>
        </div>
        @if(auth('admin')->user()->unreadNotifications->count() > 0)
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button 
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
                >
                    <i class="fas fa-check-double mr-2"></i>
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow">
        @forelse(auth('admin')->user()->notifications()->paginate(20) as $notification)
            <div class="p-6 border-b border-gray-200 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-10 h-10 {{ $notification->read_at ? 'bg-gray-100' : 'bg-blue-100' }} rounded-full flex items-center justify-center">
                            <i class="fas fa-bell {{ $notification->read_at ? 'text-gray-600' : 'text-blue-600' }}"></i>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <h3 class="text-sm font-semibold text-gray-900">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h3>
                                @if(!$notification->read_at)
                                    <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-2">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2 ml-4">
                        @if(!$notification->read_at)
                            <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST">
                                @csrf
                                <button 
                                    type="submit"
                                    class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50"
                                    title="Mark as read"
                                >
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.notifications.delete', $notification->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit"
                                class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50"
                                title="Delete"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                <p class="text-sm text-gray-600">You're all caught up!</p>
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
