<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Account') - {{ config('app.name') }}</title>
    @vite(['packages/elevatecommerce/core/resources/css/admin.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900">
                        {{ config('app.name') }}
                    </a>
                </div>
                <nav class="flex items-center space-x-4">
                    <a href="/" class="text-sm text-gray-600 hover:text-gray-900">
                        <i class="fas fa-home mr-1"></i> Store
                    </a>
                    <form action="{{ route('account.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar -->
            <aside class="lg:w-64 flex-shrink-0">
                <nav class="bg-white rounded-lg shadow-sm p-3 space-y-1">
                    <a href="{{ route('account.dashboard') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-home w-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('purchasable.cart.index') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchasable.cart.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-shopping-cart w-5 mr-3"></i>
                        Shopping Cart
                    </a>
                    <a href="{{ route('purchasable.wishlist.index') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('purchasable.wishlist.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-heart w-5 mr-3"></i>
                        Wishlist
                    </a>
                    <a href="{{ route('account.orders') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.orders') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-shopping-bag w-5 mr-3"></i>
                        Orders
                    </a>
                    <a href="{{ route('account.addresses') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.addresses') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-map-marker-alt w-5 mr-3"></i>
                        Addresses
                    </a>
                    <a href="{{ route('account.profile') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('account.profile') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-user w-5 mr-3"></i>
                        Profile
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 min-w-0">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
