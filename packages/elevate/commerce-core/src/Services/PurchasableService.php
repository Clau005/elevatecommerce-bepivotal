<?php

namespace Elevate\CommerceCore\Services;

use Elevate\CommerceCore\Models\Cart;
use Elevate\CommerceCore\Models\CartLine;
use Elevate\CommerceCore\Models\Wishlist;
use Elevate\CommerceCore\Models\WishlistLine;
use Elevate\CommerceCore\Models\Order;
use App\Models\User;
use Elevate\CommerceCore\Models\Currency;
use Illuminate\Support\Facades\DB;

class PurchasableService
{
    protected ?string $sessionId;
    protected ?int $userId;

    public function __construct(?string $sessionId = null, ?int $userId = null)
    {
        $this->sessionId = $sessionId ?: session()->getId();
        $this->userId = $userId ?: auth()->id();
    }

    /**
     * Create a new service instance for a specific user/session.
     */
    public static function for(?string $sessionId = null, ?int $userId = null): self
    {
        return new static($sessionId, $userId);
    }

    /**
     * Get the current cart.
     */
    public function getCart(): ?Cart
    {
        return Cart::getCart($this->sessionId, $this->userId);
    }

    /**
     * Get or create the current cart.
     */
    public function getOrCreateCart(): Cart
    {
        return Cart::getOrCreateCart($this->sessionId, $this->userId);
    }

    /**
     * Add item to cart.
     */
    public function addToCart($purchasable, int $quantity = 1, array $meta = []): CartLine
    {
        // Validate purchasable
        if (!$this->isPurchasable($purchasable)) {
            throw new \InvalidArgumentException('Item must use the Purchasable trait');
        }

        // Check availability
        if (!$purchasable->isAvailableForPurchase()) {
            throw new \Exception("Item '{$purchasable->getName()}' is not available for purchase");
        }

        // Check stock
        if (!$purchasable->hasStock($quantity)) {
            $stock = $purchasable->getStockLevel();
            throw new \Exception("Insufficient stock. Available: {$stock}, Requested: {$quantity}");
        }

        return $purchasable->addToCart($quantity, $this->sessionId, $this->userId, $meta);
    }

    /**
     * Update item quantity in cart.
     */
    public function updateQuantity($purchasable, int $quantity): bool
    {
        if (!$this->isPurchasable($purchasable)) {
            throw new \InvalidArgumentException('Item must use the Purchasable trait');
        }

        if ($quantity > 0 && !$purchasable->hasStock($quantity)) {
            $stock = $purchasable->getStockLevel();
            throw new \Exception("Insufficient stock. Available: {$stock}, Requested: {$quantity}");
        }

        return $purchasable->updateCartQuantity($quantity, $this->sessionId, $this->userId);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart($purchasable): bool
    {
        if (!$this->isPurchasable($purchasable)) {
            throw new \InvalidArgumentException('Item must use the Purchasable trait');
        }

        return $purchasable->removeFromCart($this->sessionId, $this->userId);
    }

    /**
     * Clear entire cart.
     */
    public function clearCart(): bool
    {
        $cart = $this->getCart();
        
        if ($cart) {
            $cart->clear();
            return true;
        }

        return false;
    }

    /**
     * Get cart totals with currency formatting.
     */
    public function getCartTotals(): array
    {
        $cart = $this->getCart();
        
        if (!$cart) {
            return [
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'item_count' => 0,
                'formatted' => [
                    'subtotal' => '£0.00',
                    'tax' => '£0.00',
                    'total' => '£0.00',
                ],
            ];
        }

        return $cart->getFormattedTotals();
    }

    // ===== WISHLIST OPERATIONS =====

    /**
     * Get the current wishlist.
     */
    public function getWishlist(): ?Wishlist
    {
        return Wishlist::getWishlist($this->sessionId, $this->userId);
    }

    /**
     * Get or create the current wishlist.
     */
    public function getOrCreateWishlist(): Wishlist
    {
        return Wishlist::getOrCreateWishlist($this->sessionId, $this->userId);
    }

    /**
     * Add item to wishlist.
     */
    public function addToWishlist($purchasable, array $meta = []): WishlistLine
    {
        // Validate purchasable
        if (!$this->isPurchasable($purchasable)) {
            throw new \InvalidArgumentException('Item must use the Purchasable trait');
        }

        // Check availability
        if (!$purchasable->isAvailableForPurchase()) {
            throw new \Exception("Item '{$purchasable->getName()}' is not available for purchase");
        }

        return $purchasable->addToWishlist($this->sessionId, $this->userId, $meta);
    }

    /**
     * Remove item from wishlist.
     */
    public function removeFromWishlist($purchasable): bool
    {
        if (!$this->isPurchasable($purchasable)) {
            throw new \InvalidArgumentException('Item must use the Purchasable trait');
        }

        return $purchasable->removeFromWishlist($this->sessionId, $this->userId);
    }

    /**
     * Check if item is in wishlist.
     */
    public function isInWishlist($purchasable): bool
    {
        if (!$this->isPurchasable($purchasable)) {
            return false;
        }

        return $purchasable->isInWishlist($this->sessionId, $this->userId);
    }

    /**
     * Move item from wishlist to cart.
     */
    public function moveFromWishlistToCart($purchasable, int $quantity = 1): ?CartLine
    {
        if (!$this->isPurchasable($purchasable)) {
            throw new \InvalidArgumentException('Item must use the Purchasable trait');
        }

        // Check stock before moving
        if (!$purchasable->hasStock($quantity)) {
            $stock = $purchasable->getStockLevel();
            throw new \Exception("Insufficient stock. Available: {$stock}, Requested: {$quantity}");
        }

        return $purchasable->moveFromWishlistToCart($quantity, $this->sessionId, $this->userId);
    }

    /**
     * Clear entire wishlist.
     */
    public function clearWishlist(): bool
    {
        $wishlist = $this->getWishlist();
        
        if ($wishlist) {
            $wishlist->clear();
            return true;
        }

        return false;
    }

    /**
     * Get wishlist item count.
     */
    public function getWishlistCount(): int
    {
        $wishlist = $this->getWishlist();
        return $wishlist ? $wishlist->getItemCount() : 0;
    }

    /**
     * Move all available items from wishlist to cart.
     */
    public function moveAllFromWishlistToCart(): array
    {
        $wishlist = $this->getWishlist();
        
        if (!$wishlist) {
            return [];
        }

        return $wishlist->moveAllToCart();
    }

    /**
     * Merge guest wishlist with user wishlist after login.
     */
    public function mergeGuestWishlist(string $guestSessionId, int $userId): void
    {
        $guestWishlist = Wishlist::getWishlist($guestSessionId, null);
        
        if (!$guestWishlist || $guestWishlist->isEmpty()) {
            return;
        }

        $userWishlist = Wishlist::getOrCreateWishlist(null, $userId);
        
        // Move items from guest wishlist to user wishlist
        foreach ($guestWishlist->lines()->with('purchasable')->get() as $line) {
            if ($line->purchasable) {
                try {
                    $userWishlist->addItem($line->purchasable, $line->meta ?? []);
                } catch (\Exception $e) {
                    // Skip items that can't be added (duplicates will be handled by addItem)
                    continue;
                }
            }
        }

        // Delete guest wishlist
        $guestWishlist->delete();
    }

    /**
     * Merge guest cart with user cart after login.
     */
    public function mergeGuestCart(string $guestSessionId, int $userId): void
    {
        $guestCart = Cart::getCart($guestSessionId, null);
        
        if (!$guestCart || $guestCart->isEmpty()) {
            return;
        }

        $userCart = Cart::getOrCreateCart(null, $userId);

        DB::beginTransaction();
        
        try {
            foreach ($guestCart->lines as $guestLine) {
                // Check if user cart already has this item
                $existingLine = $userCart->lines()
                    ->where('purchasable_type', $guestLine->purchasable_type)
                    ->where('purchasable_id', $guestLine->purchasable_id)
                    ->first();

                if ($existingLine) {
                    // Merge quantities
                    $newQuantity = $existingLine->quantity + $guestLine->quantity;
                    
                    // Check stock before merging
                    if ($guestLine->purchasable->hasStock($newQuantity)) {
                        $existingLine->update(['quantity' => $newQuantity]);
                        $existingLine->recalculate();
                    }
                } else {
                    // Move the line to user cart
                    $guestLine->update(['cart_id' => $userCart->id]);
                }
            }

            // Delete the guest cart
            $guestCart->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if an object is purchasable.
     */
    protected function isPurchasable($item): bool
    {
        return in_array('Elevate\CommerceCore\Traits\Purchasable', class_uses_recursive($item));
    }
}
