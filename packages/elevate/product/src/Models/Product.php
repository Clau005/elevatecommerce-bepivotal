<?php

namespace Elevate\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Elevate\CommerceCore\Traits\Purchasable;
use Elevate\CommerceCore\Traits\HasTags;
use Elevate\Editor\Traits\HasTemplate;

class Product extends Model
{
    use SoftDeletes, Purchasable, HasTags, HasTemplate;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'type', // simple or variable
        'status', // draft, active, archived
        'price',
        'compare_at_price',
        'cost_per_item',
        'track_inventory',
        'stock',
        'weight',
        'weight_unit',
        'requires_shipping',
        'is_taxable',
        'tax_rate',
        'featured_image',
        'gallery_images',
        'meta_title',
        'meta_description',
        'template_id',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'integer', // Stored in cents!
        'compare_at_price' => 'integer', // Stored in cents!
        'cost_per_item' => 'integer', // Stored in cents!
        'track_inventory' => 'boolean',
        'stock' => 'integer',
        'weight' => 'float',
        'requires_shipping' => 'boolean',
        'is_taxable' => 'boolean',
        'tax_rate' => 'float',
        'gallery_images' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Get product variants
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(\Elevate\Editor\Models\Template::class);
    }

    /**
     * Check if product has variants
     */
    public function hasVariants(): bool
    {
        return $this->type === 'variable' && $this->variants()->count() > 0;
    }

    /**
     * Get the default variant (first variant or null)
     */
    public function defaultVariant(): ?ProductVariant
    {
        return $this->variants()->first();
    }

    // ========================================
    // Purchasable Trait Overrides (only when needed)
    // ========================================

    /**
     * Get the preview image.
     * Override to use 'featured_image' instead of 'image'
     */
    public function getPreview(): ?string
    {
        return $this->featured_image ?? null;
    }

    /**
     * Get the unit price in cents.
     * Override to handle variable products (use cheapest variant)
     */
    public function getUnitPrice(): int
    {
        if ($this->hasVariants()) {
            return $this->variants()->min('price') ?? 0;
        }

        return $this->price ?? 0; // Already in cents!
    }

    /**
     * Get stock level.
     * Override to sum variant stock for variable products
     */
    public function getStockLevel(): ?int
    {
        if ($this->hasVariants()) {
            return $this->variants()->sum('stock');
        }

        return $this->stock;
    }

    // ========================================
    // Additional Helper Methods
    // ========================================

    /**
     * Get price in dollars (for display)
     */
    public function getPriceInDollars(): float
    {
        return $this->getUnitPrice() / 100;
    }

    /**
     * Get compare at price in cents
     */
    public function getCompareAtPrice(): ?int
    {
        if ($this->hasVariants()) {
            return $this->variants()->min('compare_at_price');
        }

        return $this->compare_at_price;
    }

    /**
     * Check if product is on sale
     */
    public function isOnSale(): bool
    {
        $comparePrice = $this->getCompareAtPrice();
        return $comparePrice && $comparePrice > $this->getUnitPrice();
    }

    /**
     * Get the URL to view this product
     */
    public function getPurchasableUrl(): ?string
    {
        return route('product.show', $this->slug);
    }

    // ========================================
    // Query Scopes
    // ========================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSimple($query)
    {
        return $query->where('type', 'simple');
    }

    public function scopeVariable($query)
    {
        return $query->where('type', 'variable');
    }

    // ========================================
    // Accessors (for backwards compatibility)
    // ========================================

    /**
     * Alias 'image' to 'featured_image' for Purchasable trait
     */
    public function getImageAttribute()
    {
        return $this->featured_image;
    }
}
