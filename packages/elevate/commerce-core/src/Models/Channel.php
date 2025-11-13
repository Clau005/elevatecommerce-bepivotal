<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'handle',
        'url',
        'default',
    ];

    protected $casts = [
        'default' => 'boolean',
    ];

    /**
     * Get the orders for this channel.
     * Note: This will be properly resolved when orders package is loaded
     */
    public function orders(): HasMany
    {
        return $this->hasMany('Elevate\Orders\Models\Order');
    }

    /**
     * Get the carts for this channel.
     * Note: This will be properly resolved when cart package is loaded
     */
    public function carts(): HasMany
    {
        return $this->hasMany('Elevate\Cart\Models\Cart');
    }

    /**
     * Get the default channel.
     */
    public static function getDefault(): ?self
    {
        return static::where('default', true)->first();
    }

    /**
     * Set this channel as default and unset others.
     */
    public function setAsDefault(): void
    {
        static::where('default', true)->update(['default' => false]);
        $this->update(['default' => true]);
    }
}
