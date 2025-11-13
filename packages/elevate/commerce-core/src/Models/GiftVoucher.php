<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GiftVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'value',
        'image_url',
        'status',
        'valid_from',
        'valid_until',
        'usage_limit',
        'usage_count',
        'per_customer_limit',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'integer',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'per_customer_limit' => 'integer',
        'sort_order' => 'integer',
        'is_featured' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'meta_keywords' => 'array',
    ];

    protected $appends = [
        'formatted_value',
        'is_valid',
        'is_expired',
        'usage_percentage',
        'remaining_uses',
    ];

    // ===== RELATIONSHIPS =====

    public function usages(): HasMany
    {
        return $this->hasMany(GiftVoucherUsage::class);
    }

    // ===== ACCESSORS =====

    public function getFormattedValueAttribute(): string
    {
        return '£' . number_format($this->value / 100, 2);
    }

    public function getIsValidAttribute(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();
        
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until && now()->gt($this->valid_until);
    }

    public function getUsagePercentageAttribute(): ?float
    {
        if (!$this->usage_limit) {
            return null;
        }

        return ($this->usage_count / $this->usage_limit) * 100;
    }

    public function getRemainingUsesAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }

        return max(0, $this->usage_limit - $this->usage_count);
    }

    // ===== MUTATORS =====

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    // ===== HELPER METHODS =====

    /**
     * Check if the gift voucher is valid (method version for service compatibility)
     */
    public function isValid(): bool
    {
        return $this->is_valid;
    }

    /**
     * Check if the gift voucher has been used
     */
    public function isUsed(): bool
    {
        return $this->usages()->exists();
    }

    /**
     * Check if the gift voucher can be used by a specific user or customer ID
     */
    public function canBeUsed($userIdOrUser = null): bool
    {
        if (!$this->is_valid) {
            return false;
        }

        // Handle both User object and user ID
        $userId = null;
        if ($userIdOrUser instanceof User) {
            $userId = $userIdOrUser->id;
        } elseif (is_numeric($userIdOrUser)) {
            $userId = $userIdOrUser;
        }

        // Check per-customer limit if user ID is provided
        if ($userId && $this->per_customer_limit) {
            $userUsageCount = $this->usages()
                ->where('used_by_user_id', $userId)
                ->count();
                
            if ($userUsageCount >= $this->per_customer_limit) {
                return false;
            }
        }

        return true;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function decrementUsage(): void
    {
        if ($this->usage_count > 0) {
            $this->decrement('usage_count');
        }
    }

    public static function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('status', 'active')
                    ->where(function ($q) use ($now) {
                        $q->whereNull('valid_from')
                          ->orWhere('valid_from', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>=', $now);
                    })
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('usage_count < usage_limit');
                    });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}
