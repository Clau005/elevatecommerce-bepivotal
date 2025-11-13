<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'handle',
        'description',
        'type',
        'value',
        'coupon_code',
        'is_active',
        'is_automatic',
        'usage_limit',
        'usage_limit_per_customer',
        'minimum_order_amount',
        'maximum_discount_amount',
        'starts_at',
        'expires_at',
        'priority',
        'buy_x_get_y_config',
        'combine_with_other_discounts',
        'affiliation_id',
        'event_type_id',
        'apply_to',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_automatic' => 'boolean',
        'usage_limit' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'minimum_order_amount' => 'integer',
        'maximum_discount_amount' => 'integer',
        // value: integer for fixed_amount (cents), float for percentage
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'priority' => 'integer',
        'buy_x_get_y_config' => 'json',
        'combine_with_other_discounts' => 'boolean',
        'meta' => 'json',
    ];

    // ===== HELPER METHODS =====

    /**
     * Get the formatted value for display
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        } elseif ($this->type === 'fixed_amount') {
            return '£' . number_format($this->value / 100, 2);
        } else {
            return 'N/A';
        }
    }

    /**
     * Get the value for form display (converts cents to pounds for fixed amounts)
     */
    public function getDisplayValueAttribute()
    {
        if ($this->type === 'fixed_amount') {
            return number_format($this->value / 100, 2, '.', '');
        }
        return $this->value;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($discount) {
            if (empty($discount->handle)) {
                $discount->handle = static::generateUniqueHandle($discount->name);
            }
        });

        static::updating(function ($discount) {
            if ($discount->isDirty('name') && empty($discount->handle)) {
                $discount->handle = static::generateUniqueHandle($discount->name);
            }
        });
    }

    public function rules(): HasMany
    {
        return $this->hasMany(DiscountRule::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }

    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('is_automatic', true);
    }

    public function scopeWithCoupon($query)
    {
        return $query->whereNotNull('coupon_code');
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

    public function scopeForAffiliation($query, $affiliationId)
    {
        return $query->where('affiliation_id', $affiliationId);
    }

    public function scopeForEventType($query, $eventTypeId)
    {
        return $query->where('event_type_id', $eventTypeId);
    }

    public function scopeAffiliationDiscounts($query)
    {
        return $query->whereNotNull('affiliation_id');
    }

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

        return true;
    }

    public function hasUsageLimit(): bool
    {
        return $this->usage_limit !== null;
    }

    public function hasCustomerUsageLimit(): bool
    {
        return $this->usage_limit_per_customer !== null;
    }

    public function getRemainingUsages(): ?int
    {
        if (!$this->hasUsageLimit()) {
            return null;
        }

        return $this->usage_limit - $this->usages()->count();
    }

    public function getCustomerRemainingUsages($customerId): ?int
    {
        if (!$this->hasCustomerUsageLimit()) {
            return null;
        }

        $used = $this->usages()->where('user_id', $customerId)->count();
        return $this->usage_limit_per_customer - $used;
    }

    public function canBeUsed($customerId = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check global usage limit
        if ($this->hasUsageLimit() && $this->getRemainingUsages() <= 0) {
            return false;
        }

        // Check customer usage limit
        if ($customerId && $this->hasCustomerUsageLimit()) {
            if ($this->getCustomerRemainingUsages($customerId) <= 0) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($orderTotal, $orderItems = []): array
    {
        $discountAmount = 0;
        $breakdown = [];

        switch ($this->type) {
            case 'percentage':
                $discountAmount = ($orderTotal * $this->value) / 100;
                if ($this->maximum_discount_amount) {
                    $discountAmount = min($discountAmount, $this->maximum_discount_amount);
                }
                break;

            case 'fixed_amount':
                $discountAmount = min($this->value, $orderTotal);
                break;

            case 'free_shipping':
                // This would be handled in shipping calculation
                $discountAmount = 0;
                $breakdown['free_shipping'] = true;
                break;

            case 'buy_x_get_y':
                // This would require more complex logic based on items
                // For now, return 0 - implement based on specific requirements
                $discountAmount = 0;
                break;
        }

        return [
            'discount_amount' => round($discountAmount, 2),
            'breakdown' => $breakdown,
        ];
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

    public function getUsageCountAttribute(): int
    {
        return $this->usages()->count();
    }

    public function getCustomerUsageCountAttribute(): int
    {
        return $this->usages()->distinct('user_id')->count('user_id');
    }
}
