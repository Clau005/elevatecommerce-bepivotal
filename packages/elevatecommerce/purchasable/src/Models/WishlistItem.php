<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * WishlistItem Model
 * 
 * Polymorphic relationship to any purchasable item.
 * 
 * @property int $id
 * @property int $wishlist_id
 * @property int $purchasable_id
 * @property string $purchasable_type
 * @property string|null $note (customer note about why they want this)
 * @property int|null $priority (1-5, for sorting)
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class WishlistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'wishlist_id',
        'purchasable_id',
        'purchasable_type',
        'note',
        'priority',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    /**
     * Get the wishlist that owns the item
     */
    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }

    /**
     * Get the purchasable item (polymorphic)
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for high priority items
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 4);
    }

    /**
     * Scope for ordered by priority
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderByDesc('priority')->orderByDesc('created_at');
    }
}
