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
        <button class="relative p-2 text-blue-200 hover:text-white hover:bg-blue-800 rounded-lg">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- User Menu -->
        <div class="relative">
            <button class="flex items-center space-x-3 p-2 hover:bg-blue-800 rounded-lg">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr(auth('admin')->user()->first_name, 0, 1)) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-white">
                    {{ auth('admin')->user()->name }}
                </span>
                <i class="fas fa-chevron-down text-xs text-blue-300"></i>
            </button>
        </div>
    </div>
</header>
