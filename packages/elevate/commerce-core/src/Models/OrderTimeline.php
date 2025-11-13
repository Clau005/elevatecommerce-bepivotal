<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'staff_id',
        'type',
        'title',
        'content',
        'data',
        'is_system_event',
        'is_visible_to_customer',
    ];

    protected $casts = [
        'data' => 'array',
        'is_system_event' => 'boolean',
        'is_visible_to_customer' => 'boolean',
    ];

    /**
     * Get the order this timeline entry belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user (customer) who made this entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the staff member who made this entry.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the author of this timeline entry (staff or customer).
     */
    public function getAuthorAttribute()
    {
        if ($this->staff_id) {
            return $this->staff;
        }
        
        if ($this->user_id) {
            return $this->user;
        }
        
        return null;
    }

    /**
     * Get the author name for display.
     */
    public function getAuthorNameAttribute(): string
    {
        if ($this->staff_id && $this->staff) {
            return $this->staff->full_name;
        }
        
        if ($this->user_id && $this->user) {
            return $this->user->full_name;
        }
        
        if ($this->is_system_event) {
            return 'System';
        }
        
        return 'Unknown';
    }

    /**
     * Check if this entry was made by staff.
     */
    public function isFromStaff(): bool
    {
        return !is_null($this->staff_id);
    }

    /**
     * Check if this entry was made by a customer.
     */
    public function isFromCustomer(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Scope to only show entries visible to customers.
     */
    public function scopeVisibleToCustomer($query)
    {
        return $query->where('is_visible_to_customer', true);
    }

    /**
     * Scope to only show system events.
     */
    public function scopeSystemEvents($query)
    {
        return $query->where('is_system_event', true);
    }

    /**
     * Scope to only show manual comments.
     */
    public function scopeComments($query)
    {
        return $query->where('is_system_event', false);
    }
}
