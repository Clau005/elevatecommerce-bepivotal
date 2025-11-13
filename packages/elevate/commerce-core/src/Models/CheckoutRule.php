<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckoutRule extends Model
{
    protected $fillable = [
        'name',
        'handle',
        'description',
        'type',
        'is_active',
        'priority',
        'threshold_amount',
        'threshold_quantity',
        'conditions',
        'action_type',
        'action_value',
        'action_config',
        'customer_groups',
        'product_categories',
        'excluded_products',
        'starts_at',
        'expires_at',
        'usage_limit',
        'usage_count',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'threshold_amount' => 'decimal:2',
        'threshold_quantity' => 'integer',
        'conditions' => 'json',
        'action_value' => 'decimal:2',
        'action_config' => 'json',
        'customer_groups' => 'json',
        'product_categories' => 'json',
        'excluded_products' => 'json',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'meta' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rule) {
            if (empty($rule->handle)) {
                $rule->handle = static::generateUniqueHandle($rule->name);
            }
        });

        static::updating(function ($rule) {
            if ($rule->isDirty('name') && empty($rule->handle)) {
                $rule->handle = static::generateUniqueHandle($rule->name);
            }
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->active()
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            });
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    // Methods
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        
        if ($this->starts_at && $this->starts_at->isAfter($now)) {
            return false;
        }
        
        if ($this->expires_at && $this->expires_at->isBefore($now)) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canApplyToOrder($order, $cart = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check order amount threshold
        if ($this->threshold_amount) {
            $orderTotal = is_array($order) ? ($order['total'] ?? 0) : $order->total;
            if ($orderTotal < $this->threshold_amount) {
                return false;
            }
        }

        // Check quantity threshold
        if ($this->threshold_quantity && $cart) {
            $totalQuantity = $cart->lines->sum('quantity');
            if ($totalQuantity < $this->threshold_quantity) {
                return false;
            }
        }

        // Check customer groups
        if ($this->customer_groups && !empty($this->customer_groups)) {
            $user = is_array($order) ? null : $order->user;
            if (!$user || !in_array($user->customer_group_id, $this->customer_groups)) {
                return false;
            }
        }

        return true;
    }

    public function applyRule($order, $cart = null): array
    {
        $result = [
            'applied' => false,
            'discount_amount' => 0,
            'free_shipping' => false,
            'message' => '',
            'breakdown' => [],
        ];

        if (!$this->canApplyToOrder($order, $cart)) {
            return $result;
        }

        $orderTotal = is_array($order) ? ($order['total'] ?? 0) : $order->total;

        switch ($this->action_type) {
            case 'free_shipping':
                $result['applied'] = true;
                $result['free_shipping'] = true;
                $result['message'] = "Free shipping applied: {$this->name}";
                break;

            case 'percentage_discount':
                $discountAmount = ($orderTotal * $this->action_value) / 100;
                $maxDiscount = $this->action_config['max_discount'] ?? null;
                if ($maxDiscount) {
                    $discountAmount = min($discountAmount, $maxDiscount);
                }
                $result['applied'] = true;
                $result['discount_amount'] = round($discountAmount, 2);
                $result['message'] = "{$this->action_value}% discount applied: {$this->name}";
                break;

            case 'fixed_discount':
                $discountAmount = min($this->action_value, $orderTotal);
                $result['applied'] = true;
                $result['discount_amount'] = round($discountAmount, 2);
                $result['message'] = "£{$this->action_value} discount applied: {$this->name}";
                break;
        }

        if ($result['applied']) {
            // Increment usage count
            $this->increment('usage_count');
        }

        return $result;
    }

    protected static function generateUniqueHandle(string $name): string
    {
        $handle = Str::slug($name);
        $originalHandle = $handle;
        $counter = 1;

        while (static::where('handle', $handle)->exists()) {
            $handle = $originalHandle . '-' . $counter;
            $counter++;
        }

        return $handle;
    }

    // Predefined rule types
    public static function getTypes(): array
    {
        return [
            'free_shipping_threshold' => [
                'label' => 'Free Shipping Threshold',
                'description' => 'Offer free shipping when order total exceeds amount',
                'fields' => ['threshold_amount'],
                'action_type' => 'free_shipping',
            ],
            'minimum_order_amount' => [
                'label' => 'Minimum Order Amount',
                'description' => 'Require minimum order amount',
                'fields' => ['threshold_amount'],
                'action_type' => 'percentage_discount',
            ],
            'quantity_discount' => [
                'label' => 'Quantity Discount',
                'description' => 'Discount based on total quantity',
                'fields' => ['threshold_quantity', 'action_value'],
                'action_type' => 'percentage_discount',
            ],
            'bulk_discount' => [
                'label' => 'Bulk Discount',
                'description' => 'Discount for large orders',
                'fields' => ['threshold_amount', 'action_value'],
                'action_type' => 'percentage_discount',
            ],
        ];
    }

    public static function getActionTypes(): array
    {
        return [
            'free_shipping' => 'Free Shipping',
            'percentage_discount' => 'Percentage Discount',
            'fixed_discount' => 'Fixed Amount Discount',
            'gift_item' => 'Gift Item',
            'upgrade_shipping' => 'Upgrade Shipping',
        ];
    }
}
