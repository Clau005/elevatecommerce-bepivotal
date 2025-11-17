<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * OrderTimeline Model
 * 
 * Logs all events and changes to an order.
 * Tracks both system and user actions.
 * 
 * @property int $id
 * @property int $order_id
 * @property string $event (e.g., order_created, status_changed, payment_received)
 * @property string $description
 * @property string|null $note (additional notes)
 * @property int|null $user_id (admin/staff who made the change, null for system)
 * @property string|null $user_type (admin, customer, system)
 * @property array|null $metadata (additional event data)
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class OrderTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'event',
        'description',
        'note',
        'user_id',
        'user_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the order that owns the timeline entry
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who created this entry (if any)
     */
    public function user(): BelongsTo
    {
        // This could be Admin or Customer depending on user_type
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Check if event was created by system
     */
    public function isSystemEvent(): bool
    {
        return $this->user_type === 'system' || $this->user_id === null;
    }

    /**
     * Check if event was created by admin
     */
    public function isAdminEvent(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if event was created by customer
     */
    public function isCustomerEvent(): bool
    {
        return $this->user_type === 'customer';
    }

    /**
     * Get formatted event name
     */
    public function getFormattedEventAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->event));
    }

    /**
     * Get actor name (who performed the action)
     */
    public function getActorNameAttribute(): string
    {
        if ($this->isSystemEvent()) {
            return 'System';
        }

        if ($this->user) {
            return $this->user->first_name . ' ' . $this->user->last_name;
        }

        return 'Unknown';
    }

    /**
     * Scope for specific event types
     */
    public function scopeEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope for system events
     */
    public function scopeSystemEvents($query)
    {
        return $query->where('user_type', 'system')->orWhereNull('user_id');
    }

    /**
     * Scope for admin events
     */
    public function scopeAdminEvents($query)
    {
        return $query->where('user_type', 'admin');
    }

    /**
     * Scope for customer events
     */
    public function scopeCustomerEvents($query)
    {
        return $query->where('user_type', 'customer');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($timeline) {
            if (!$timeline->user_type) {
                $timeline->user_type = $timeline->user_id ? 'admin' : 'system';
            }
        });
    }
}
