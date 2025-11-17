<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * CartItem Model
 * 
 * Polymorphic relationship to any purchasable item.
 * 
 * @property int $id
 * @property int $cart_id
 * @property int $purchasable_id
 * @property string $purchasable_type
 * @property int $quantity
 * @property int $price (in smallest currency unit - snapshot at time of adding)
 * @property array|null $options (e.g., size, color, custom text)
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'purchasable_id',
        'purchasable_type',
        'quantity',
        'price',
        'options',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'options' => 'array',
    ];

    /**
     * Get the cart that owns the item
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the purchasable item (polymorphic)
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): float
    {
        return $this->price / 100;
    }

    /**
     * Get line total (price * quantity)
     */
    public function getLineTotalAttribute(): int
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get formatted line total
     */
    public function getFormattedLineTotalAttribute(): float
    {
        return $this->line_total / 100;
    }

    /**
     * Update quantity
     */
    public function updateQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->save();
        $this->cart->recalculate();
    }

    /**
     * Increment quantity
     */
    public function incrementQuantity(int $amount = 1): void
    {
        $this->quantity += $amount;
        $this->save();
        $this->cart->recalculate();
    }

    /**
     * Decrement quantity
     */
    public function decrementQuantity(int $amount = 1): void
    {
        $this->quantity = max(1, $this->quantity - $amount);
        $this->save();
        $this->cart->recalculate();
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Recalculate cart totals when item is created, updated, or deleted
        static::created(function ($item) {
            $item->cart->recalculate();
        });

        static::updated(function ($item) {
            $item->cart->recalculate();
        });

        static::deleted(function ($item) {
            $item->cart->recalculate();
        });
    }
}
