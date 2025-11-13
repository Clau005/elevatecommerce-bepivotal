<?php

namespace Elevate\CommerceCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\User;
use Elevate\CommerceCore\Models\Order;
use App\Models\Service;
use App\Models\ProductVariant;
use App\Models\Sellable;
use Elevate\CommerceCore\Services\PurchasableService;
use Elevate\CommerceCore\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StorefrontCustomerController extends Controller
{
    protected array $purchasableTypes = [
        'service' => Service::class,
        'services' => Service::class,
        'product' => \Elevate\Product\Models\Product::class,
        'products' => \Elevate\Product\Models\Product::class,
        'watch' => \Elevate\Watches\Models\Watch::class,
        'watches' => \Elevate\Watches\Models\Watch::class,
    ];

    /**
     * Show customer login page.
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('storefront.account');
        }

        return view('commerce::storefront.auth.login');
    }

    /**
     * Handle customer login.
     */
    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Use the 'web' guard explicitly for customer authentication
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Merge guest cart with user cart after login
            if ($request->session()->has('guest_session_id')) {
                $guestSessionId = $request->session()->get('guest_session_id');
                $purchasableService = new PurchasableService();
                $purchasableService->mergeGuestCart($guestSessionId, Auth::guard('web')->id());
                $request->session()->forget('guest_session_id');
            }

            return redirect()->intended(route('storefront.account'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Show customer registration page.
     */
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('storefront.account');
        }

        return view('commerce::storefront.auth.register');
    }

    /**
     * Handle customer registration.
     */
    public function registerPost(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'account_reference' => User::generateAccountReference(),
        ]);

        Auth::guard('web')->login($user);

        // Merge guest cart with new user cart
        if ($request->session()->has('guest_session_id')) {
            $guestSessionId = $request->session()->get('guest_session_id');
            $purchasableService = new PurchasableService();
            $purchasableService->mergeGuestCart($guestSessionId, $user->id);
            $request->session()->forget('guest_session_id');
        }

        return redirect()->route('storefront.account');
    }

    /**
     * Handle customer logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.homepage');
    }

    /**
     * Show customer account dashboard.
     */
    public function account()
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('storefront.login');
        }

        // Get customer statistics
        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->where('status', 'completed')->sum('total') / 100,
            'recent_orders' => $user->orders()->latest()->take(5)->get(),
            'account_age_days' => $user->created_at->diffInDays(now()),
        ];

        return view('commerce::storefront.account.dashboard', compact('user', 'stats'));
    }

    /**
     * Show customer profile page.
     */
    public function profile()
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('storefront.login');
        }

        return view('commerce::storefront.account.profile', compact('user'));
    }

    /**
     * Update customer profile.
     */
    public function profileUpdate(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('storefront.login');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'company_name' => 'nullable|string|max:255',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The current password is incorrect.'],
                ]);
            }
        }

        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'company_name' => $request->company_name,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('storefront.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show customer orders.
     */
    public function orders()
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('storefront.login');
        }

        $orders = $user->orders()
            ->with(['lines.purchasable', 'addresses'])
            ->latest()
            ->paginate(10);

        return view('commerce::storefront.account.orders', compact('orders'));
    }

    /**
     * Show specific order details.
     */
    public function orderShow($id)
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('storefront.login');
        }

        $order = $user->orders()
            ->with(['lines.purchasable', 'addresses', 'payments', 'timelines'])
            ->findOrFail($id);

        return view('commerce::storefront.account.order-detail', compact('order'));
    }

    /**
     * Show cart page.
     */
    public function cart()
    {
        $purchasableService = new PurchasableService();
        
        \Log::info('Cart page - looking for cart', [
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
        ]);
        
        $cart = $purchasableService->getCart();
        
        \Log::info('Cart page - cart found', [
            'cart' => $cart ? $cart->id : null,
            'lines_count' => $cart ? $cart->lines->count() : 0,
        ]);
        
        $totals = $purchasableService->getCartTotals();

        return view('commerce::storefront.cart', compact('cart', 'totals'));
    }

    /**
     * Add item to cart from storefront.
     */
    public function cartAdd(Request $request)
    {
        \Log::info('CartAdd endpoint hit!', [
            'request_data' => $request->all(),
            'url' => $request->fullUrl(),
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
        ]);

        $request->validate([
            'purchasable_type' => 'required|string',
            'purchasable_id' => 'required|integer',
            'quantity' => 'integer|min:1',
        ]);

        try {
            // Store guest session ID for later cart merging
            if (!Auth::check()) {
                session(['guest_session_id' => session()->getId()]);
            }

            \Log::info('Getting purchasable class', ['type' => $request->purchasable_type]);
            $purchasableClass = $this->getPurchasableClass($request->purchasable_type);
            
            \Log::info('Finding purchasable', ['class' => $purchasableClass, 'id' => $request->purchasable_id]);
            $purchasable = $purchasableClass::findOrFail($request->purchasable_id);

            \Log::info('Adding to cart', ['purchasable' => $purchasable->name ?? 'Unknown']);
            $purchasableService = new PurchasableService();
            $purchasableService->addToCart($purchasable, $request->input('quantity', 1));

            \Log::info('Item added to cart successfully', [
                'product_name' => $purchasable->name ?? 'Unknown',
                'quantity' => $request->input('quantity', 1),
                'session_id_after' => session()->getId(),
            ]);

            // Force session save
            session()->save();

            return redirect()->back()->with('success', 'Item added to cart successfully.');
        } catch (\Exception $e) {
            \Log::error('Error adding to cart', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update cart item quantity.
     */
    public function cartUpdate(Request $request, string $purchasableType, int $purchasableId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $purchasableClass = $this->getPurchasableClass($purchasableType);
            $purchasable = $purchasableClass::findOrFail($purchasableId);

            $purchasableService = new PurchasableService();
            $purchasableService->updateQuantity($purchasable, $request->quantity);

            return redirect()->route('storefront.cart.index')->with('success', 'Cart updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('storefront.cart.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove item from cart.
     */
    public function cartRemove(string $purchasableType, int $purchasableId)
    {
        try {
            $purchasableClass = $this->getPurchasableClass($purchasableType);
            $purchasable = $purchasableClass::findOrFail($purchasableId);

            $purchasableService = new PurchasableService();
            $purchasableService->removeFromCart($purchasable);

            return redirect()->route('storefront.cart.index')->with('success', 'Item removed from cart.');
        } catch (\Exception $e) {
            return redirect()->route('storefront.cart.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Clear entire cart.
     */
    public function cartClear()
    {
        try {
            $purchasableService = new PurchasableService();
            $purchasableService->clearCart();

            return redirect()->route('storefront.cart.index')->with('success', 'Cart cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('storefront.cart.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display the wishlist.
     */
    public function wishlist()
    {
        $purchasableService = new PurchasableService();
        $wishlist = $purchasableService->getWishlist();
        $totals = $purchasableService->getWishlistTotals();

        return view('commerce::storefront.wishlist', compact('wishlist', 'totals'));
    }

    /**
     * Add item to wishlist.
     */
    public function wishlistAdd(Request $request)
    {
        $request->validate([
            'purchasable_type' => 'required|string',
            'purchasable_id' => 'required|integer',
        ]);

        try {
            $purchasableClass = $this->getPurchasableClass($request->purchasable_type);
            $purchasable = $purchasableClass::findOrFail($request->purchasable_id);

            $purchasableService = new PurchasableService();
            $purchasableService->addToWishlist($purchasable);

            return redirect()->back()->with('success', 'Item added to wishlist.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove item from wishlist.
     */
    public function wishlistRemove(string $purchasableType, int $purchasableId)
    {
        try {
            $purchasableClass = $this->getPurchasableClass($purchasableType);
            $purchasable = $purchasableClass::findOrFail($purchasableId);

            $purchasableService = new PurchasableService();
            $purchasableService->removeFromWishlist($purchasable);

            return redirect()->route('storefront.wishlist.index')->with('success', 'Item removed from wishlist.');
        } catch (\Exception $e) {
            return redirect()->route('storefront.wishlist.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Move item from wishlist to cart.
     */
    public function wishlistMoveToCart(string $purchasableType, int $purchasableId)
    {
        try {
            $purchasableClass = $this->getPurchasableClass($purchasableType);
            $purchasable = $purchasableClass::findOrFail($purchasableId);

            $purchasableService = new PurchasableService();
            $purchasableService->moveFromWishlistToCart($purchasable);

            return redirect()->route('storefront.wishlist.index')->with('success', 'Item moved to cart.');
        } catch (\Exception $e) {
            return redirect()->route('storefront.wishlist.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Move all available items from wishlist to cart.
     */
    public function wishlistMoveAllToCart()
    {
        try {
            $purchasableService = new PurchasableService();
            $results = $purchasableService->moveAllFromWishlistToCart();

            $movedCount = count($results);
            if ($movedCount > 0) {
                return redirect()->route('storefront.wishlist.index')->with('success', "Moved {$movedCount} items to cart.");
            } else {
                return redirect()->route('storefront.wishlist.index')->with('info', 'No available items to move to cart.');
            }
        } catch (\Exception $e) {
            return redirect()->route('storefront.wishlist.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Clear entire wishlist.
     */
    public function wishlistClear()
    {
        try {
            $purchasableService = new PurchasableService();
            $purchasableService->clearWishlist();

            return redirect()->route('storefront.wishlist.index')->with('success', 'Wishlist cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('storefront.wishlist.index')->with('error', $e->getMessage());
        }
    }

    // OLD CHECKOUT METHODS - DEPRECATED
    // Use CheckoutController instead (packages/elevate/commerce-core/src/Http/Controllers/CheckoutController.php)
    
    // /**
    //  * Show checkout page.
    //  */
    // public function checkout()
    // {
    //     $purchasableService = new PurchasableService();
    //     $cart = $purchasableService->getCart();
    //     $totals = $purchasableService->getCartTotals();

    //     if (!$cart || $cart->isEmpty()) {
    //         return redirect()->route('storefront.cart.index')->with('error', 'Your cart is empty.');
    //     }

    //     $user = Auth::user();
    //     $addresses = $user ? $user->addresses : collect();

    //     return view('commerce::storefront.checkout', compact('cart', 'totals', 'user', 'addresses'));
    // }

    // /**
    //  * Handle checkout completion (3DS return URL).
    //  */
    // public function checkoutComplete(Request $request)
    // {
    //     // This is where customers return after 3DS authentication
    //     // For now, redirect to success page
    //     return redirect()->route('storefront.checkout.success')
    //         ->with('success', 'Payment completed successfully!');
    // }

    // /**
    //  * Show checkout success page.
    //  */
    // public function checkoutSuccess(Request $request)
    // {
    //     $orderId = $request->get('order');
    //     $order = null;
        
    //     if ($orderId) {
    //         // Try to find the order for the current user/session
    //         if (Auth::check()) {
    //             $order = Auth::user()->orders()->find($orderId);
    //         }
    //     }
        
    //     return view('commerce::storefront.checkout-success', compact('order'));
    // }

    /**
     * Get purchasable class from type string.
     */
    protected function getPurchasableClass(string $type): string
    {
        // If it's already a full class name, return it directly
        if (class_exists($type)) {
            return $type;
        }
        
        // Otherwise, look it up in the purchasableTypes array
        if (!isset($this->purchasableTypes[$type])) {
            throw new \InvalidArgumentException("Invalid purchasable type: {$type}");
        }

        return $this->purchasableTypes[$type];
    }


    /**
     * Display the shop with sellables
     */
    public function shop(Request $request)
    {
        $query = Sellable::with(['sellableType', 'images'])
            ->where('status', 'active');

        // Filter by type if specified
        if ($request->filled('type')) {
            $query->where('sellable_type_id', $request->type);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price * 100);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price * 100);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort products
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12);
        $sellableTypes = \App\Models\SellableType::orderBy('name')->get();
        return view('commerce::storefront.shop', compact('products', 'sellableTypes'));
    }

    /**
     * Display a single sellable product
     */
    public function product(Sellable $sellable)
    {
        // Only show active products
        if ($sellable->status !== 'active') {
            abort(404);
        }

        $sellable->load(['sellableType', 'images', 'productOptions']);

        // Get related products (same type, excluding current)
        $relatedProducts = Sellable::with(['images'])
            ->where('sellable_type_id', $sellable->sellable_type_id)
            ->where('id', '!=', $sellable->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view('commerce::storefront.product', compact('sellable', 'relatedProducts'));
    }
}
