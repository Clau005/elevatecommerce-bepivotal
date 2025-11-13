<?php

namespace Elevate\CommerceCore\Services;

use Elevate\CommerceCore\Models\Wishlist;
use Elevate\CommerceCore\Models\WishlistLine;
use Elevate\CommerceCore\Models\CartLine;
use Elevate\CommerceCore\Services\PurchasableService;
use Illuminate\Support\Str;

class WishlistService
{
    protected ?string $sessionId;
    protected ?int $userId;
    protected ?string $wishlistToken;
    protected ?Wishlist $wishlist = null;

    public function __construct(?string $sessionId = null, ?int $userId = null, ?string $wishlistToken = null)
    {
        // Validate session ID length and format
        if ($sessionId && (strlen($sessionId) > 255 || strlen($sessionId) < 10)) {
            throw new \InvalidArgumentException('Invalid session ID format');
        }

        // Validate user ID
        if ($userId && ($userId <= 0 || $userId > PHP_INT_MAX)) {
            throw new \InvalidArgumentException('Invalid user ID');
        }

        $this->sessionId = $sessionId;
        $this->userId = $userId;
        $this->wishlistToken = $wishlistToken;
        
        // Generate session ID if none provided and not authenticated
        if (!$this->sessionId && !$this->userId) {
            $this->sessionId = Str::uuid()->toString();
        }
    }

    /**
     * Get the current wishlist (create if needed).
     */
    public function getWishlist(): Wishlist
    {
        if (!$this->wishlist) {
            $this->wishlist = Wishlist::getOrCreateWishlist($this->sessionId, $this->userId, $this->wishlistToken);
        }

        return $this->wishlist;
    }

    /**
     * Get existing wishlist (don't create).
     */
    public function getExistingWishlist(): ?Wishlist
    {
        return Wishlist::getWishlist($this->sessionId, $this->userId, $this->wishlistToken);
    }

    /**
     * Add item to wishlist.
     */
    public function add($purchasable, array $meta = []): WishlistLine
    {
        if (!$purchasable) {
            throw new \InvalidArgumentException('Purchasable item cannot be null');
        }

        if (!method_exists($purchasable, 'isAvailableForPurchase')) {
            throw new \InvalidArgumentException('Item must implement Purchasable trait');
        }

        if (!$purchasable->isAvailableForPurchase()) {
            throw new \Exception("Item '{$purchasable->getName()}' is not available for purchase");
        }

        return $this->getWishlist()->addItem($purchasable, $meta);
    }

    /**
     * Remove item from wishlist.
     */
    public function remove($purchasable): bool
    {
        if (!$purchasable) {
            throw new \InvalidArgumentException('Purchasable item cannot be null');
        }

        $wishlist = $this->getExistingWishlist();
        
        if (!$wishlist) {
            return false;
        }

        return $wishlist->removeItem($purchasable);
    }

    /**
     * Check if item is in wishlist.
     */
    public function has($purchasable): bool
    {
        if (!$purchasable) {
            return false; // Null items are never in wishlist
        }

        $wishlist = $this->getExistingWishlist();
        
        if (!$wishlist) {
            return false;
        }

        return $wishlist->hasItem($purchasable);
    }

    /**
     * Clear all items from wishlist.
     */
    public function clear(): bool
    {
        $wishlist = $this->getExistingWishlist();
        
        if (!$wishlist) {
            return true; // Already empty
        }

        $wishlist->clear();
        return true;
    }

    /**
     * Get wishlist totals and item count.
     */
    public function getWishlistTotals(): array
    {
        $wishlist = $this->getExistingWishlist();
        
        if (!$wishlist) {
            return [
                'item_count' => 0,
                'total_value' => 0,
                'formatted' => [
                    'total_value' => '£0.00',
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
                    'formatted_price' => '£' . number_format($unitPrice / 100, 2),
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
                'total_value' => '£' . number_format($totalValue / 100, 2),
            ],
            'items' => $items,
        ];
    }

    /**
     * Move single item from wishlist to cart.
     */
    public function moveToCart($purchasable, int $quantity = 1): ?CartLine
    {
        if (!$purchasable) {
            throw new \InvalidArgumentException('Purchasable item cannot be null');
        }

        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than 0');
        }

        $wishlist = $this->getExistingWishlist();
        
        if (!$wishlist || !$wishlist->hasItem($purchasable)) {
            return null;
        }

        return $wishlist->moveItemToCart($purchasable, $quantity);
    }

    /**
     * Move all available items from wishlist to cart.
     */
    public function moveAllToCart(): array
    {
        $wishlist = $this->getExistingWishlist();
        
        if (!$wishlist) {
            return [];
        }

        return $wishlist->moveAllToCart();
    }

    /**
     * Merge guest wishlist with user wishlist (when user logs in).
     */
    public function mergeGuestWishlist(string $guestSessionId, int $userId, ?string $wishlistToken = null): void
    {
        $guestWishlist = Wishlist::getWishlist($guestSessionId, null, $wishlistToken);
        
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

    // ===== STATIC CONVENIENCE METHODS =====

    /**
     * Static method to add item to wishlist.
     */
    public static function addItem($purchasable, array $meta = []): WishlistLine
    {
        $service = new static();
        return $service->add($purchasable, $meta);
    }

    /**
     * Static method to remove item from wishlist.
     */
    public static function removeItem($purchasable): bool
    {
        $service = new static();
        return $service->remove($purchasable);
    }

    /**
     * Static method to check if item is in wishlist.
     */
    public static function hasItem($purchasable): bool
    {
        $service = new static();
        return $service->has($purchasable);
    }

    /**
     * Static method to get wishlist totals.
     */
    public static function totals(): array
    {
        $service = new static();
        return $service->getWishlistTotals();
    }
}
