<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * OrderAddress Model
 * 
 * Stores shipping and billing addresses for orders.
 * Snapshot at time of order.
 * 
 * @property int $id
 * @property int $order_id
 * @property string $type (shipping or billing)
 * @property string $first_name
 * @property string $last_name
 * @property string|null $company
 * @property string $address_line_1
 * @property string|null $address_line_2
 * @property string $city
 * @property string|null $state
 * @property string $postal_code
 * @property string $country_code
 * @property string|null $phone
 * @property string|null $email
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country_code',
        'phone',
        'email',
    ];

    /**
     * Get the order that owns the address
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get formatted address (single line)
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get formatted address (multi-line)
     */
    public function getFormattedAddressLinesAttribute(): array
    {
        $lines = [];

        if ($this->company) {
            $lines[] = $this->company;
        }

        $lines[] = $this->full_name;
        $lines[] = $this->address_line_1;

        if ($this->address_line_2) {
            $lines[] = $this->address_line_2;
        }

        $cityLine = $this->city;
        if ($this->state) {
            $cityLine .= ', ' . $this->state;
        }
        $cityLine .= ' ' . $this->postal_code;
        $lines[] = $cityLine;

        $lines[] = $this->country_code;

        if ($this->phone) {
            $lines[] = 'Phone: ' . $this->phone;
        }

        return $lines;
    }

    /**
     * Scope for shipping addresses
     */
    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    /**
     * Scope for billing addresses
     */
    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }
}
