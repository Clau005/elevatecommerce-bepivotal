<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ElevateCommerce\Purchasable\Traits\IsPurchasable;

class TestingPurchasable extends Model
{
    use SoftDeletes, IsPurchasable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'compare_at_price',
        'is_active',
        'stock_quantity',
        'track_inventory',
        'image_url',
        'options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'integer',
        'compare_at_price' => 'integer',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
        'track_inventory' => 'boolean',
        'options' => 'array',
    ];

    /**
     * Check if item is in stock
     * 
     * @return bool
     */
    public function inStock(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->track_inventory) {
            return $this->stock_quantity > 0;
        }

        return true;
    }

    /**
     * Get available quantity
     * 
     * @return int|null
     */
    public function availableQuantity(): ?int
    {
        if ($this->track_inventory) {
            return $this->stock_quantity;
        }

        return null;
    }

    /**
     * Get purchasable name (required by IsPurchasable)
     * 
     * @return string
     */
    public function getPurchasableName(): string
    {
        return $this->name;
    }

    /**
     * Get purchasable SKU (required by IsPurchasable)
     * 
     * @return string
     */
    public function getPurchasableSku(): string
    {
        return $this->sku;
    }

    /**
     * Get purchasable price (required by IsPurchasable)
     * 
     * @return int
     */
    public function getPurchasablePrice(): int
    {
        return $this->price;
    }
}
