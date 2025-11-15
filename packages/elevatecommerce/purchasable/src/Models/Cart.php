<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Cart Model
 * 
 * Supports both guest (session-based) and authenticated user carts.
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property int $subtotal (in smallest currency unit)
 * @property int $tax (in smallest currency unit)
 * @property int $shipping (in smallest currency unit)
 * @property int $discount (in smallest currency unit)
 * @property int $total (in smallest currency unit)
 * @property array|null $metadata
 * @property \Carbon\Carbon|null $expires_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'metadata',
        'expires_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'tax' => 'integer',
        'shipping' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the cart
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Get all cart items
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Check if cart belongs to a guest
     */
    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Check if cart is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): float
    {
        return $this->subtotal / 100;
    }

    /**
     * Get formatted tax
     */
    public function getFormattedTaxAttribute(): float
    {
        return $this->tax / 100;
    }

    /**
     * Get formatted shipping
     */
    public function getFormattedShippingAttribute(): float
    {
        return $this->shipping / 100;
    }

    /**
     * Get formatted discount
     */
    public function getFormattedDiscountAttribute(): float
    {
        return $this->discount / 100;
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): float
    {
        return $this->total / 100;
    }

    /**
     * Get total item count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Recalculate cart totals
     */
    public function recalculate(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $this->subtotal = $subtotal;
        
        // Calculate tax (from config)
        $taxRate = config('purchasable.tax.rate', 0);
        $this->tax = (int)($subtotal * $taxRate);

        // Calculate shipping (simplified - can be enhanced)
        $freeShippingThreshold = config('purchasable.shipping.free_shipping_threshold', 50) * 100;
        $this->shipping = $subtotal >= $freeShippingThreshold ? 0 : 500; // $5.00 shipping

        // Calculate total
        $this->total = $subtotal + $this->tax + $this->shipping - $this->discount;

        $this->save();
    }

    /**
     * Clear all items from cart
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->subtotal = 0;
        $this->tax = 0;
        $this->shipping = 0;
        $this->discount = 0;
        $this->total = 0;
        $this->save();
    }
}
