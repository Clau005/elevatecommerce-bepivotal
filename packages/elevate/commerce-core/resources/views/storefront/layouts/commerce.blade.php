<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop') - {{ config('app.name') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --color-navy: #1e3a8a;
            --color-navy-dark: #1e40af;
            --color-navy-light: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Simple Header --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-gray-900">
                        {{ config('app.name') }}
                    </a>
                </div>

                {{-- Navigation --}}
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/shop" class="text-gray-700 hover:text-blue-800 font-medium">Shop</a>
                    
                    @auth
                        <a href="{{ route('storefront.wishlist.index') }}" class="text-gray-700 hover:text-blue-800 font-medium flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Wishlist
                        </a>
                        <a href="{{ route('storefront.account') }}" class="text-gray-700 hover:text-blue-800 font-medium">Account</a>
                    @else
                        <a href="{{ route('storefront.login') }}" class="text-gray-700 hover:text-blue-800 font-medium">Login</a>
                    @endauth
                    
                    <a href="{{ route('storefront.cart.index') }}" class="relative text-gray-700 hover:text-blue-800 font-medium flex items-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="ml-1">Cart</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-grow py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">{{ config('app.name') }}</h3>
                    <p class="text-gray-400 text-sm">Your trusted online store</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Shop</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/shop" class="hover:text-white">All Products</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @auth
                            <li><a href="{{ route('storefront.account') }}" class="hover:text-white">My Account</a></li>
                            <li><a href="{{ route('storefront.account.orders') }}" class="hover:text-white">Orders</a></li>
                        @else
                            <li><a href="{{ route('storefront.login') }}" class="hover:text-white">Login</a></li>
                            <li><a href="{{ route('storefront.register') }}" class="hover:text-white">Register</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/contact" class="hover:text-white">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
