<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_reference',
        'card_reference',
        'stripe_payment_intent_id',
        'stripe_payment_method_id',
        'stripe_charge_id',
        'status',
        'type',
        'currency',
        'amount',
        'amount_authorized',
        'amount_captured',
        'amount_received',
        'amount_refunded',
        'failure_code',
        'failure_message',
        'gateway_message',
        'gateway_data',
        'stripe_data',
        'meta',
        'authorized_at',
        'captured_at',
        'processed_at',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'amount_authorized' => 'integer',
        'amount_captured' => 'integer',
        'amount_received' => 'integer',
        'amount_refunded' => 'integer',
        'gateway_data' => 'array',
        'stripe_data' => 'array',
        'meta' => 'array',
        'authorized_at' => 'datetime',
        'captured_at' => 'datetime',
        'processed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if payment was successful.
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, ['succeeded', 'completed']);
    }

    /**
     * Check if payment failed.
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'canceled', 'declined']);
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing', 'requires_action', 'requires_payment_method']);
    }

    /**
     * Check if payment is authorized but not captured.
     */
    public function isAuthorized(): bool
    {
        return $this->status === 'authorized' && $this->amount_captured < $this->amount_authorized;
    }

    /**
     * Check if payment can be captured.
     */
    public function canBeCaptured(): bool
    {
        return $this->isAuthorized() && $this->expires_at && $this->expires_at->isFuture();
    }

    /**
     * Check if payment requires redirect (3DS, etc.).
     */
    public function requiresRedirect(): bool
    {
        return $this->status === 'requires_action';
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '£' . number_format($this->amount / 100, 2);
    }

    /**
     * Get formatted amount received.
     */
    public function getFormattedAmountReceivedAttribute(): string
    {
        return '£' . number_format(($this->amount_received ?? 0) / 100, 2);
    }

    /**
     * Get formatted amount refunded.
     */
    public function getFormattedAmountRefundedAttribute(): string
    {
        return '£' . number_format($this->amount_refunded / 100, 2);
    }

    /**
     * Check if payment can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->isSuccessful() && $this->amount_refunded < $this->amount_received;
    }

    /**
     * Get remaining refundable amount.
     */
    public function getRefundableAmount(): int
    {
        if (!$this->canBeRefunded()) {
            return 0;
        }

        return ($this->amount_received ?? 0) - $this->amount_refunded;
    }

    /**
     * Get formatted refundable amount.
     */
    public function getFormattedRefundableAmountAttribute(): string
    {
        return '£' . number_format($this->getRefundableAmount() / 100, 2);
    }

    /**
     * Scope to successful payments.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'succeeded');
    }

    /**
     * Scope to failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'canceled']);
    }

    /**
     * Scope to pending payments.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'processing', 'requires_action', 'requires_payment_method']);
    }

    /**
     * Scope to payments for a specific order.
     */
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }
}