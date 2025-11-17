<?php

namespace Elevate\CommerceCore\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // ===== RELATIONSHIPS =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(WishlistLine::class);
    }

    // ===== STATIC METHODS =====


    /**
     * Get or create wishlist for user/session
     * Priority: User ID (logged in) > Session ID (guest)
     */
    public static function getOrCreateWishlist(?string $sessionId = null, ?int $userId = null): self
    {
        // For authenticated users
        if ($userId) {
            $wishlist = static::where('user_id', $userId)->first();
            
            if ($wishlist) {
                return $wishlist;
            }
            
            // Create new user wishlist
            return static::create([
                'user_id' => $userId,
                'session_id' => null,
            ]);
        }
        
        // For guests, use session-based wishlist
        $sessionId = $sessionId ?: session()->getId();
        $wishlist = static::where('session_id', $sessionId)
                         ->whereNull('user_id')
                         ->first();
        
        if (!$wishlist) {
            $wishlist = static::create([
                'user_id' => null,
                'session_id' => $sessionId,
            ]);
        }
        
        return $wishlist;
    }

    /**
     * Get existing wishlist (don't create)
     * Priority: User ID (logged in) > Session ID (guest)
     */
    public static function getWishlist(?string $sessionId = null, ?int $userId = null): ?self
    {
        // For authenticated users
        if ($userId) {
            return static::where('user_id', $userId)->first();
        }
        
        // For guests, use session-based wishlist
        $sessionId = $sessionId ?: session()->getId();
        return static::where('session_id', $sessionId)
                     ->whereNull('user_id')
                     ->first();
    }

    // ===== INSTANCE METHODS =====

    /**
     * Add item to wishlist
     */
    public function addItem($purchasable, array $meta = []): WishlistLine
    {
        if (!$purchasable || !is_object($purchasable)) {
            throw new \InvalidArgumentException('Purchasable item must be a valid object');
        }

        if (!$purchasable->id) {
            throw new \InvalidArgumentException('Purchasable item must have a valid ID');
        }

        // Check if item already exists
        $existingLine = $this->lines()
            ->where('purchasable_type', get_class($purchasable))
            ->where('purchasable_id', $purchasable->id)
            ->first();

        if ($existingLine) {
            // Update meta if needed
            if (!empty($meta)) {
                $existingLine->update(['meta' => array_merge($existingLine->meta ?? [], $meta)]);
            }
            return $existingLine;
        }

        // Create new wishlist line
        return $this->lines()->create([
            'purchasable_type' => get_class($purchasable),
            'purchasable_id' => $purchasable->id,
            'meta' => array_merge($purchasable->getPurchasableMeta() ?? [], $meta),
        ]);
    }

    /**
     * Remove item from wishlist
     */
    public function removeItem($purchasable): bool
    {
        return $this->lines()
            ->where('purchasable_type', get_class($purchasable))
            ->where('purchasable_id', $purchasable->id)
            ->delete() > 0;
    }

    /**
     * Check if item is in wishlist
     */
    public function hasItem($purchasable): bool
    {
        return $this->lines()
            ->where('purchasable_type', get_class($purchasable))
            ->where('purchasable_id', $purchasable->id)
            ->exists();
    }

    /**
     * Get item count
     */
    public function getItemCount(): int
    {
        return $this->lines()->count();
    }

    /**
     * Check if wishlist is empty
     */
    public function isEmpty(): bool
    {
        return $this->getItemCount() === 0;
    }

    /**
     * Clear all items
     */
    public function clear(): void
    {
        $this->lines()->delete();
    }

    /**
     * Move item to cart
     */
    public function moveItemToCart($purchasable, int $quantity = 1): ?CartLine
    {
        if (!$this->hasItem($purchasable)) {
            return null;
        }

        // Add to cart
        $cartLine = $purchasable->addToCart($quantity, $this->session_id, $this->user_id);

        // Remove from wishlist
        $this->removeItem($purchasable);

        return $cartLine;
    }

    /**
     * Move all items to cart
     */
    public function moveAllToCart(): array
    {
        $cartLines = [];

        foreach ($this->lines()->with('purchasable')->get() as $line) {
            if ($line->purchasable && $line->purchasable->isAvailableForPurchase()) {
                try {
                    $cartLine = $this->moveItemToCart($line->purchasable, 1);
                    if ($cartLine) {
                        $cartLines[] = $cartLine;
                    }
                } catch (\Exception $e) {
                    // Skip items that can't be added to cart (out of stock, etc.)
                    continue;
                }
            }
        }

        return $cartLines;
    }
}
