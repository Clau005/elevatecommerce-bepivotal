<?php

namespace Elevate\CommerceCore\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Purchasable
{
    /**
     * Boot the trait - add any observers here
     */
    public static function bootPurchasable()
    {
        // Add any global scopes or observers here if needed
    }

    /**
     * Get the cart lines for this purchasable item.
     */
    public function cartLines(): MorphMany
    {
        return $this->morphMany(\Elevate\CommerceCore\Models\CartLine::class, 'purchasable');
    }

    /**
     * Get the order lines for this purchasable item.
     */
    public function orderLines(): MorphMany
    {
        return $this->morphMany(\Elevate\CommerceCore\Models\OrderLine::class, 'purchasable');
    }

    /**
     * Get the wishlist lines for this purchasable item.
     */
    public function wishlistLines(): MorphMany
    {
        return $this->morphMany(\Elevate\CommerceCore\Models\WishlistLine::class, 'purchasable');
    }

    // ===== CORE METHODS (smart defaults - override if needed) =====

    /**
     * Get the preview image for this purchasable item.
     * 
     * Convention: Uses 'image' property
     * 
     * Override if you need custom logic:
     * - Use different property name
     * - Get first gallery image
     * - Generate thumbnail
     * 
     * @return string|null Image URL or path
     */
    public function getPreview(): ?string
    {
        return $this->image ?? null;
    }

    /**
     * Get the price for this purchasable item (in cents).
     * 
     * Convention: Uses 'price' property (stored in cents)
     * 
     * IMPORTANT: Price MUST be stored in cents (e.g., $10.00 = 1000)
     * 
     * Override if you need custom logic:
     * - Calculate from variants
     * - Apply member discounts
     * - Use different property name
     * 
     * @return int Price in cents
     */
    public function getUnitPrice(): int
    {
        return (int) ($this->price ?? 0);
    }

    /**
     * Get the description for this purchasable item.
     * 
     * Convention: Uses 'description' property
     * 
     * Override if you need custom logic:
     * - Use different property name
     * - Truncate long descriptions
     * - Format with HTML
     * 
     * @return string Description text
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * Get the identifier/SKU for this purchasable item.
     * 
     * Convention: Uses 'sku' property, falls back to 'id'
     * 
     * Override if you need custom logic:
     * - Use different property name
     * - Generate SKU dynamically
     * - Include variant SKU
     * 
     * @return string Unique identifier
     */
    public function getIdentifier(): string
    {
        return $this->sku ?? (string) $this->id;
    }

    /**
     * Get the name/title for this purchasable item.
     * 
     * Convention: Uses 'name' property, falls back to 'id'
     * 
     * Override if you need custom logic:
     * - Use different property name
     * - Include variant name
     * - Format with brand
     * 
     * @return string Item name
     */
    public function getName(): string
    {
        return $this->name ?? "Item #{$this->id}";
    }

    // ===== OPTIONAL METHODS (with sensible defaults - override as needed) =====

    /**
     * Check if this item is available for purchase.
     * 
     * Override to add custom logic:
     * - Check status (active/inactive)
     * - Check dates (available_from/available_until)
     * - Check visibility
     * - Check any custom conditions
     * 
     * @return bool
     */
    public function isAvailableForPurchase(): bool
    {
        // Check common properties if they exist
        if (property_exists($this, 'status') && $this->status !== 'active') {
            return false;
        }
        
        if (property_exists($this, 'is_active') && !$this->is_active) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if this item tracks inventory.
     * 
     * Override to customize:
     * - Check different property names
     * - Check variant-level inventory
     * - Implement complex inventory logic
     * 
     * @return bool
     */
    public function tracksInventory(): bool
    {
        // Check common property names
        if (property_exists($this, 'track_inventory')) {
            return (bool) $this->track_inventory;
        }
        
        if (property_exists($this, 'manage_stock')) {
            return (bool) $this->manage_stock;
        }
        
        // Default: don't track inventory
        return false;
    }

    /**
     * Get the current stock level.
     * 
     * Override to:
     * - Use different property names
     * - Calculate from variants
     * - Query external inventory system
     * 
     * @return int|null Null means unlimited stock
     */
    public function getStockLevel(): ?int
    {
        // Check common property names
        if (property_exists($this, 'stock_quantity')) {
            return $this->stock_quantity;
        }
        
        if (property_exists($this, 'stock')) {
            return $this->stock;
        }
        
        if (property_exists($this, 'quantity')) {
            return $this->quantity;
        }
        
        // Null = unlimited stock
        return null;
    }

    /**
     * Check if sufficient stock is available.
     * 
     * Override for complex stock logic:
     * - Reserved stock
     * - Backorders
     * - Multi-warehouse
     * 
     * @param int $quantity
     * @return bool
     */
    public function hasStock(int $quantity = 1): bool
    {
        if (!$this->tracksInventory()) {
            return true; // Unlimited stock
        }

        $stock = $this->getStockLevel();
        
        // Null stock level = unlimited
        if ($stock === null) {
            return true;
        }
        
        return $stock >= $quantity;
    }

    /**
     * Check if this item requires shipping.
     * 
     * Override for:
     * - Digital products (return false)
     * - Services (return false)
     * - Physical products (return true)
     * 
     * @return bool
     */
    public function requiresShipping(): bool
    {
        // Check common property names
        if (property_exists($this, 'requires_shipping')) {
            return (bool) $this->requires_shipping;
        }
        
        if (property_exists($this, 'is_physical')) {
            return (bool) $this->is_physical;
        }
        
        if (property_exists($this, 'type')) {
            return !in_array($this->type, ['digital', 'service', 'virtual']);
        }
        
        // Default: physical products require shipping
        return true;
    }

    /**
     * Get the weight for shipping calculations (in grams).
     * 
     * Override to:
     * - Use different units
     * - Calculate from variants
     * - Include packaging weight
     * 
     * @return float|null Weight in grams, null if not applicable
     */
    public function getWeight(): ?float
    {
        if (property_exists($this, 'weight')) {
            return (float) $this->weight;
        }
        
        if (property_exists($this, 'weight_grams')) {
            return (float) $this->weight_grams;
        }
        
        // Default weight for unknown items (500g)
        return $this->requiresShipping() ? 500.0 : null;
    }

    /**
     * Get the dimensions for shipping calculations.
     * 
     * Override to:
     * - Use different property names
     * - Calculate from variants
     * - Use different units
     * 
     * @return array|null ['length' => float, 'width' => float, 'height' => float, 'unit' => string]
     */
    public function getDimensions(): ?array
    {
        $length = property_exists($this, 'length') ? $this->length : null;
        $width = property_exists($this, 'width') ? $this->width : null;
        $height = property_exists($this, 'height') ? $this->height : null;
        
        if ($length && $width && $height) {
            return [
                'length' => (float) $length,
                'width' => (float) $width,
                'height' => (float) $height,
                'unit' => 'cm',
            ];
        }
        
        return null;
    }

    /**
     * Get the tax rate for this item (as decimal, e.g., 0.20 for 20%).
     * 
     * Override to:
     * - Use product-specific tax rates
     * - Calculate based on tax category
     * - Implement location-based tax
     * 
     * @return float Tax rate as decimal (0.20 = 20%)
     */
    public function getTaxRate(): float
    {
        if (property_exists($this, 'tax_rate')) {
            return (float) $this->tax_rate;
        }
        
        if (property_exists($this, 'vat_rate')) {
            return (float) $this->vat_rate;
        }
        
        // Default: no tax
        return 0.0;
    }

    /**
     * Get additional meta data for this purchasable item.
     * 
     * Override to add:
     * - Product options (size, color, etc.)
     * - Customization data
     * - Gift messages
     * - Any custom data to store with cart/order line
     * 
     * @return array
     */
    public function getPurchasableMeta(): array
    {
        $meta = [];
        
        // Add common meta if properties exist
        if (property_exists($this, 'color') && $this->color) {
            $meta['color'] = $this->color;
        }
        
        if (property_exists($this, 'size') && $this->size) {
            $meta['size'] = $this->size;
        }
        
        return $meta;
    }

    /**
     * Get the purchasable type for database storage.
     * 
     * Generally don't override this unless you have a specific reason.
     * 
     * @return string
     */
    public function getPurchasableType(): string
    {
        return static::class;
    }

    /**
     * Get a URL to view this purchasable item.
     * 
     * Override to provide a link to the product page.
     * 
     * @return string|null
     */
    public function getPurchasableUrl(): ?string
    {
        // Try common route patterns
        if (method_exists($this, 'getUrl')) {
            return $this->getUrl();
        }
        
        if (property_exists($this, 'slug')) {
            return route('product.show', $this->slug);
        }
        
        return null;
    }

    /**
     * Add this item to cart.
     */
    public function addToCart(int $quantity, ?string $sessionId = null, ?int $userId = null, array $meta = [])
    {
        $cart = \Elevate\CommerceCore\Models\Cart::getOrCreateCart($sessionId, $userId);
        
        return $cart->add(
            purchasable: $this,
            quantity: $quantity,
            meta: $meta
        );
    }

    /**
     * Update quantity in cart.
     */
    public function updateCartQuantity(int $quantity, ?string $sessionId = null, ?int $userId = null): bool
    {
        $cart = \Elevate\CommerceCore\Models\Cart::getCart($sessionId, $userId);
        
        if (!$cart) {
            return false;
        }

        $line = $cart->lines()
            ->where('purchasable_type', static::class)
            ->where('purchasable_id', $this->id)
            ->first();

        if (!$line) {
            return false;
        }

        if ($quantity > 0) {
            $line->update(['quantity' => $quantity]);
            $line->recalculate();
        } else {
            $line->delete();
        }

        return true;
    }

    /**
     * Remove this item from cart.
     */
    public function removeFromCart(?string $sessionId = null, ?int $userId = null): bool
    {
        $cart = \Elevate\CommerceCore\Models\Cart::getCart($sessionId, $userId);
        
        if (!$cart) {
            return false;
        }

        return $cart->lines()
            ->where('purchasable_type', static::class)
            ->where('purchasable_id', $this->id)
            ->delete() > 0;
    }

    // ===== WISHLIST METHODS =====

    /**
     * Add this item to wishlist.
     */
    public function addToWishlist(?string $sessionId = null, ?int $userId = null, array $meta = [])
    {
        $wishlist = \Elevate\CommerceCore\Models\Wishlist::getOrCreateWishlist($sessionId, $userId);
        
        return $wishlist->addItem($this, $meta);
    }

    /**
     * Remove this item from wishlist.
     */
    public function removeFromWishlist(?string $sessionId = null, ?int $userId = null): bool
    {
        $wishlist = \Elevate\CommerceCore\Models\Wishlist::getWishlist($sessionId, $userId);
        
        if (!$wishlist) {
            return false;
        }

        return $wishlist->removeItem($this);
    }

    /**
     * Check if this item is in wishlist.
     */
    public function isInWishlist(?string $sessionId = null, ?int $userId = null): bool
    {
        $wishlist = \Elevate\CommerceCore\Models\Wishlist::getWishlist($sessionId, $userId);
        
        if (!$wishlist) {
            return false;
        }

        return $wishlist->hasItem($this);
    }

    /**
     * Move this item from wishlist to cart.
     */
    public function moveFromWishlistToCart(int $quantity = 1, ?string $sessionId = null, ?int $userId = null)
    {
        $wishlist = \Elevate\CommerceCore\Models\Wishlist::getWishlist($sessionId, $userId);
        
        if (!$wishlist) {
            return null;
        }

        return $wishlist->moveItemToCart($this, $quantity);
    }
}
