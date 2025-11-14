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
     * Note: This will be properly resolved when cart package is loaded
     */
    public function cartLines(): MorphMany
    {
        return $this->morphMany('Elevate\Cart\Models\CartLine', 'purchasable');
    }

    /**
     * Get the order lines for this purchasable item.
     * Note: This will be properly resolved when orders package is loaded
     */
    public function orderLines(): MorphMany
    {
        return $this->morphMany('Elevate\Orders\Models\OrderLine', 'purchasable');
    }

    // ===== REQUIRED METHODS (must be implemented by the model) =====

    /**
     * Get the preview for this purchasable item.
     * This method should be implemented by the model using this trait.
     */
    abstract public function getPreview(): string | null;

    /**
     * Get the price for this purchasable item (in cents).
     * This method should be implemented by the model using this trait.
     */
    abstract public function getUnitPrice(): int;

    /**
     * Get the description for this purchasable item.
     * This method should be implemented by the model using this trait.
     */
    abstract public function getDescription(): string;

    /**
     * Get the identifier/SKU for this purchasable item.
     * This method should be implemented by the model using this trait.
     */
    abstract public function getIdentifier(): string;

    /**
     * Get the name/title for this purchasable item.
     * This method should be implemented by the model using this trait.
     */
    abstract public function getName(): string;

    // ===== OPTIONAL METHODS (with sensible defaults) =====

    /**
     * Check if this item is available for purchase.
     * Override this method to add custom availability logic.
     */
    public function isAvailableForPurchase(): bool
    {
        return true;
    }

    /**
     * Check if this item tracks inventory.
     * Override this method if your model has inventory tracking.
     */
    public function tracksInventory(): bool
    {
        return property_exists($this, 'track_inventory') ? $this->track_inventory : false;
    }

    /**
     * Get the current stock level.
     * Override this method if your model has inventory.
     */
    public function getStockLevel(): ?int
    {
        return property_exists($this, 'stock') ? $this->stock : null;
    }

    /**
     * Check if sufficient stock is available.
     */
    public function hasStock(int $quantity = 1): bool
    {
        if (!$this->tracksInventory()) {
            return true;
        }

        $stock = $this->getStockLevel();
        return $stock === null || $stock >= $quantity;
    }

    /**
     * Check if this item requires shipping.
     * Override this method or add a 'requires_shipping' column to your model.
     */
    public function requiresShipping(): bool
    {
        return property_exists($this, 'requires_shipping') ? $this->requires_shipping : true;
    }

    /**
     * Get the weight for shipping calculations (in grams).
     * Override this method if your model has weight.
     */
    public function getWeight(): ?float
    {
        return property_exists($this, 'weight') ? $this->weight : null;
    }

    /**
     * Get the dimensions for shipping calculations.
     * Override this method if your model has dimensions.
     * Returns array with 'length', 'width', 'height' in cm.
     */
    public function getDimensions(): ?array
    {
        if (property_exists($this, 'length') && property_exists($this, 'width') && property_exists($this, 'height')) {
            return [
                'length' => $this->length,
                'width' => $this->width,
                'height' => $this->height,
                'unit' => 'cm',
            ];
        }
        return null;
    }

    /**
     * Get the tax rate for this item.
     * Override this method for custom tax logic.
     */
    public function getTaxRate(): float
    {
        return 0.0; // No tax by default
    }

    /**
     * Get additional meta data for this purchasable item.
     * Override this method to add custom meta data.
     */
    public function getPurchasableMeta(): array
    {
        return [];
    }

    /**
     * Get the purchasable type for database storage.
     */
    public function getPurchasableType(): string
    {
        return static::class;
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
}
