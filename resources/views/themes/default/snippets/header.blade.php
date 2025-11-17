<header class="border-b border-gray-200 px-8 py-4" style="background-color: {{ $background_color ?? '#ffffff' }};">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo -->
        <div class="text-2xl font-bold text-gray-900">
            <a href="/">{{ $site_name ?? config('app.name', 'Store') }}</a>
        </div>
        
        <!-- Navigation -->
        <nav class="flex gap-8">
            <a href="/" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">Home</a>
            <a href="/catalog" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">Catalog</a>
            <a href="/about" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">About</a>
            <a href="/contact" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">Contact</a>
        </nav>
        
        <!-- Cart Icon -->
        <div class="flex flex-row items-center space-x-2">
            <a href="/wishlist" class="text-gray-900 hover:text-blue-600 transition-colors">
                 Wishlist
            </a>
            <a href="/cart" class="text-gray-900 hover:text-blue-600 transition-colors">
                🛒 Cart
            </a>
        </div>
    </div>
</header>
