<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Wishlist Model
 * 
 * Supports both guest (session-based) and authenticated user wishlists.
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property string|null $name (e.g., "My Wishlist", "Birthday Wishlist")
 * @property bool $is_public
 * @property array|null $metadata
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'name',
        'is_public',
        'metadata',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the wishlist
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Get all wishlist items
     */
    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Get or create wishlist for authenticated user
     */
    public static function forUser($user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'My Wishlist',
                'is_public' => false,
            ]
        );
    }

    /**
     * Get or create wishlist for guest session
     */
    public static function forSession(string $sessionId): self
    {
        return static::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'name' => 'Guest Wishlist',
                'is_public' => false,
            ]
        );
    }

    /**
     * Add item to wishlist
     */
    public function addItem($purchasable, ?string $note = null, int $priority = 0): WishlistItem
    {
        return $this->items()->firstOrCreate(
            [
                'purchasable_type' => get_class($purchasable),
                'purchasable_id' => $purchasable->id,
            ],
            [
                'note' => $note,
                'priority' => $priority,
            ]
        );
    }

    /**
     * Check if wishlist belongs to a guest
     */
    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Get total item count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->count();
    }

    /**
     * Check if item exists in wishlist
     */
    public function hasItem(string $purchasableType, int $purchasableId): bool
    {
        return $this->items()
            ->where('purchasable_type', $purchasableType)
            ->where('purchasable_id', $purchasableId)
            ->exists();
    }

    /**
     * Clear all items from wishlist
     */
    public function clear(): void
    {
        $this->items()->delete();
    }
}
