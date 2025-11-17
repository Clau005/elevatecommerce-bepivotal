<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - ElevateCommerce</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-8">
                    <h1 class="text-2xl font-bold text-gray-900">ElevateCommerce</h1>
                    <nav class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-gray-900 font-medium">Shop</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900">Categories</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900">Deals</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('purchasable.wishlist.index') }}" class="text-gray-600 hover:text-gray-900 relative">
                        <i class="far fa-heart text-xl"></i>
                        <span class="sr-only">Wishlist</span>
                    </a>
                    <a href="{{ route('purchasable.cart.index') }}" class="text-gray-600 hover:text-gray-900 relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="sr-only">Cart</span>
                    </a>
                    <a href="{{ route('admin.login') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-user-shield text-xl"></i>
                        <span class="sr-only">Admin</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
            @endif

            <!-- Page Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Featured Products</h1>
                <p class="text-gray-600">Discover our latest collection</p>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-200 group">
                    <!-- Product Image -->
                    <div class="relative aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                        @else
                            <i class="fas fa-box text-gray-400 text-6xl"></i>
                        @endif

                        <!-- Stock Badge -->
                        @if($product->isInStock())
                            <span class="absolute top-2 right-2 bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded">
                                In Stock
                            </span>
                        @else
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">
                                Out of Stock
                            </span>
                        @endif

                        <!-- Discount Badge -->
                        @if($product->compare_at_price && $product->compare_at_price > $product->price)
                            @php
                                $discount = round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100);
                            @endphp
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">
                                -{{ $discount }}%
                            </span>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        <p class="text-xs text-gray-500 mb-2">SKU: {{ $product->sku }}</p>

                        @if($product->description)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                        @endif

                        <!-- Price -->
                        <div class="mb-4">
                            <div class="flex items-baseline space-x-2">
                                <span class="text-xl font-bold text-gray-900">@currency($product->price)</span>
                                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <span class="text-sm text-gray-400 line-through">@currency($product->compare_at_price)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-2">
                            @if($product->canPurchase())
                                <form action="{{ route('purchasable.cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="purchasable_type" value="App\Models\TestingPurchasable">
                                    <input type="hidden" name="purchasable_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Add to Cart
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded-lg cursor-not-allowed">
                                    <i class="fas fa-ban mr-2"></i>
                                    Unavailable
                                </button>
                            @endif

                            <form action="{{ route('purchasable.wishlist.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="purchasable_type" value="App\Models\TestingPurchasable">
                                <input type="hidden" name="purchasable_id" value="{{ $product->id }}">
                                <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    <i class="far fa-heart mr-2"></i>
                                    Add to Wishlist
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>