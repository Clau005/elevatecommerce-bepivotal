<?php

namespace ElevateCommerce\Purchasable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * IsPurchasable Trait
 * 
 * Add this trait to any model you want to make purchasable (sellable).
 * 
 * Required attributes on the model:
 * - name (string): Product/item name
 * - short_description (string, nullable): Brief description
 * - description (text, nullable): Full description
 * - price (integer): Selling price in smallest currency unit (e.g., cents)
 * - unit_price (integer, nullable): Original/unit price before discounts
 * - cost_price (integer, nullable): Cost to business
 * - sku (string): Stock Keeping Unit (unique identifier)
 * 
 * All prices are stored as integers in the smallest currency unit.
 * Example: $20.00 = 2000 (cents)
 * 
 * Usage:
 * ```php
 * use ElevateCommerce\Purchasable\Traits\IsPurchasable;
 * 
 * class Product extends Model
 * {
 *     use IsPurchasable;
 * }
 * ```
 */
trait IsPurchasable
{
    /**
     * Boot the trait
     */
    protected static function bootIsPurchasable(): void
    {
        static::creating(function ($model) {
            // Validate required fields before creating
            $model->validatePurchasableFields();
        });
    }

    /**
     * Validate that all required purchasable fields are present
     * 
     * @throws \Exception
     */
    protected function validatePurchasableFields(): void
    {
        $required = ['name', 'price', 'sku'];
        
        foreach ($required as $field) {
            if (empty($this->$field)) {
                throw new \Exception("IsPurchasable trait requires '{$field}' field on " . get_class($this));
            }
        }

        // Validate price is integer
        if (!is_int($this->price)) {
            throw new \Exception("Price must be an integer (smallest currency unit) on " . get_class($this));
        }
    }

    /**
     * Get cart items for this purchasable
     */
    public function cartItems(): MorphMany
    {
        return $this->morphMany(\ElevateCommerce\Purchasable\Models\CartItem::class, 'purchasable');
    }

    /**
     * Get wishlist items for this purchasable
     */
    public function wishlistItems(): MorphMany
    {
        return $this->morphMany(\ElevateCommerce\Purchasable\Models\WishlistItem::class, 'purchasable');
    }

    /**
     * Get order items for this purchasable
     */
    public function orderItems(): MorphMany
    {
        return $this->morphMany(\ElevateCommerce\Purchasable\Models\OrderItem::class, 'purchasable');
    }

    /**
     * Get formatted price (converts from smallest unit to decimal)
     * 
     * @return float
     */
    public function getFormattedPriceAttribute(): float
    {
        return $this->price / 100;
    }

    /**
     * Get formatted unit price
     * 
     * @return float|null
     */
    public function getFormattedUnitPriceAttribute(): ?float
    {
        return $this->unit_price ? $this->unit_price / 100 : null;
    }

    /**
     * Get formatted cost price
     * 
     * @return float|null
     */
    public function getFormattedCostPriceAttribute(): ?float
    {
        return $this->cost_price ? $this->cost_price / 100 : null;
    }

    /**
     * Set price (converts decimal to smallest unit)
     * 
     * @param float|int $value
     */
    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = is_float($value) ? (int)($value * 100) : $value;
    }

    /**
     * Set unit price (converts decimal to smallest unit)
     * 
     * @param float|int|null $value
     */
    public function setUnitPriceAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['unit_price'] = null;
            return;
        }
        $this->attributes['unit_price'] = is_float($value) ? (int)($value * 100) : $value;
    }

    /**
     * Set cost price (converts decimal to smallest unit)
     * 
     * @param float|int|null $value
     */
    public function setCostPriceAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['cost_price'] = null;
            return;
        }
        $this->attributes['cost_price'] = is_float($value) ? (int)($value * 100) : $value;
    }

    /**
     * Check if item is in stock (override in your model if needed)
     * 
     * @return bool
     */
    public function inStock(): bool
    {
        // Default implementation - override in your model
        return true;
    }

    /**
     * Get available quantity (override in your model if needed)
     * 
     * @return int|null
     */
    public function availableQuantity(): ?int
    {
        // Default implementation - override in your model
        return null;
    }

    /**
     * Check if item can be purchased
     * 
     * @param int $quantity
     * @return bool
     */
    public function canPurchase(int $quantity = 1): bool
    {
        if (!$this->inStock()) {
            return false;
        }

        $available = $this->availableQuantity();
        if ($available !== null && $quantity > $available) {
            return false;
        }

        return true;
    }

    /**
     * Get purchasable display name
     * 
     * @return string
     */
    public function getPurchasableNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get purchasable image (override in your model)
     * 
     * @return string|null
     */
    public function getPurchasableImageAttribute(): ?string
    {
        // Override in your model to return image URL
        return null;
    }
}
