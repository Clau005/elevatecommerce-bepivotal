<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WishlistLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'wishlist_id',
        'purchasable_type',
        'purchasable_id',
        'preview',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // ===== RELATIONSHIPS =====

    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    // ===== HELPER METHODS =====

    /**
     * Get formatted description
     */
    public function getDescriptionAttribute(): string
    {
        return $this->purchasable?->getDescription() ?? 'Unknown Item';
    }

    /**
     * Get formatted name
     */
    public function getNameAttribute(): string
    {
        return $this->purchasable?->getName() ?? 'Unknown Item';
    }

    /**
     * Get unit price in cents
     */
    public function getUnitPriceAttribute(): int
    {
        return $this->purchasable?->getUnitPrice() ?? 0;
    }

    /**
     * Check if item is available for purchase
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->purchasable?->isAvailableForPurchase() ?? false;
    }

    /**
     * Move this item to cart
     */
    public function moveToCart(int $quantity = 1): ?CartLine
    {
        if (!$this->purchasable || !$this->is_available) {
            return null;
        }

        // Add to cart
        $cartLine = $this->purchasable->addToCart(
            $quantity,
            $this->wishlist->session_id,
            $this->wishlist->user_id
        );

        // Remove from wishlist
        $this->delete();

        return $cartLine;
    }
}
