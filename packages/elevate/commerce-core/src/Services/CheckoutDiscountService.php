<?php

namespace Elevate\CommerceCore\Services;

use App\Models\Discount;
use App\Models\CheckoutRule;
use Elevate\CommerceCore\Models\Order;
use Elevate\CommerceCore\Models\Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CheckoutDiscountService
{
    protected DiscountService $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }
    /**
     * Apply all applicable discounts and checkout rules to an order.
     */
    public function applyDiscountsAndRules(Order $order, Cart $cart = null, string $couponCode = null): array
    {
        $results = [
            'discounts_applied' => [],
            'rules_applied' => [],
            'total_discount_amount' => 0,
            'free_shipping' => false,
            'messages' => [],
            'breakdown' => [],
        ];

        // Apply coupon code discount first (if provided)
        if ($couponCode) {
            $couponResult = $this->applyCouponDiscount($order, $couponCode);
            if ($couponResult['applied']) {
                $results['discounts_applied'][] = $couponResult;
                $results['total_discount_amount'] += $couponResult['discount_amount'];
                $results['messages'][] = $couponResult['message'];
                $results['breakdown']['coupon'] = $couponResult;
            }
        }

        // Apply automatic discounts
        $automaticDiscounts = $this->getApplicableAutomaticDiscounts($order);
        foreach ($automaticDiscounts as $discount) {
            $discountResult = $discount->calculateDiscount($order->total / 100); // Convert cents to pounds
            if ($discountResult['discount_amount'] > 0) {
                $discountResult['applied'] = true;
                $discountResult['discount'] = $discount;
                $discountResult['message'] = "Automatic discount applied: {$discount->name}";
                
                $results['discounts_applied'][] = $discountResult;
                $results['total_discount_amount'] += $discountResult['discount_amount'];
                $results['messages'][] = $discountResult['message'];
                
                if (isset($discountResult['breakdown']['free_shipping'])) {
                    $results['free_shipping'] = true;
                }
            }
        }

        // Apply checkout rules
        $checkoutRules = $this->getApplicableCheckoutRules($order, $cart);
        foreach ($checkoutRules as $rule) {
            $ruleResult = $rule->applyRule($order, $cart);
            if ($ruleResult['applied']) {
                $results['rules_applied'][] = $ruleResult;
                $results['total_discount_amount'] += $ruleResult['discount_amount'];
                $results['messages'][] = $ruleResult['message'];
                $results['breakdown']['rules'][] = $ruleResult;
                
                if ($ruleResult['free_shipping']) {
                    $results['free_shipping'] = true;
                }
            }
        }

        // Note: Gift vouchers are now handled via coupon codes in applyCouponDiscount()
        // No automatic cart-based gift voucher application needed

        // Log the discount application
        Log::info('Discounts and rules applied to order', [
            'order_id' => $order->id,
            'total_discount_amount' => $results['total_discount_amount'],
            'free_shipping' => $results['free_shipping'],
            'discounts_count' => count($results['discounts_applied']),
            'rules_count' => count($results['rules_applied']),
        ]);

        return $results;
    }

    /**
     * Apply a coupon code discount (includes gift vouchers).
     */
    public function applyCouponDiscount(Order $order, string $couponCode): array
    {
        // Use the enhanced DiscountService which handles both regular coupons and gift vouchers
        $result = $this->discountService->applyCouponCode($couponCode, $order, $order->user);

        if ($result['success']) {
            if (isset($result['discount'])) {
                // Regular discount coupon
                return [
                    'applied' => true,
                    'discount' => $result['discount'],
                    'discount_amount' => $result['amount'] / 100, // Convert cents to pounds for display
                    'message' => "Coupon '{$couponCode}' applied successfully",
                    'breakdown' => $result['breakdown'],
                ];
            } elseif (isset($result['gift_voucher'])) {
                // New GiftVoucher model
                return [
                    'applied' => true,
                    'gift_voucher' => $result['gift_voucher'],
                    'discount_amount' => $result['amount'] / 100, // Convert cents to pounds for display
                    'message' => "Gift voucher '{$couponCode}' applied successfully",
                    'breakdown' => $result['breakdown'],
                    'voucher_value' => $result['voucher_value'] / 100, // Convert cents to pounds
                    'voucher_type' => 'new_model',
                ];
            }
        }

        return [
            'applied' => false,
            'error' => $result['message'] ?? 'Coupon could not be applied',
            'discount_amount' => 0,
        ];
    }

    /**
     * Get applicable automatic discounts.
     */
    protected function getApplicableAutomaticDiscounts(Order $order): Collection
    {
        return Discount::automatic()
            ->valid()
            ->where(function ($query) use ($order) {
                $query->whereNull('minimum_order_amount')
                      ->orWhere('minimum_order_amount', '<=', $order->total / 100);
            })
            ->orderBy('priority', 'desc')
            ->get();
    }

    /**
     * Get applicable checkout rules.
     */
    protected function getApplicableCheckoutRules(Order $order, Cart $cart = null): Collection
    {
        return CheckoutRule::valid()
            ->byPriority()
            ->get()
            ->filter(function ($rule) use ($order, $cart) {
                return $rule->canApplyToOrder($order, $cart);
            });
    }


    /**
     * Calculate shipping cost with free shipping rules applied.
     */
    public function calculateShipping(Order $order, Cart $cart = null, bool $freeShippingApplied = false): array
    {
        if ($freeShippingApplied) {
            return [
                'shipping_cost' => 0,
                'free_shipping' => true,
                'message' => 'Free shipping applied',
            ];
        }

        // Check for free shipping checkout rules
        $freeShippingRules = CheckoutRule::valid()
            ->byType('free_shipping_threshold')
            ->get()
            ->filter(function ($rule) use ($order, $cart) {
                return $rule->canApplyToOrder($order, $cart);
            });

        if ($freeShippingRules->isNotEmpty()) {
            return [
                'shipping_cost' => 0,
                'free_shipping' => true,
                'message' => 'Free shipping threshold met',
            ];
        }

        // Default shipping calculation (you can customize this)
        $baseShippingCost = 5.99; // £5.99 base shipping
        
        return [
            'shipping_cost' => $baseShippingCost,
            'free_shipping' => false,
            'message' => "Standard shipping: £{$baseShippingCost}",
        ];
    }

    /**
     * Validate a coupon code before applying (includes gift vouchers).
     */
    public function validateCouponCode(string $couponCode, Order $order = null, int $userId = null): array
    {
        // Create a temporary order if none provided for validation
        if (!$order && $userId) {
            $order = new Order(['user_id' => $userId, 'sub_total' => 10000]); // £100 for estimation
        }

        $user = $userId ? \Elevate\CommerceCore\Models\User::find($userId) : null;
        
        // Use the enhanced DiscountService which handles both regular coupons and gift vouchers
        $result = $this->discountService->applyCouponCode($couponCode, $order, $user);

        if ($result['success']) {
            if (isset($result['discount'])) {
                // Regular discount coupon
                return [
                    'valid' => true,
                    'discount' => $result['discount'],
                    'estimated_discount' => $result['amount'] / 100, // Convert cents to pounds
                    'message' => "Coupon '{$couponCode}' is valid",
                ];
            } elseif (isset($result['gift_voucher'])) {
                // New GiftVoucher model
                return [
                    'valid' => true,
                    'gift_voucher' => $result['gift_voucher'],
                    'estimated_discount' => $result['amount'] / 100, // Convert cents to pounds
                    'voucher_value' => $result['voucher_value'] / 100, // Convert cents to pounds
                    'message' => "Gift voucher '{$couponCode}' is valid",
                    'voucher_type' => 'new_model',
                ];
            }
        }

        return [
            'valid' => false,
            'error' => $result['message'] ?? 'Invalid coupon code',
        ];
    }
}
