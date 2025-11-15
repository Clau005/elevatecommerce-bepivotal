<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Order Model
 * 
 * @property int $id
 * @property string $order_number (e.g., ORD-20231115-0001)
 * @property int|null $user_id
 * @property string|null $guest_email
 * @property string $status
 * @property int $subtotal (in smallest currency unit)
 * @property int $tax (in smallest currency unit)
 * @property int $shipping (in smallest currency unit)
 * @property int $discount (in smallest currency unit)
 * @property int $total (in smallest currency unit)
 * @property string $currency_code (e.g., USD, EUR)
 * @property string|null $payment_method
 * @property string|null $payment_status
 * @property string|null $payment_transaction_id
 * @property string|null $shipping_method
 * @property string|null $tracking_number
 * @property string|null $customer_note
 * @property string|null $admin_note
 * @property array|null $metadata
 * @property \Carbon\Carbon|null $paid_at
 * @property \Carbon\Carbon|null $shipped_at
 * @property \Carbon\Carbon|null $delivered_at
 * @property \Carbon\Carbon|null $cancelled_at
 * @property \Carbon\Carbon|null $refunded_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'guest_email',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'currency_code',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'shipping_method',
        'tracking_number',
        'customer_note',
        'admin_note',
        'metadata',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'refunded_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'tax' => 'integer',
        'shipping' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Get all order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get order addresses (shipping and billing)
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    /**
     * Get shipping address
     */
    public function shippingAddress()
    {
        return $this->addresses()->where('type', 'shipping')->first();
    }

    /**
     * Get billing address
     */
    public function billingAddress()
    {
        return $this->addresses()->where('type', 'billing')->first();
    }

    /**
     * Get order timeline/history
     */
    public function timeline(): HasMany
    {
        return $this->hasMany(OrderTimeline::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if order is from a guest
     */
    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Get customer email (user or guest)
     */
    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user ? $this->user->email : $this->guest_email;
    }

    /**
     * Get customer name (user or from shipping address)
     */
    public function getCustomerNameAttribute(): ?string
    {
        if ($this->user) {
            return $this->user->first_name . ' ' . $this->user->last_name;
        }

        $shippingAddress = $this->shippingAddress();
        return $shippingAddress ? $shippingAddress->first_name . ' ' . $shippingAddress->last_name : null;
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): float
    {
        return $this->subtotal / 100;
    }

    /**
     * Get formatted tax
     */
    public function getFormattedTaxAttribute(): float
    {
        return $this->tax / 100;
    }

    /**
     * Get formatted shipping
     */
    public function getFormattedShippingAttribute(): float
    {
        return $this->shipping / 100;
    }

    /**
     * Get formatted discount
     */
    public function getFormattedDiscountAttribute(): float
    {
        return $this->discount / 100;
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): float
    {
        return $this->total / 100;
    }

    /**
     * Get total item count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Update order status
     */
    public function updateStatus(string $status, ?string $note = null, ?int $userId = null): void
    {
        $oldStatus = $this->status;
        $this->status = $status;

        // Set timestamp fields based on status
        switch ($status) {
            case 'paid':
                $this->paid_at = now();
                break;
            case 'shipped':
                $this->shipped_at = now();
                break;
            case 'delivered':
                $this->delivered_at = now();
                break;
            case 'cancelled':
                $this->cancelled_at = now();
                break;
            case 'refunded':
                $this->refunded_at = now();
                break;
        }

        $this->save();

        // Log to timeline
        $this->logTimeline(
            'status_changed',
            "Status changed from {$oldStatus} to {$status}",
            $note,
            $userId
        );
    }

    /**
     * Log event to timeline
     */
    public function logTimeline(string $event, string $description, ?string $note = null, ?int $userId = null): void
    {
        $this->timeline()->create([
            'event' => $event,
            'description' => $description,
            'note' => $note,
            'user_id' => $userId,
            'metadata' => [
                'status' => $this->status,
                'payment_status' => $this->payment_status,
            ],
        ]);
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if order can be refunded
     */
    public function canBeRefunded(): bool
    {
        return in_array($this->status, ['paid', 'shipped', 'delivered']) && !$this->refunded_at;
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = config('purchasable.order.prefix', 'ORD');
        $date = now()->format('Ymd');
        
        // Get last order number for today
        $lastOrder = static::where('order_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $newNumber);
    }

    /**
     * Scope for user orders
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for guest orders
     */
    public function scopeForGuest($query, string $email)
    {
        return $query->where('guest_email', $email);
    }

    /**
     * Scope for status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = static::generateOrderNumber();
            }
            if (!$order->currency_code) {
                $order->currency_code = config('app.currency', 'USD');
            }
        });

        static::created(function ($order) {
            $order->logTimeline('order_created', 'Order created');
        });
    }
}
