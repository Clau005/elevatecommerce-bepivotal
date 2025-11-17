<header class="bg-slate-900 border-b border-slate-800 h-16 flex items-center px-6 justify-between fixed top-0 left-0 right-0 z-20">
    <!-- Left: Logo & Search -->
    <div class="flex items-center space-x-6 flex-1">
        <!-- Logo -->
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-slate-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-store text-white text-sm"></i>
            </div>
            <span class="text-lg font-semibold text-white">{{ config('app.name') }}</span>
        </div>

        <!-- Search -->
        <div class="flex-1 max-w-2xl">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-800"></i>
            </div>
            <input 
                type="text" 
                placeholder="Search" 
                class="w-full pl-10 pr-4 py-2 bg-slate-300 border border-slate-700 text-black placeholder-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent"
            >
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <kbd class="hidden sm:inline-block px-2 py-1 text-xs font-semibold text-slate-300 bg-slate-800 border border-slate-700 rounded">⌘K</kbd>
            </div>
        </div>
        </div>
    </div>

    <!-- Right: Notifications & User -->
    <div class="flex items-center space-x-4 ml-6">
        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button 
                @click="open = !open"
                class="relative p-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-lg"
            >
                <i class="fas fa-bell text-lg"></i>
                @if(auth('admin')->user()->unreadNotifications->count() > 0)
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </button>

            <!-- Notifications Dropdown -->
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                style="display: none;"
            >
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @forelse(auth('admin')->user()->unreadNotifications->take(5) as $notification)
                        <a 
                            href="{{ route('admin.notifications.show', $notification->id) }}"
                            class="block p-4 hover:bg-gray-50 border-b border-gray-100"
                        >
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-bell text-blue-600 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </p>
                                    <p class="text-sm text-gray-600 truncate">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <i class="fas fa-bell-slash text-3xl mb-2"></i>
                            <p class="text-sm">No new notifications</p>
                        </div>
                    @endforelse
                </div>
                @if(auth('admin')->user()->unreadNotifications->count() > 0)
                    <div class="p-3 border-t border-gray-200 text-center">
                        <a 
                            href="{{ route('admin.notifications.index') }}"
                            class="text-sm font-medium text-blue-600 hover:text-blue-700"
                        >
                            View All Notifications
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- User Menu -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button 
                @click="open = !open"
                class="flex items-center space-x-3 p-2 hover:bg-blue-800 rounded-lg"
            >
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr(auth('admin')->user()->first_name, 0, 1)) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-white">
                    {{ auth('admin')->user()->name }}
                </span>
                <i class="fas fa-chevron-down text-xs text-blue-300"></i>
            </button>

            <!-- User Dropdown -->
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                style="display: none;"
            >
                <div class="p-4 border-b border-gray-200">
                    <p class="text-sm font-medium text-gray-900">{{ auth('admin')->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ auth('admin')->user()->email }}</p>
                    @if(auth('admin')->user()->is_super_admin)
                        <span class="inline-block mt-2 px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">
                            Super Admin
                        </span>
                    @endif
                </div>
                <div class="py-2">
                    <a 
                        href="{{ route('admin.profile') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        <i class="fas fa-user w-5 mr-3"></i>
                        Profile
                    </a>
                    <a 
                        href="{{ route('admin.settings.index') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        <i class="fas fa-cog w-5 mr-3"></i>
                        Settings
                    </a>
                </div>
                <div class="border-t border-gray-200 py-2">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button 
                            type="submit"
                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                        >
                            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
