<?php

namespace Elevate\CommerceCore\Services;

use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\GiftVoucher;
use Elevate\CommerceCore\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DiscountService
{
    /**
     * Apply automatic discounts to an order
     */
    public function applyAutomaticDiscounts(Order $order): array
    {
        $customer = $order->user;
        $context = $this->buildDiscountContext($order, $customer);
        
        // Get all valid automatic discounts ordered by priority
        $automaticDiscounts = Discount::valid()
            ->automatic()
            ->orderBy('priority', 'desc')
            ->with('rules')
            ->get();

        $appliedDiscounts = [];
        $totalDiscount = 0;
        $freeShipping = false;

        foreach ($automaticDiscounts as $discount) {
            // Check if discount can be used
            if (!$discount->canBeUsed($customer?->id)) {
                continue;
            }

            // Check if discount rules pass
            if (!$this->evaluateDiscountRules($discount, $context)) {
                continue;
            }

            // Check if we can combine with other discounts
            if (!empty($appliedDiscounts) && !$discount->combine_with_other_discounts) {
                continue;
            }

            // Calculate discount amount
            $calculation = $discount->calculateDiscount($order->sub_total, $this->getOrderItems($order));
            
            if ($calculation['discount_amount'] > 0 || isset($calculation['breakdown']['free_shipping'])) {
                $appliedDiscounts[] = [
                    'discount' => $discount,
                    'amount' => $calculation['discount_amount'],
                    'breakdown' => $calculation['breakdown'],
                ];

                $totalDiscount += $calculation['discount_amount'];
                
                if (isset($calculation['breakdown']['free_shipping'])) {
                    $freeShipping = true;
                }

                // If this discount doesn't combine with others, stop here
                if (!$discount->combine_with_other_discounts) {
                    break;
                }
            }
        }

        return [
            'discounts' => $appliedDiscounts,
            'total_discount' => $totalDiscount,
            'free_shipping' => $freeShipping,
        ];
    }

    /**
     * Validate and apply a coupon code to an order
     */
    public function applyCouponCode(string $couponCode, Order $order, ?User $customer = null): array
    {
        // First, try to find a regular discount with this coupon code
        $discount = Discount::where('coupon_code', $couponCode)
            ->valid()
            ->first();

        if ($discount) {
            if (!$discount->canBeUsed($customer?->id)) {
                return [
                    'success' => false,
                    'message' => 'Coupon usage limit exceeded',
                ];
            }

            // Check minimum order amount
            if ($discount->minimum_order_amount && $order->sub_total < $discount->minimum_order_amount) {
                return [
                    'success' => false,
                    'message' => "Minimum order amount of £{$discount->minimum_order_amount} required",
                ];
            }

            $context = $this->buildDiscountContext($order, $customer);

            // Check if discount rules pass
            if (!$this->evaluateDiscountRules($discount, $context)) {
                return [
                    'success' => false,
                    'message' => 'This coupon is not valid for your current order',
                ];
            }

            // Calculate discount
            $calculation = $discount->calculateDiscount($order->sub_total, $this->getOrderItems($order));

            return [
                'success' => true,
                'discount' => $discount,
                'amount' => $calculation['discount_amount'],
                'breakdown' => $calculation['breakdown'],
            ];
        }

        // If no regular discount found, check for gift vouchers by SKU
        return $this->applyGiftVoucher($couponCode, $order, $customer);
    }

    /**
     * Apply a gift voucher by code (new GiftVoucher model only)
     */
    protected function applyGiftVoucher(string $code, Order $order, ?User $customer = null): array
    {
        // Use only the new GiftVoucher model
        return $this->applyNewGiftVoucher($code, $order, $customer);
    }

    /**
     * Apply new GiftVoucher model voucher
     */
    protected function applyNewGiftVoucher(string $code, Order $order, ?User $customer = null): array
    {
        // Find gift voucher by code
        $giftVoucher = GiftVoucher::where('code', $code)
            ->where('status', 'active')
            ->first();

        if (!$giftVoucher) {
            return [
                'success' => false,
                'message' => 'Gift voucher not found',
            ];
        }

        // Check if voucher is valid (dates)
        if (!$giftVoucher->isValid()) {
            return [
                'success' => false,
                'message' => 'This gift voucher has expired or is not yet valid',
            ];
        }

        // Check if voucher has been used
        if ($giftVoucher->isUsed()) {
            return [
                'success' => false,
                'message' => 'This gift voucher has already been used',
            ];
        }

        // Check usage limits
        if (!$giftVoucher->canBeUsed($customer?->id)) {
            return [
                'success' => false,
                'message' => 'Gift voucher usage limit exceeded',
            ];
        }

        // Calculate discount amount (gift voucher value, but not more than order total)
        $voucherValue = $giftVoucher->value; // Already in cents
        $orderTotal = $order->sub_total ?? $order->total ?? 0; // Already in cents
        
        // Debug logging
        \Log::info('Gift voucher calculation', [
            'voucher_code' => $code,
            'voucher_value' => $voucherValue,
            'order_sub_total' => $order->sub_total,
            'order_total' => $order->total,
            'order_total_used' => $orderTotal,
        ]);
        
        $discountAmount = min($voucherValue, $orderTotal);

        return [
            'success' => true,
            'gift_voucher' => $giftVoucher,
            'amount' => $discountAmount,
            'voucher_value' => $voucherValue,
            'voucher_type' => 'new_model',
            'breakdown' => [
                'gift_voucher' => [
                    'code' => $code,
                    'value' => $voucherValue,
                    'applied' => $discountAmount,
                ]
            ],
        ];
    }



    /**
     * Record gift voucher usage when an order is completed (new GiftVoucher model only)
     */
    public function recordGiftVoucherUsage(Order $order, array $giftVoucherData): void
    {
        if (isset($giftVoucherData['gift_voucher'])) {
            // New GiftVoucher model
            $giftVoucher = $giftVoucherData['gift_voucher'];
            
            \Elevate\CommerceCore\Models\GiftVoucherUsage::create([
                'gift_voucher_id' => $giftVoucher->id,
                'code' => $giftVoucher->code,
                'used_by_user_id' => $order->user_id,
                'used_in_order_id' => $order->id,
                'voucher_value' => $giftVoucherData['voucher_value'],
                'discount_applied' => $giftVoucherData['amount'],
                'used_at' => now(),
            ]);
        }
    }

    /**
     * Record discount usage when an order is completed
     */
    public function recordDiscountUsage(Order $order, array $appliedDiscounts): void
    {
        foreach ($appliedDiscounts as $discountData) {
            $discount = $discountData['discount'];
            
            DiscountUsage::create([
                'discount_id' => $discount->id,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'coupon_code' => $discount->coupon_code,
                'discount_amount' => $discountData['amount'],
            ]);
        }
    }

    /**
     * Record all discount and gift voucher usage when an order is completed
     */
    public function recordAllDiscountUsage(Order $order, array $appliedDiscountsAndVouchers): void
    {
        foreach ($appliedDiscountsAndVouchers as $item) {
            if (isset($item['discount'])) {
                // Regular discount
                DiscountUsage::create([
                    'discount_id' => $item['discount']->id,
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'coupon_code' => $item['discount']->coupon_code,
                    'discount_amount' => $item['amount'],
                ]);
            } elseif (isset($item['gift_voucher'])) {
                // Gift voucher (new model only)
                $this->recordGiftVoucherUsage($order, $item);
            }
        }
    }

    /**
     * Build context array for discount rule evaluation
     */
    protected function buildDiscountContext(Order $order, ?User $customer = null): array
    {
        $context = [
            'total_amount' => $order->sub_total,
            'total_quantity' => $this->getTotalQuantity($order),
            'order_id' => $order->id,
        ];

        if ($customer) {
            $context['customer_id'] = $customer->id;
            $context['customer_group_id'] = $customer->customer_group_id;
            $context['customer_email'] = $customer->email;
            $context['order_count'] = $this->getCustomerOrderCount($customer);
            $context['first_order'] = $this->isFirstOrder($customer);
        }

        // Add product-related context
        $orderItems = $this->getOrderItems($order);
        if ($orderItems->isNotEmpty()) {
            $context['product_ids'] = $orderItems->pluck('purchasable_id')->unique()->values()->toArray();
            $context['product_types'] = $orderItems->pluck('purchasable_type')->unique()->values()->toArray();
            
            // Get product tags if available (this would need to be implemented based on your product structure)
            $context['product_tags'] = $this->getProductTags($orderItems);
        }

        return $context;
    }

    /**
     * Evaluate if discount rules pass for given context
     */
    protected function evaluateDiscountRules(Discount $discount, array $context): bool
    {
        // If no rules, discount applies to everyone
        if ($discount->rules->isEmpty()) {
            return true;
        }

        // All rules must pass (AND logic)
        foreach ($discount->rules as $rule) {
            if (!$rule->passes($context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get order items (lines) for discount calculation
     */
    protected function getOrderItems(Order $order): Collection
    {
        return $order->lines ?? collect();
    }

    /**
     * Get total quantity of items in order
     */
    protected function getTotalQuantity(Order $order): int
    {
        return $this->getOrderItems($order)->sum('quantity');
    }

    /**
     * Get customer's total order count
     */
    protected function getCustomerOrderCount(User $customer): int
    {
        return $customer->orders()->count();
    }

    /**
     * Check if this is customer's first order
     */
    protected function isFirstOrder(User $customer): bool
    {
        return $this->getCustomerOrderCount($customer) === 0;
    }

    /**
     * Get product tags from order items
     * This would need to be implemented based on your product structure
     */
    protected function getProductTags(Collection $orderItems): array
    {
        $tags = [];
        
        foreach ($orderItems as $item) {
            // This assumes you have a way to get tags from purchasable items
            // You might need to implement this based on your specific product structure
            if (method_exists($item->purchasable, 'tags')) {
                $itemTags = $item->purchasable->tags->pluck('handle')->toArray();
                $tags = array_merge($tags, $itemTags);
            }
        }

        return array_unique($tags);
    }

    /**
     * Calculate final order totals with discounts applied
     */
    public function calculateOrderTotals(Order $order, array $discountResults = []): array
    {
        $subTotal = $order->sub_total;
        $discountTotal = 0;
        $freeShipping = false;
        $discountBreakdown = [];

        foreach ($discountResults as $result) {
            if (isset($result['discounts'])) {
                // Multiple discounts (automatic)
                foreach ($result['discounts'] as $discountData) {
                    $discountTotal += $discountData['amount'];
                    $discountBreakdown[] = [
                        'discount_id' => $discountData['discount']->id,
                        'name' => $discountData['discount']->name,
                        'type' => $discountData['discount']->type,
                        'amount' => $discountData['amount'],
                    ];
                }
                if ($result['free_shipping']) {
                    $freeShipping = true;
                }
            } elseif (isset($result['gift_voucher'])) {
                // New GiftVoucher model
                $discountTotal += $result['amount'];
                $discountBreakdown[] = [
                    'gift_voucher_id' => $result['gift_voucher']->id,
                    'name' => 'Gift Voucher: ' . $result['gift_voucher']->code,
                    'type' => 'gift_voucher',
                    'amount' => $result['amount'],
                    'voucher_value' => $result['voucher_value'],
                ];
            } else {
                // Single discount (coupon)
                $discountTotal += $result['amount'];
                $discountBreakdown[] = [
                    'discount_id' => $result['discount']->id,
                    'name' => $result['discount']->name,
                    'type' => $result['discount']->type,
                    'amount' => $result['amount'],
                ];
                if (isset($result['breakdown']['free_shipping'])) {
                    $freeShipping = true;
                }
            }
        }

        // Calculate shipping (this would integrate with your shipping system)
        $shippingTotal = $freeShipping ? 0 : $this->calculateShipping($order);

        // Calculate tax (this would integrate with your tax system)
        $taxableAmount = $subTotal - $discountTotal;
        $taxTotal = $this->calculateTax($taxableAmount, $order);

        $total = $subTotal - $discountTotal + $shippingTotal + $taxTotal;

        return [
            'sub_total' => $subTotal,
            'discount_total' => $discountTotal,
            'discount_breakdown' => $discountBreakdown,
            'shipping_total' => $shippingTotal,
            'tax_total' => $taxTotal,
            'total' => max(0, $total), // Ensure total never goes negative
            'free_shipping' => $freeShipping,
        ];
    }

    /**
     * Calculate shipping costs (placeholder - implement based on your shipping system)
     */
    protected function calculateShipping(Order $order): float
    {
        // This would integrate with your shipping calculation logic
        // For now, shipping is free
        return 0.0;
    }

    /**
     * Calculate tax (placeholder - implement based on your tax system)
     */
    protected function calculateTax(float $taxableAmount, Order $order): float
    {
        // Tax-inclusive pricing: prices already include tax, so no additional tax is added
        // This would integrate with your tax calculation logic if needed
        return 0.0;
    }

    /**
     * Get all available discounts for a customer (for frontend display)
     */
    public function getAvailableDiscounts(?User $customer = null): Collection
    {
        return Discount::valid()
            ->where(function ($query) use ($customer) {
                $query->where('is_automatic', true)
                      ->orWhere(function ($q) {
                          $q->where('is_automatic', false)
                            ->whereNotNull('coupon_code');
                      });
            })
            ->when($customer, function ($query, $customer) {
                // Filter out discounts that have reached usage limits for this customer
                return $query->whereDoesntHave('usages', function ($q) use ($customer) {
                    $q->where('user_id', $customer->id)
                      ->whereColumn('discount_usages.discount_id', 'discounts.id')
                      ->havingRaw('COUNT(*) >= discounts.usage_limit_per_customer')
                      ->whereNotNull('discounts.usage_limit_per_customer');
                });
            })
            ->orderBy('priority', 'desc')
            ->get();
    }
}
