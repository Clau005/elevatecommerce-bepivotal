<?php

namespace ElevateCommerce\Purchasable\Http\Controllers;

use ElevateCommerce\Purchasable\Models\Cart;
use ElevateCommerce\Purchasable\Models\Wishlist;
use ElevateCommerce\Purchasable\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page
     */
    public function index(Request $request)
    {
        $wishlist = $this->getWishlist($request);
        
        return view('purchasable::wishlist.index', [
            'wishlist' => $wishlist,
            'items' => $wishlist->items()->with('purchasable')->get(),
        ]);
    }

    /**
     * Add item to wishlist
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'purchasable_type' => 'required|string',
            'purchasable_id' => 'required|integer',
        ]);

        $wishlist = $this->getWishlist($request);
        
        // Get the purchasable model
        $purchasableClass = $validated['purchasable_type'];
        $purchasable = $purchasableClass::findOrFail($validated['purchasable_id']);

        // Add to wishlist
        $wishlist->addItem($purchasable);

        return back()->with('success', 'Item added to wishlist!');
    }

    /**
     * Remove item from wishlist
     */
    public function remove(WishlistItem $wishlistItem)
    {
        $wishlistItem->delete();

        return back()->with('success', 'Item removed from wishlist!');
    }

    /**
     * Move item from wishlist to cart
     */
    public function moveToCart(Request $request, WishlistItem $wishlistItem)
    {
        $cart = $this->getCart($request);
        $purchasable = $wishlistItem->purchasable;

        // Check if item can be purchased
        if (!$purchasable->canPurchase()) {
            return back()->with('error', 'This item is not available for purchase.');
        }

        // Check stock
        if ($purchasable->track_inventory && $purchasable->stock_quantity < 1) {
            return back()->with('error', 'This item is out of stock.');
        }

        // Add to cart
        $cart->addItem($purchasable, 1);
        
        // Remove from wishlist
        $wishlistItem->delete();

        return back()->with('success', 'Item moved to cart!');
    }

    /**
     * Move all wishlist items to cart
     */
    public function moveAllToCart(Request $request)
    {
        $wishlist = $this->getWishlist($request);
        $cart = $this->getCart($request);

        $items = $wishlist->items()->with('purchasable')->get();
        $movedCount = 0;
        $skippedCount = 0;

        foreach ($items as $item) {
            $purchasable = $item->purchasable;

            // Check if can be purchased
            if (!$purchasable->canPurchase()) {
                $skippedCount++;
                continue;
            }

            // Check stock
            if ($purchasable->track_inventory && $purchasable->stock_quantity < 1) {
                $skippedCount++;
                continue;
            }

            // Add to cart
            $cart->addItem($purchasable, 1);
            $item->delete();
            $movedCount++;
        }

        $message = "Moved {$movedCount} items to cart!";
        if ($skippedCount > 0) {
            $message .= " ({$skippedCount} items were unavailable)";
        }

        return redirect()->route('purchasable.cart.index')
            ->with('success', $message);
    }

    /**
     * Clear the entire wishlist
     */
    public function clear(Request $request)
    {
        $wishlist = $this->getWishlist($request);
        $wishlist->items()->delete();

        return back()->with('success', 'Wishlist cleared!');
    }

    /**
     * Get or create cart for current user/session
     */
    protected function getCart(Request $request): Cart
    {
        if (auth()->check()) {
            return Cart::forUser(auth()->user());
        }

        return Cart::forSession($request->session()->getId());
    }

    /**
     * Get or create wishlist for current user/session
     */
    protected function getWishlist(Request $request): Wishlist
    {
        if (auth()->check()) {
            return Wishlist::forUser(auth()->user());
        }

        return Wishlist::forSession($request->session()->getId());
    }
}
