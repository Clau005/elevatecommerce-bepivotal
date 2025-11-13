<?php

namespace Elevate\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Elevate\CommerceCore\Traits\Purchasable;

class ProductVariant extends Model
{
    use SoftDeletes, Purchasable;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'compare_at_price',
        'cost_per_item',
        'track_inventory',
        'stock',
        'weight',
        'image',
        'option1_name',
        'option1_value',
        'option2_name',
        'option2_value',
        'option3_name',
        'option3_value',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_per_item' => 'decimal:2',
        'track_inventory' => 'boolean',
        'stock' => 'integer',
        'weight' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get variant title (combination of options)
     */
    public function getVariantTitle(): string
    {
        $parts = array_filter([
            $this->option1_value,
            $this->option2_value,
            $this->option3_value,
        ]);

        return implode(' / ', $parts);
    }

    // ========================================
    // Purchasable Trait Implementations (Required)
    // ========================================

    /**
     * Get the purchasable name
     */
    public function getName(): string
    {
        return $this->product->name . ' - ' . $this->getVariantTitle();
    }

    /**
     * Get the preview image for cart/checkout
     */
    public function getPreview(): ?string
    {
        return $this->image ?? $this->product->featured_image;
    }

    /**
     * Get the unit price in cents
     */
    public function getUnitPrice(): int
    {
        return (int) ($this->price * 100);
    }

    /**
     * Get the description for cart/checkout
     */
    public function getDescription(): string
    {
        return $this->product->short_description ?? $this->product->description ?? '';
    }

    /**
     * Get the identifier (SKU)
     */
    public function getIdentifier(): string
    {
        return $this->sku ?? $this->product->sku ?? (string) $this->id;
    }

    // ========================================
    // Additional Purchasable Methods
    // ========================================

    /**
     * Get the purchasable price
     */
    public function getPrice(): float
    {
        return (float) $this->price;
    }

    /**
     * Get the compare at price
     */
    public function getCompareAtPrice(): ?float
    {
        return $this->compare_at_price ? (float) $this->compare_at_price : null;
    }

    /**
     * Get the SKU
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * Get the image URL
     */
    public function getImageUrl(): ?string
    {
        return $this->image ?? $this->product->featured_image;
    }

    /**
     * Check if tracks inventory
     */
    public function tracksInventory(): bool
    {
        return $this->track_inventory;
    }

    /**
     * Get stock level
     */
    public function getStockLevel(): ?int
    {
        return $this->stock;
    }

    /**
     * Get weight for shipping
     */
    public function getWeight(): ?float
    {
        return $this->weight ? (float) $this->weight : $this->product->getWeight();
    }

    /**
     * Get tax rate
     */
    public function getTaxRate(): float
    {
        return $this->product->getTaxRate();
    }

    /**
     * Check if requires shipping
     */
    public function requiresShipping(): bool
    {
        return $this->product->requiresShipping();
    }

    /**
     * Get additional meta data
     */
    public function getMetaData(): array
    {
        return [
            'product_id' => $this->product_id,
            'variant_title' => $this->getVariantTitle(),
            'options' => [
                $this->option1_name => $this->option1_value,
                $this->option2_name => $this->option2_value,
                $this->option3_name => $this->option3_value,
            ],
        ];
    }

    /**
     * Scope for active variants
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
