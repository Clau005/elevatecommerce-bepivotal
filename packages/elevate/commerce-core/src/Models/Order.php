<?php

namespace Elevate\CommerceCore\Models;

use Elevate\CommerceCore\Models\User;
use Elevate\CommerceCore\Models\Staff;
use Elevate\CommerceCore\Models\Channel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'channel_id',
        'shipping_carrier_id',
        'stripe_checkout_session_id',
        'stripe_payment_intent',
        'new_customer',
        'status',
        'reference',
        'customer_reference',
        'sub_total',
        'discount_total',
        'discount_breakdown',
        'gift_voucher_total',
        'gift_voucher_breakdown',
        'shipping_breakdown',
        'tax_breakdown',
        'tax_total',
        'total',
        'notes',
        'currency_code',
        'compare_currency_code',
        'exchange_rate',
        'placed_at',
        'meta',
    ];

    protected $casts = [
        'new_customer' => 'boolean',
        'sub_total' => 'integer',
        'discount_total' => 'integer',
        'gift_voucher_total' => 'integer',
        'tax_total' => 'integer',
        'total' => 'integer',
        'discount_breakdown' => 'array',
        'gift_voucher_breakdown' => 'array',
        'shipping_breakdown' => 'array',
        'tax_breakdown' => 'array',
        'exchange_rate' => 'float',
        'placed_at' => 'datetime',
        'meta' => 'array',
    ];

    // ===== HELPER METHODS =====

    /**
     * Get total savings (discounts + gift vouchers)
     */
    public function getTotalSavingsAttribute(): int
    {
        return ($this->discount_total ?? 0) + ($this->gift_voucher_total ?? 0);
    }

    /**
     * Get formatted total savings
     */
    public function getFormattedTotalSavingsAttribute(): string
    {
        return '£' . number_format($this->total_savings / 100, 2);
    }

    /**
     * Get formatted discount total
     */
    public function getFormattedDiscountTotalAttribute(): string
    {
        return '£' . number_format(($this->discount_total ?? 0) / 100, 2);
    }

    /**
     * Get formatted gift voucher total
     */
    public function getFormattedGiftVoucherTotalAttribute(): string
    {
        return '£' . number_format(($this->gift_voucher_total ?? 0) / 100, 2);
    }

    /**
     * Check if order has discounts applied
     */
    public function hasDiscounts(): bool
    {
        return ($this->discount_total ?? 0) > 0;
    }

    /**
     * Check if order has gift vouchers applied
     */
    public function hasGiftVouchers(): bool
    {
        return ($this->gift_voucher_total ?? 0) > 0;
    }

    // ===== RELATIONSHIPS =====

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the channel for this order.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Get the order lines.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    /**
     * Get the order addresses.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    /**
     * Get the billing address for this order.
     */
    public function billingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'billing');
    }

    /**
     * Get the shipping address for this order.
     */
    public function shippingAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'shipping');
    }

    /**
     * Get the timeline entries for this order.
     */
    public function timelines(): HasMany
    {
        return $this->hasMany(OrderTimeline::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the discount usages for this order.
     */
    public function discountUsages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }


    /**
     * Get the shipping carrier used for this order.
     */
    public function shippingCarrier(): BelongsTo
    {
        return $this->belongsTo(\Elevate\Shipping\Models\ShippingCarrier::class, 'shipping_carrier_id');
    }

    /**
     * Generate a unique order reference.
     */
    public static function generateReference(): string
    {
        do {
            $reference = str_pad(random_int(1, 99999999), 8, '0', STR_PAD_LEFT);
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->reference) {
                $order->reference = self::generateReference();
            }

            // Set customer type based on previous orders
            if ($order->user_id) {
                $previousOrdersCount = self::where('user_id', $order->user_id)->count();
                $order->new_customer = $previousOrdersCount === 0;
            }
        });

        static::created(function ($order) {
            // Create timeline entry for order creation
            $order->timelines()->create([
                'type' => 'order_created',
                'title' => 'Order created',
                'content' => "Order {$order->reference} was created",
                'is_system_event' => true,
                'is_visible_to_customer' => true,
                'data' => [
                    'order_reference' => $order->reference,
                    'total' => $order->total,
                    'currency' => $order->currency_code,
                ],
            ]);
        });

        static::updated(function ($order) {
            $changes = $order->getChanges();
            
            // Create timeline entry for status changes
            if (isset($changes['status'])) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;
                
                $order->timelines()->create([
                    'type' => 'status_change',
                    'title' => 'Status updated',
                    'content' => "Status changed from {$oldStatus} to {$newStatus}",
                    'is_system_event' => true,
                    'is_visible_to_customer' => true,
                    'data' => [
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                    ],
                ]);
            }

            // Create general update timeline entry for other changes (excluding status and timestamps)
            $significantChanges = array_diff(array_keys($changes), ['status', 'updated_at']);
            if (!empty($significantChanges)) {
                $order->timelines()->create([
                    'type' => 'order_updated',
                    'title' => 'Order updated',
                    'content' => 'Order details were updated',
                    'is_system_event' => true,
                    'is_visible_to_customer' => false, // Usually internal updates
                    'data' => [
                        'changed_fields' => $significantChanges,
                    ],
                ]);
            }
        });
    }

    /**
     * Get the customer type (new/returning).
     */
    public function getCustomerTypeAttribute(): string
    {
        return $this->new_customer ? 'New' : 'Returning';
    }

    /**
     * Add a comment to the order timeline.
     */
    public function addComment(string $content, $author = null, bool $visibleToCustomer = true): OrderTimeline
    {
        $data = [
            'type' => 'comment',
            'content' => $content,
            'is_system_event' => false,
            'is_visible_to_customer' => $visibleToCustomer,
        ];

        // Determine who made the comment
        if ($author instanceof Staff) {
            $data['staff_id'] = $author->id;
        } elseif ($author instanceof User) {
            $data['user_id'] = $author->id;
        }

        return $this->timelines()->create($data);
    }

    /**
     * Add a system event to the order timeline.
     */
    public function addTimelineEvent(string $type, string $title, string $content = null, array $data = [], bool $visibleToCustomer = true): OrderTimeline
    {
        return $this->timelines()->create([
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'data' => $data,
            'is_system_event' => true,
            'is_visible_to_customer' => $visibleToCustomer,
        ]);
    }

    /**
     * Apply discounts to this order using the DiscountService
     */
    public function applyDiscounts(): void
    {
        $discountService = app(\Elevate\CommerceCore\Services\DiscountService::class);
        
        // Apply automatic discounts
        $automaticDiscounts = $discountService->applyAutomaticDiscounts($this);
        
        // Calculate totals with discounts
        $totals = $discountService->calculateOrderTotals($this, [$automaticDiscounts]);
        
        // Update order totals
        $this->update([
            'discount_total' => $totals['discount_total'],
            'discount_breakdown' => $totals['discount_breakdown'],
            'tax_total' => $totals['tax_total'],
            'total' => $totals['total'],
        ]);

        // Record discount usage
        if (!empty($automaticDiscounts['discounts'])) {
            $discountService->recordDiscountUsage($this, $automaticDiscounts['discounts']);
            
            // Add timeline event for applied discounts
            $discountNames = collect($automaticDiscounts['discounts'])
                ->pluck('discount.name')
                ->implode(', ');
                
            $this->addTimelineEvent(
                'discounts_applied',
                'Discounts Applied',
                "Automatic discounts applied: {$discountNames}",
                [
                    'discount_total' => $totals['discount_total'],
                    'discounts' => $totals['discount_breakdown'],
                ],
                true
            );
        }
    }

    /**
     * Apply a coupon code to this order
     */
    public function applyCoupon(string $couponCode): array
    {
        $discountService = app(\Elevate\CommerceCore\Services\DiscountService::class);
        
        $result = $discountService->applyCouponCode($couponCode, $this, $this->user);
        
        if ($result['success']) {
            // Calculate totals with coupon
            $totals = $discountService->calculateOrderTotals($this, [$result]);
            
            // Update order totals
            $this->update([
                'discount_total' => $totals['discount_total'],
                'discount_breakdown' => $totals['discount_breakdown'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
            ]);

            // Record discount usage
            $discountService->recordDiscountUsage($this, [$result]);
            
            // Add timeline event for coupon application
            $this->addTimelineEvent(
                'coupon_applied',
                'Coupon Applied',
                "Coupon code '{$couponCode}' applied: {$result['discount']->name}",
                [
                    'coupon_code' => $couponCode,
                    'discount_amount' => $result['amount'],
                    'discount_name' => $result['discount']->name,
                ],
                true
            );
        }
        
        return $result;
    }

    /**
     * Get applied discounts for this order
     */
    public function getAppliedDiscounts()
    {
        return $this->discountUsages()->with('discount')->get();
    }

    /**
     * Get total discount amount applied to this order
     */
    public function getTotalDiscountAmount(): float
    {
        return $this->discountUsages()->sum('discount_amount');
    }
}
