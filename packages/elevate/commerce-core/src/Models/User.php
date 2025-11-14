<?php

namespace Elevate\CommerceCore\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'company_name',
        'account_reference',
        'tax_identifier',
        'customer_group_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute(): string
    {
        $name = trim("{$this->first_name} {$this->last_name}");
        return $this->title ? "{$this->title} {$name}" : $name;
    }

    /**
     * Get the customer's name (alias for full_name).
     * This matches the frontend User interface expectation.
     */
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Get the carts for this user.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the orders for this user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the addresses for this user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Check if this is a new customer (no previous orders).
     */
    public function isNewCustomer(): bool
    {
        return $this->orders()->count() === 0;
    }

    /**
     * Get the customer type (new/returning).
     */
    public function getCustomerTypeAttribute(): string
    {
        return $this->isNewCustomer() ? 'New' : 'Returning';
    }

    /**
     * Get the customer group that this customer belongs to.
     */
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    /**
     * Generate a unique account reference for the customer.
     */
    public static function generateAccountReference(): string
    {
        do {
            $reference = 'CUST' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('account_reference', $reference)->exists());

        return $reference;
    }
}
