<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'line_one',
        'line_two',
        'line_three',
        'city',
        'state',
        'postcode',
        'country_code',
        'delivery_instructions',
        'contact_email',
        'contact_phone',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full name for this address.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the formatted address lines.
     */
    public function getFormattedAddressAttribute(): array
    {
        $lines = [$this->line_one];
        
        if ($this->line_two) {
            $lines[] = $this->line_two;
        }
        
        if ($this->line_three) {
            $lines[] = $this->line_three;
        }
        
        return $lines;
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->formatted_address;
        $address[] = $this->city;
        
        if ($this->state) {
            $address[] = $this->state;
        }
        
        $address[] = $this->postcode;
        $address[] = $this->country_code;
        
        return implode(', ', $address);
    }

    /**
     * Scope to get default addresses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get addresses by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}