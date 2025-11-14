<?php

namespace Elevate\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the transaction
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\Elevate\CommerceCore\Models\Order::class);
    }

    /**
     * Get the refunds for the transaction
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * Check if transaction is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is refunded
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Mark transaction as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Mark transaction as refunded
     */
    public function markAsRefunded(): void
    {
        $this->update(['status' => 'refunded']);
    }

    /**
     * Get formatted amount with currency symbol
     */
    public function getFormattedAmountAttribute(): string
    {
        $currencyService = app(\Elevate\CommerceCore\Services\CurrencyService::class);
        // Amount is stored in smallest unit (cents/pence), format directly
        return $currencyService->format($this->amount, $this->currency);
    }
}
