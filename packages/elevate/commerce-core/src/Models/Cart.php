<?php

namespace Elevate\CommerceCore\Models;

use App\Models\User;
use App\Models\Order;
use Elevate\CommerceCore\Models\Channel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'channel_id',
        'session_id',
        'currency_code',
        'compare_currency_code',
        'exchange_rate',
        'meta',
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'meta' => 'array',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the channel for this cart.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Get the cart lines.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(CartLine::class);
    }

    /**
     * Add a purchasable item to the cart.
     */
    public function add($purchasable, int $quantity = 1, array $meta = []): CartLine
    {
        // Check if item already exists in cart
        $existingLine = $this->lines()
            ->where('purchasable_type', $purchasable->getPurchasableType())
            ->where('purchasable_id', $purchasable->id)
            ->first();

        if ($existingLine) {
            $existingLine->increment('quantity', $quantity);
            $existingLine->recalculate();
            return $existingLine;
        }

        // Create new cart line
        return $this->lines()->create([
            'purchasable_type' => $purchasable->getPurchasableType(),
            'purchasable_id' => $purchasable->id,
            'quantity' => $quantity,
            'unit_price' => $purchasable->getUnitPrice(),
            'description' => $purchasable->getDescription(),
            'identifier' => $purchasable->getIdentifier(),
            'meta' => array_merge($purchasable->getPurchasableMeta(), $meta),
            'preview' => $purchasable->getPreview(),
        ]);
    }

    /**
     * Remove a line from the cart.
     */
    public function removeLine(CartLine $line): void
    {
        $line->delete();
    }

    /**
     * Clear all lines from the cart.
     */
    public function clear(): void
    {
        $this->lines()->delete();
    }

    /**
     * Get the cart totals.
     */
    public function getSubTotal(): int
    {
        return $this->lines->sum('sub_total');
    }

    /**
     * Get the cart total.
     */
    public function getTotal(): int
    {
        return $this->getSubTotal(); // Add discounts, taxes, shipping later
    }

    /**
     * Get formatted cart totals with all details.
     */
    public function getFormattedTotals(): array
    {
        $subtotal = $this->getSubTotal();
        $tax = 0; // Tax-inclusive pricing: no additional tax added
        $shipping = 0; // Free shipping for now
        $total = $subtotal + $tax + $shipping;
        $itemCount = $this->getItemCount();

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'item_count' => $itemCount,
            'formatted' => [
                'subtotal' => $this->formatCurrency($subtotal),
                'tax' => $this->formatCurrency($tax),
                'shipping' => $this->formatCurrency($shipping),
                'total' => $this->formatCurrency($total),
            ]
        ];
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
     * Get or create a cart for session/user.
     * Priority: User ID (logged in) > Session ID (guest)
     */
    public static function getOrCreateCart(?string $sessionId = null, ?int $userId = null): self
    {
        // For authenticated users
        if ($userId) {
            $cart = static::where('user_id', $userId)->first();
            
            if ($cart) {
                return $cart;
            }
            
            // Create new user cart
            $cart = static::create([
                'user_id' => $userId,
                'session_id' => null,
                'channel_id' => null, // Will be set when channels are configured
                'currency_code' => 'GBP',
                'compare_currency_code' => 'GBP',
                'exchange_rate' => 1.0,
                'meta' => [],
            ]);
            
            return $cart;
        }
        
        // For guests, use session-based cart
        $sessionId = $sessionId ?: session()->getId();
        $cart = static::where('session_id', $sessionId)
                     ->whereNull('user_id')
                     ->first();
                     
        if (!$cart) {
            $cart = static::create([
                'user_id' => null,
                'session_id' => $sessionId,
                'channel_id' => null, // Will be set when channels are configured
                'currency_code' => 'GBP',
                'compare_currency_code' => 'GBP',
                'exchange_rate' => 1.0,
                'meta' => [],
            ]);
        }

        return $cart;
    }

    /**
     * Get existing cart (don't create if not found).
     * Priority: User ID (logged in) > Session ID (guest)
     */
    public static function getCart(?string $sessionId = null, ?int $userId = null): ?self
    {
        // For authenticated users
        if ($userId) {
            return static::where('user_id', $userId)->first();
        }
        
        // For guests, use session cart
        $sessionId = $sessionId ?: session()->getId();
        return static::where('session_id', $sessionId)
                     ->whereNull('user_id')
                     ->first();
    }


    /**
     * Get cart count (number of items).
     */
    public function getItemCount(): int
    {
        return $this->lines->sum('quantity');
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->lines->isEmpty();
    }

    /**
     * Find existing pending order for this cart or create a new one.
     */
    public function findOrCreateOrder(): Order
    {
        // Check if there's already a pending order for this user with the same cart contents
        $existingOrder = Order::where('user_id', $this->user_id)
            ->where('status', 'awaiting-payment')
            ->where('sub_total', $this->getSubTotal())
            ->whereDate('created_at', today()) // Only check orders from today
            ->first();

        if ($existingOrder) {
            // Update the existing order with current cart state
            return $this->updateExistingOrder($existingOrder);
        }

        // No existing order found, create a new one
        return $this->createOrder();
    }

    /**
     * Update existing order with current cart state.
     */
    private function updateExistingOrder(Order $existingOrder): Order
    {
        // Get applied coupons from session and calculate discounts
        $appliedCoupons = session()->get('applied_coupons', []);
        $subtotal = $this->getSubTotal();
        $discountTotal = 0;
        $giftVoucherTotal = 0;
        $discountBreakdown = [];
        $giftVoucherBreakdown = [];
        
        if (!empty($appliedCoupons)) {
            $discountService = app(\Elevate\CommerceCore\Services\CheckoutDiscountService::class);
            
            // Create temporary order for discount calculation
            $tempOrder = new Order([
                'sub_total' => $subtotal,
                'total' => $subtotal,
                'user_id' => $this->user_id,
            ]);
            
            foreach ($appliedCoupons as $couponCode) {
                $result = $discountService->applyCouponDiscount($tempOrder, $couponCode);
                if ($result['applied']) {
                    $amount = $result['discount_amount'] * 100; // Convert to cents
                    
                    if (isset($result['gift_voucher'])) {
                        // Gift voucher
                        $giftVoucherTotal += $amount;
                        $giftVoucherBreakdown[] = [
                            'code' => $couponCode,
                            'gift_voucher_id' => $result['gift_voucher']->id,
                            'amount' => $amount,
                            'voucher_value' => $result['voucher_value'],
                            'description' => $result['message'],
                        ];
                    } else {
                        // Regular discount
                        $discountTotal += $amount;
                        $discountBreakdown[] = [
                            'code' => $couponCode,
                            'discount_id' => $result['discount']->id ?? null,
                            'amount' => $amount,
                            'description' => $result['message'],
                        ];
                    }
                }
            }
        }
        
        $totalSavings = $discountTotal + $giftVoucherTotal;
        $finalTotal = max(0, $subtotal - $totalSavings);

        // Update the existing order
        $existingOrder->update([
            'sub_total' => $subtotal,
            'discount_total' => $discountTotal,
            'discount_breakdown' => $discountBreakdown,
            'gift_voucher_total' => $giftVoucherTotal,
            'gift_voucher_breakdown' => $giftVoucherBreakdown,
            'total' => $finalTotal,
            'updated_at' => now(),
        ]);

        return $existingOrder;
    }

    /**
     * Convert cart to order.
     */
    public function createOrder(): Order
    {
        if ($this->isEmpty()) {
            throw new \Exception('Cannot create order from empty cart');
        }

        // Get applied coupons from session and calculate discounts
        $appliedCoupons = session()->get('applied_coupons', []);
        $subtotal = $this->getSubTotal();
        $discountTotal = 0;
        $giftVoucherTotal = 0;
        $discountBreakdown = [];
        $giftVoucherBreakdown = [];
        
        if (!empty($appliedCoupons)) {
            $discountService = app(\Elevate\CommerceCore\Services\CheckoutDiscountService::class);
            
            // Create temporary order for discount calculation
            $tempOrder = new Order([
                'sub_total' => $subtotal,
                'total' => $subtotal,
                'user_id' => $this->user_id,
            ]);
            
            foreach ($appliedCoupons as $couponCode) {
                $result = $discountService->applyCouponDiscount($tempOrder, $couponCode);
                if ($result['applied']) {
                    $amount = $result['discount_amount'] * 100; // Convert to cents
                    
                    if (isset($result['gift_voucher'])) {
                        // Gift voucher
                        $giftVoucherTotal += $amount;
                        $giftVoucherBreakdown[] = [
                            'code' => $couponCode,
                            'gift_voucher_id' => $result['gift_voucher']->id,
                            'amount' => $amount,
                            'voucher_value' => $result['voucher_value'],
                            'description' => $result['message'],
                        ];
                    } else {
                        // Regular discount
                        $discountTotal += $amount;
                        $discountBreakdown[] = [
                            'code' => $couponCode,
                            'discount_id' => $result['discount']->id ?? null,
                            'amount' => $amount,
                            'description' => $result['message'],
                        ];
                    }
                }
            }
        }
        
        $totalSavings = $discountTotal + $giftVoucherTotal;
        $finalTotal = max(0, $subtotal - $totalSavings);

        $order = Order::create([
            'user_id' => $this->user_id,
            'channel_id' => $this->channel_id,
            'new_customer' => $this->user ? $this->user->isNewCustomer() : true,
            'status' => 'awaiting-payment',
            'currency_code' => $this->currency_code,
            'compare_currency_code' => $this->compare_currency_code,
            'exchange_rate' => $this->exchange_rate,
            'sub_total' => $subtotal,
            'discount_total' => $discountTotal,
            'discount_breakdown' => $discountBreakdown,
            'gift_voucher_total' => $giftVoucherTotal,
            'gift_voucher_breakdown' => $giftVoucherBreakdown,
            'total' => $finalTotal,
            'meta' => $this->meta,
        ]);

        // Convert cart lines to order lines
        foreach ($this->lines as $cartLine) {
            $order->lines()->create([
                'purchasable_type' => $cartLine->purchasable_type,
                'purchasable_id' => $cartLine->purchasable_id,
                'type' => 'purchasable',
                'preview' => $cartLine->preview,
                'description' => $cartLine->description,
                'identifier' => $cartLine->identifier,
                'unit_price' => $cartLine->unit_price,
                'unit_quantity' => 1,
                'quantity' => $cartLine->quantity,
                'sub_total' => $cartLine->sub_total,
                'total' => $cartLine->total,
                'meta' => $cartLine->meta,
            ]);
        }

        // Note: Gift voucher usage will be recorded when payment is successful
        // This prevents vouchers from being marked as used when payment fails
        
        return $order;
    }

    /**
     * Record gift voucher and discount usage after successful payment
     */
    public static function recordDiscountUsageForOrder(Order $order): void
    {
        // Record gift voucher usage for applied gift vouchers
        if (!empty($order->gift_voucher_breakdown)) {
            foreach ($order->gift_voucher_breakdown as $voucher) {
                // Find the gift voucher and record usage
                $giftVoucher = \Elevate\CommerceCore\Models\GiftVoucher::find($voucher['gift_voucher_id']);
                if ($giftVoucher) {
                    \Elevate\CommerceCore\Models\GiftVoucherUsage::create([
                        'gift_voucher_id' => $giftVoucher->id,
                        'code' => $giftVoucher->code,
                        'used_by_user_id' => $order->user_id,
                        'used_in_order_id' => $order->id,
                        'voucher_value' => $giftVoucher->value,
                        'discount_applied' => $voucher['amount'],
                        'used_at' => now(),
                    ]);
                    
                    // Increment usage count
                    $giftVoucher->incrementUsage();
                }
            }
        }

        // Record discount usage for applied discounts
        if (!empty($order->discount_breakdown)) {
            foreach ($order->discount_breakdown as $discountData) {
                if (isset($discountData['discount_id'])) {
                    \Elevate\CommerceCore\Models\DiscountUsage::create([
                        'discount_id' => $discountData['discount_id'],
                        'coupon_code' => $discountData['code'],
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'discount_amount' => $discountData['amount'] / 100, // Convert cents to pounds
                    ]);
                }
            }
        }
    }
}
