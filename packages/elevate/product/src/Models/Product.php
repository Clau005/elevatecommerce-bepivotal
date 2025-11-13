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
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_per_item' => 'decimal:2',
        'track_inventory' => 'boolean',
        'stock' => 'integer',
        'weight' => 'decimal:2',
        'requires_shipping' => 'boolean',
        'is_taxable' => 'boolean',
        'tax_rate' => 'decimal:4',
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
        // Convert price to cents
        if ($this->hasVariants()) {
            $minPrice = $this->variants()->min('price') ?? 0;
            return (int) ($minPrice * 100);
        }

        return (int) ($this->price * 100);
    }

    /**
     * Get the description for cart/checkout
     */
    public function getDescription(): string
    {
        return $this->short_description ?? $this->description ?? '';
    }

    /**
     * Get the identifier (SKU)
     */
    public function getIdentifier(): string
    {
        return $this->sku ?? $this->slug ?? (string) $this->id;
    }

    // ========================================
    // Additional Purchasable Methods
    // ========================================

    /**
     * Get the purchasable price
     */
    public function getPrice(): float
    {
        // If has variants, return the lowest variant price
        if ($this->hasVariants()) {
            return $this->variants()->min('price') ?? 0;
        }

        return (float) $this->price;
    }

    /**
     * Get the compare at price
     */
    public function getCompareAtPrice(): ?float
    {
        if ($this->hasVariants()) {
            return $this->variants()->min('compare_at_price');
        }

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
        return $this->featured_image;
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
        // If has variants, sum up variant stock
        if ($this->hasVariants()) {
            return $this->variants()->sum('stock');
        }

        return $this->stock;
    }

    /**
     * Get weight for shipping
     */
    public function getWeight(): ?float
    {
        return $this->weight ? (float) $this->weight : null;
    }

    /**
     * Get tax rate
     */
    public function getTaxRate(): float
    {
        return $this->is_taxable ? (float) $this->tax_rate : 0.0;
    }

    /**
     * Check if requires shipping
     */
    public function requiresShipping(): bool
    {
        return $this->requires_shipping;
    }

    /**
     * Get additional meta data
     */
    public function getMetaData(): array
    {
        return [
            'type' => $this->type,
            'weight_unit' => $this->weight_unit,
            'has_variants' => $this->hasVariants(),
        ];
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for simple products
     */
    public function scopeSimple($query)
    {
        return $query->where('type', 'simple');
    }

    /**
     * Scope for variable products
     */
    public function scopeVariable($query)
    {
        return $query->where('type', 'variable');
    }
}
