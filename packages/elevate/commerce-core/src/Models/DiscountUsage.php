<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountUsage extends Model
{
    protected $fillable = [
        'discount_id',
        'order_id',
        'user_id',
        'coupon_code',
        'discount_amount',
    ];

    protected $casts = [
        'discount_id' => 'integer',
        'order_id' => 'integer',
        'user_id' => 'integer',
        'discount_amount' => 'decimal:2',
    ];

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('user_id', $customerId);
    }

    public function scopeForDiscount($query, $discountId)
    {
        return $query->where('discount_id', $discountId);
    }

    public function scopeWithCoupon($query, $couponCode)
    {
        return $query->where('coupon_code', $couponCode);
    }
}
