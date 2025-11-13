<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftVoucherUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gift_voucher_id',
        'code',
        'used_by_user_id',
        'used_in_order_id',
        'voucher_value',
        'discount_applied',
        'used_at',
    ];

    protected $casts = [
        'voucher_value' => 'integer',
        'discount_applied' => 'integer',
        'used_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function giftVoucher(): BelongsTo
    {
        return $this->belongsTo(GiftVoucher::class);
    }

    public function usedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    public function usedInOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'used_in_order_id');
    }

    // ===== HELPER METHODS =====

    public static function isVoucherUsed(string $code): bool
    {
        return static::where('code', strtoupper($code))->exists();
    }

    public static function getUsageCount(string $code): int
    {
        return static::where('code', strtoupper($code))->count();
    }

    public static function getUserUsageCount(string $code, int $userId): int
    {
        return static::where('code', strtoupper($code))
                    ->where('used_by_user_id', $userId)
                    ->count();
    }

    public function getFormattedVoucherValueAttribute(): string
    {
        return '£' . number_format($this->voucher_value / 100, 2);
    }

    public function getFormattedDiscountAppliedAttribute(): string
    {
        return '£' . number_format($this->discount_applied / 100, 2);
    }

    // ===== SCOPES =====

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('used_by_user_id', $userId);
    }

    public function scopeByOrder($query, int $orderId)
    {
        return $query->where('used_in_order_id', $orderId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('used_at', 'desc');
    }
}
