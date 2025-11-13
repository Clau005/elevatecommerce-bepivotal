<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'purchasable_type',
        'purchasable_id',
        'quantity',
        'unit_price',
        'description',
        'identifier',
        'preview',
        'meta',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'meta' => 'array',
    ];

    protected $appends = [
        'formatted_unit_price',
        'formatted_sub_total',
        'formatted_total',
    ];

    /**
     * Get the cart that owns the line.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the purchasable item.
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the sub total for this line.
     */
    public function getSubTotalAttribute(): int
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Get the total for this line (including discounts, taxes).
     */
    public function getTotalAttribute(): int
    {
        return $this->sub_total; // Add discount/tax logic later
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return $this->formatCurrency($this->unit_price);
    }

    /**
     * Get formatted sub total.
     */
    public function getFormattedSubTotalAttribute(): string
    {
        return $this->formatCurrency($this->sub_total);
    }

    /**
     * Get formatted total.
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->formatCurrency($this->total);
    }

    /**
     * Format currency amount from cents to display format.
     */
    protected function formatCurrency(int $amountInCents): string
    {
        $amount = $amountInCents / 100;
        return '£' . number_format($amount, 2);
    }

    /**
     * Recalculate line totals.
     */
    public function recalculate(): void
    {
        if ($this->purchasable) {
            $this->unit_price = $this->purchasable->getUnitPrice();
            $this->save();
        }
    }
}
