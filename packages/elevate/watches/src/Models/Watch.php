<?php

namespace Elevate\Watches\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Elevate\CommerceCore\Traits\Purchasable;
use Elevate\CommerceCore\Traits\HasTags;
use Elevate\Editor\Traits\HasTemplate;
use Elevate\Collections\Traits\HasCollections;

class Watch extends Model
{
    use HasFactory, Purchasable, HasTags, HasTemplate, HasCollections;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'brand',
        'model_number',
        'movement_type', // Automatic, Quartz, Manual
        'case_material', // Stainless Steel, Gold, Titanium, etc.
        'case_diameter', // in mm
        'water_resistance', // in meters
        'strap_material', // Leather, Metal, Rubber, etc.
        'price',
        'compare_at_price',
        'cost',
        'sku',
        'barcode',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'featured_image',
        'images',
        'status', // draft, active, archived
        'template_id',
        'meta_title',
        'meta_description',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'weight' => 'decimal:2',
        'case_diameter' => 'decimal:1',
        'water_resistance' => 'integer',
        'images' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'formatted_price',
        'is_on_sale',
        'discount_percentage',
        'is_in_stock',
    ];

    /**
     * Polymorphic relationship to collectables
     */
    public function collectables()
    {
        return $this->morphMany(\Elevate\Collections\Models\Collectable::class, 'collectable');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Check if watch is on sale
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->is_on_sale) {
            return null;
        }

        return (int) round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
    }

    /**
     * Check if watch is in stock
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Scope for active watches
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for featured watches
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for in-stock watches
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // ========================================
    // Purchasable Trait Implementations (Required)
    // ========================================

    /**
     * Get the purchasable name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the preview image for cart/checkout
     */
    public function getPreview(): ?string
    {
        return $this->featured_image;
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
        return $this->description ?? '';
    }

    /**
     * Get the identifier (SKU)
     */
    public function getIdentifier(): string
    {
        return $this->sku ?? $this->slug ?? (string) $this->id;
    }
}
