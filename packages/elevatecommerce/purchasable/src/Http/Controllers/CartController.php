<?php

namespace ElevateCommerce\Purchasable\Http\Controllers;

use ElevateCommerce\Purchasable\Models\Cart;
use ElevateCommerce\Purchasable\Models\CartItem;
use ElevateCommerce\Purchasable\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        
        return view('purchasable::cart.index', [
            'cart' => $cart,
            'items' => $cart->items()->with('purchasable')->get(),
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'purchasable_type' => 'required|string',
            'purchasable_id' => 'required|integer',
            'quantity' => 'integer|min:1',
        ]);

        $cart = $this->getCart($request);
        
        // Get the purchasable model
        $purchasableClass = $validated['purchasable_type'];
        $purchasable = $purchasableClass::findOrFail($validated['purchasable_id']);

        // Check if item can be purchased
        if (!$purchasable->canPurchase()) {
            return back()->with('error', 'This item is not available for purchase.');
        }

        $quantity = $validated['quantity'] ?? 1;

        // Check stock
        if ($purchasable->track_inventory && $purchasable->stock_quantity < $quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        // Add to cart
        $cart->addItem($purchasable, $quantity);

        return back()->with('success', 'Item added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $purchasable = $cartItem->purchasable;

        // Check stock
        if ($purchasable->track_inventory && $purchasable->stock_quantity < $validated['quantity']) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cartItem->updateQuantity($validated['quantity']);

        return back()->with('success', 'Cart updated!');
    }

    /**
     * Remove item from cart
     */
    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart!');
    }

    /**
     * Move item from cart to wishlist
     */
    public function moveToWishlist(Request $request, CartItem $cartItem)
    {
        $wishlist = $this->getWishlist($request);
        
        // Add to wishlist
        $wishlist->addItem($cartItem->purchasable);
        
        // Remove from cart
        $cartItem->delete();

        return back()->with('success', 'Item moved to wishlist!');
    }

    /**
     * Move all cart items to wishlist
     */
    public function moveAllToWishlist(Request $request)
    {
        $cart = $this->getCart($request);
        $wishlist = $this->getWishlist($request);

        $items = $cart->items()->with('purchasable')->get();

        foreach ($items as $item) {
            $wishlist->addItem($item->purchasable);
        }

        // Clear cart
        $cart->items()->delete();
        $cart->recalculate();

        return redirect()->route('purchasable.wishlist.index')
            ->with('success', 'All items moved to wishlist!');
    }

    /**
     * Clear the entire cart
     */
    public function clear(Request $request)
    {
        $cart = $this->getCart($request);
        $cart->items()->delete();
        $cart->recalculate();

        return back()->with('success', 'Cart cleared!');
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
