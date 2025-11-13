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
    protected ?string $cartToken;
    protected ?string $wishlistToken;

    public function __construct(?string $sessionId = null, ?int $userId = null, ?string $cartToken = null, ?string $wishlistToken = null)
    {
        $this->sessionId = $sessionId ?: session()->getId();
        $this->userId = $userId ?: auth()->id();
        $this->cartToken = $cartToken ?: request()->cookie('cart_token') ?: request()->input('cart_token');
        $this->wishlistToken = $wishlistToken;
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
        return Cart::getCart($this->sessionId, $this->userId, $this->cartToken);
    }

    /**
     * Get or create the current cart.
     */
    public function getOrCreateCart(): Cart
    {
        return Cart::getOrCreateCart($this->sessionId, $this->userId, $this->cartToken);
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

        return $purchasable->addToCart($quantity, $this->sessionId, $this->userId, $meta, $this->cartToken);
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

        return $purchasable->updateCartQuantity($quantity, $this->sessionId, $this->userId, $this->cartToken);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart($purchasable): bool
    {
        if (!$this->isPurchasable($purchasable)) {
            throw new \InvalidArgumentException('Item must use the Purchasable trait');
        }

        return $purchasable->removeFromCart($this->sessionId, $this->userId, $this->cartToken);
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
                    'subtotal' => $this->formatCurrency(0),
                    'tax' => $this->formatCurrency(0),
                    'total' => $this->formatCurrency(0),
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
        return Wishlist::getWishlist($this->sessionId, $this->userId, $this->wishlistToken);
    }

    /**
     * Get or create the current wishlist.
     */
    public function getOrCreateWishlist(): Wishlist
    {
        return Wishlist::getOrCreateWishlist($this->sessionId, $this->userId, $this->wishlistToken);
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

        return $purchasable->addToWishlist($this->sessionId, $this->userId, $meta, $this->wishlistToken);
    }

    /**
     * Remove item from wishlist.
     */
    public function removeFromWishlist($purchasable): bool
    {
        if (!$this->isPurchasable($purchasable)) {
            throw new \InvalidArgumentException('Item must use the Purchasable trait');
        }

        return $purchasable->removeFromWishlist($this->sessionId, $this->userId, $this->wishlistToken);
    }

    /**
     * Check if item is in wishlist.
     */
    public function isInWishlist($purchasable): bool
    {
        if (!$this->isPurchasable($purchasable)) {
            return false;
        }

        return $purchasable->isInWishlist($this->sessionId, $this->userId, $this->wishlistToken);
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

        return $purchasable->moveFromWishlistToCart($quantity, $this->sessionId, $this->userId, $this->wishlistToken);
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
     * Get wishlist totals.
     */
    public function getWishlistTotals(): array
    {
        $wishlist = $this->getWishlist();
        
        if (!$wishlist) {
            return [
                'item_count' => 0,
                'total_value' => 0,
                'formatted' => [
                    'total_value' => $this->formatCurrency(0),
                ],
                'items' => [],
            ];
        }

        $lines = $wishlist->lines()->with('purchasable')->get();
        $itemCount = $lines->count();
        $totalValue = 0;
        $items = [];

        foreach ($lines as $line) {
            if ($line->purchasable) {
                $unitPrice = $line->unit_price;
                $totalValue += $unitPrice;
                
                $items[] = [
                    'id' => $line->id,
                    'name' => $line->name,
                    'description' => $line->description,
                    'unit_price' => $unitPrice,
                    'formatted_price' => $this->formatCurrency($unitPrice),
                    'is_available' => $line->is_available,
                    'purchasable_type' => $line->purchasable_type,
                    'purchasable_id' => $line->purchasable_id,
                    'meta' => $line->meta,
                ];
            }
        }

        return [
            'item_count' => $itemCount,
            'total_value' => $totalValue,
            'formatted' => [
                'total_value' => $this->formatCurrency($totalValue),
            ],
            'items' => $items,
        ];
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
     * Checkout - convert cart to order.
     */
    public function checkout(array $customerData = [], array $shippingAddress = [], ?array $billingAddress = null): Order
    {
        $cart = $this->getCart();
        
        if (!$cart || $cart->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        // Validate all items are still available and in stock
        foreach ($cart->lines as $line) {
            $purchasable = $line->purchasable;
            
            if (!$purchasable) {
                \Log::error('Cart line has null purchasable', [
                    'line_id' => $line->id,
                    'purchasable_type' => $line->purchasable_type,
                    'purchasable_id' => $line->purchasable_id,
                    'description' => $line->description,
                ]);
                throw new \Exception("Cart item '{$line->description}' is no longer available");
            }
            
            if (!$purchasable->isAvailableForPurchase()) {
                throw new \Exception("Item '{$purchasable->getName()}' is no longer available");
            }
            
            if (!$purchasable->hasStock($line->quantity)) {
                $stock = $purchasable->getStockLevel();
                throw new \Exception("Insufficient stock for '{$purchasable->getName()}'. Available: {$stock}, In cart: {$line->quantity}");
            }
        }

        DB::beginTransaction();
        
        try {
            // Find existing pending order or create new one
            $order = $cart->findOrCreateOrder();

            // Add customer data if provided
            if (!empty($customerData)) {
                $meta = $order->meta ?? [];
                $meta['customer'] = $customerData;
                $order->update(['meta' => $meta]);
            }

            // Add addresses if provided
            if (!empty($shippingAddress)) {
                $order->addresses()->create(array_merge($shippingAddress, ['type' => 'shipping']));
            }
            
            if (!empty($billingAddress)) {
                $order->addresses()->create(array_merge($billingAddress, ['type' => 'billing']));
            }

            // Discounts are now applied during order creation in Cart::createOrder()

            // DON'T clear cart yet - wait for payment confirmation
            // Cart will be cleared after successful payment

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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

    /**
     * Format currency amount.
     */
    protected function formatCurrency(int $amountInCents): string
    {
        $currency = Currency::getDefault();
        
        if ($currency) {
            return $currency->formatAmount($amountInCents);
        }

        // Fallback formatting
        return '£' . number_format($amountInCents / 100, 2);
    }

    // ===== STATIC CONVENIENCE METHODS =====

    /**
     * Quick add to cart (uses current session/user).
     */
    public static function add($purchasable, int $quantity = 1, array $meta = []): CartLine
    {
        return (new static())->addToCart($purchasable, $quantity, $meta);
    }

    /**
     * Quick remove from cart (uses current session/user).
     */
    public static function remove($purchasable): bool
    {
        return (new static())->removeFromCart($purchasable);
    }

    /**
     * Quick quantity update (uses current session/user).
     */
    public static function updateQty($purchasable, int $quantity): bool
    {
        return (new static())->updateQuantity($purchasable, $quantity);
    }

    /**
     * Quick checkout (uses current session/user).
     */
    public static function createOrder(array $customerData = [], array $shippingAddress = [], ?array $billingAddress = null): Order
    {
        return (new static())->checkout($customerData, $shippingAddress, $billingAddress);
    }

    /**
     * Get current cart totals (uses current session/user).
     */
    public static function totals(): array
    {
        return (new static())->getCartTotals();
    }

    // ===== STATIC WISHLIST CONVENIENCE METHODS =====

    /**
     * Quick add to wishlist (uses current session/user).
     */
    public static function addToWish($purchasable, array $meta = []): WishlistLine
    {
        return (new static())->addToWishlist($purchasable, $meta);
    }

    /**
     * Quick remove from wishlist (uses current session/user).
     */
    public static function removeFromWish($purchasable): bool
    {
        return (new static())->removeFromWishlist($purchasable);
    }

    /**
     * Quick check if in wishlist (uses current session/user).
     */
    public static function inWishlist($purchasable): bool
    {
        return (new static())->isInWishlist($purchasable);
    }

    /**
     * Quick move from wishlist to cart (uses current session/user).
     */
    public static function moveToCart($purchasable, int $quantity = 1): ?CartLine
    {
        return (new static())->moveFromWishlistToCart($purchasable, $quantity);
    }

    /**
     * Get current wishlist totals (uses current session/user).
     */
    public static function wishlistTotals(): array
    {
        return (new static())->getWishlistTotals();
    }
}
