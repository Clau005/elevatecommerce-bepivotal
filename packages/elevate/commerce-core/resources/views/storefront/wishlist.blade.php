@extends('commerce::storefront.layouts.commerce')

@section('title', 'My Wishlist')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Wishlist</h1>
            <p class="mt-2 text-sm text-gray-600">Save items for later or move them to your cart</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($wishlist && $wishlist->lines->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Wishlist Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Wishlist Items ({{ $wishlist->lines->count() }})</h2>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            @foreach($wishlist->lines as $line)
                                <div class="px-6 py-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-md flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        
                                        <div class="ml-6 flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h3 class="text-base font-medium text-gray-900">{{ $line->name }}</h3>
                                                    <p class="mt-1 text-sm text-gray-600">{{ $line->description }}</p>
                                                    <p class="mt-1 text-xs text-gray-500">{{ class_basename($line->purchasable_type) }}</p>
                                                    
                                                    @if(!$line->is_available)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                                                            Currently Unavailable
                                                        </span>
                                                    @endif
                                                    
                                                    @if($line->meta && count($line->meta) > 0)
                                                        <div class="mt-2">
                                                            @foreach($line->meta as $key => $value)
                                                                @if(in_array($key, ['sku']))
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                                        {{ $key }}: {{ $value }}
                                                                    </span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="text-right">
                                                    <p class="text-base font-medium text-gray-900">£{{ number_format($line->unit_price / 100, 2) }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    @if($line->is_available)
                                                        <form action="{{ route('storefront.wishlist.move-to-cart', [$line->purchasable_type, $line->purchasable_id]) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L4 5H2m5 8h10m0 0l1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                                                </svg>
                                                                Move to Cart
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button disabled class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L4 5H2m5 8h10m0 0l1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                                            </svg>
                                                            Unavailable
                                                        </button>
                                                    @endif
                                                    
                                                    <form action="{{ route('storefront.wishlist.remove', [$line->purchasable_type, $line->purchasable_id]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm text-red-600 hover:text-red-500">Remove</button>
                                                    </form>
                                                </div>
                                                
                                                <div class="text-xs text-gray-500">
                                                    Added {{ $line->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Wishlist Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Wishlist Summary</h2>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Items</span>
                                <span class="text-gray-900">{{ $totals['item_count'] }}</span>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Value</span>
                                <span class="text-gray-900">{{ $totals['formatted']['total_value'] }}</span>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Available Items</span>
                                <span class="text-gray-900">{{ collect($totals['items'])->where('is_available', true)->count() }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-6 space-y-3">
                            <a href="{{ route('storefront.cart.index') }}" 
                               class="w-full bg-blue-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-center block">
                                View Cart
                            </a>
                            
                            <a href="/" 
                               class="w-full bg-white border border-gray-300 rounded-md shadow-sm py-3 px-4 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-center block">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="mt-6 bg-white rounded-lg shadow p-6">
                        <h3 class="text-base font-medium text-gray-900 mb-4">Quick Actions</h3>
                        
                        <div class="space-y-3">
                            @if(collect($totals['items'])->where('is_available', true)->count() > 0)
                                <form action="{{ route('storefront.wishlist.move-all-to-cart') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Move All Available to Cart
                                    </button>
                                </form>
                            @endif
                            
                            <button onclick="if(confirm('Are you sure you want to clear your entire wishlist?')) { document.getElementById('clear-wishlist-form').submit(); }" 
                                    class="w-full bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Clear Wishlist
                            </button>
                            
                            <form id="clear-wishlist-form" action="{{ route('storefront.wishlist.clear') }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Wishlist -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Your wishlist is empty</h3>
                    <p class="mt-1 text-sm text-gray-500">Save items you love for later by adding them to your wishlist.</p>
                    <div class="mt-6">
                        <a href="{{ route('storefront.shop') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Start Shopping
                        </a>
                    </div>
                </div>
            </div>
        @endif
</div>
@endsection
