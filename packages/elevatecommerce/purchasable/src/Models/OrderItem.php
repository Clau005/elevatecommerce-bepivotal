<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * OrderItem Model
 * 
 * Polymorphic relationship to any purchasable item.
 * Snapshot of item at time of purchase.
 * 
 * @property int $id
 * @property int $order_id
 * @property int $purchasable_id
 * @property string $purchasable_type
 * @property string $name (snapshot)
 * @property string|null $sku (snapshot)
 * @property int $quantity
 * @property int $price (in smallest currency unit - snapshot at time of purchase)
 * @property int $cost_price (in smallest currency unit - snapshot)
 * @property int $tax (in smallest currency unit)
 * @property int $discount (in smallest currency unit)
 * @property array|null $options (e.g., size, color, custom text)
 * @property array|null $metadata (full snapshot of purchasable data)
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'purchasable_id',
        'purchasable_type',
        'name',
        'sku',
        'quantity',
        'price',
        'cost_price',
        'tax',
        'discount',
        'options',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'cost_price' => 'integer',
        'tax' => 'integer',
        'discount' => 'integer',
        'options' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the order that owns the item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the purchasable item (polymorphic)
     * Note: This may return null if the original item was deleted
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
     * Get formatted cost price
     */
    public function getFormattedCostPriceAttribute(): float
    {
        return $this->cost_price / 100;
    }

    /**
     * Get formatted tax
     */
    public function getFormattedTaxAttribute(): float
    {
        return $this->tax / 100;
    }

    /**
     * Get formatted discount
     */
    public function getFormattedDiscountAttribute(): float
    {
        return $this->discount / 100;
    }

    /**
     * Get line subtotal (price * quantity)
     */
    public function getLineSubtotalAttribute(): int
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get line total (subtotal + tax - discount)
     */
    public function getLineTotalAttribute(): int
    {
        return $this->line_subtotal + $this->tax - $this->discount;
    }

    /**
     * Get formatted line subtotal
     */
    public function getFormattedLineSubtotalAttribute(): float
    {
        return $this->line_subtotal / 100;
    }

    /**
     * Get formatted line total
     */
    public function getFormattedLineTotalAttribute(): float
    {
        return $this->line_total / 100;
    }

    /**
     * Get profit (line total - cost)
     */
    public function getProfitAttribute(): int
    {
        return $this->line_total - ($this->cost_price * $this->quantity);
    }

    /**
     * Get formatted profit
     */
    public function getFormattedProfitAttribute(): float
    {
        return $this->profit / 100;
    }

    /**
     * Create snapshot from purchasable item
     */
    public static function createFromPurchasable($purchasable, int $quantity, ?array $options = null): array
    {
        return [
            'purchasable_id' => $purchasable->id,
            'purchasable_type' => get_class($purchasable),
            'name' => $purchasable->name,
            'sku' => $purchasable->sku,
            'quantity' => $quantity,
            'price' => $purchasable->price,
            'cost_price' => $purchasable->cost_price ?? 0,
            'tax' => 0, // Calculate separately
            'discount' => 0, // Calculate separately
            'options' => $options,
            'metadata' => [
                'short_description' => $purchasable->short_description,
                'description' => $purchasable->description,
                'unit_price' => $purchasable->unit_price,
                'image' => $purchasable->purchasable_image ?? null,
            ],
        ];
    }
}
