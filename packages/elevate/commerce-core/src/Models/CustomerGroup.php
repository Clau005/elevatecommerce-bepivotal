<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'handle',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate handle from name if not provided
        static::creating(function ($customerGroup) {
            if (empty($customerGroup->handle)) {
                $customerGroup->handle = Str::slug($customerGroup->name);
                
                // Ensure handle is unique
                $originalHandle = $customerGroup->handle;
                $counter = 1;
                while (static::where('handle', $customerGroup->handle)->exists()) {
                    $customerGroup->handle = $originalHandle . '-' . $counter;
                    $counter++;
                }
            }
        });

        // Ensure only one default group exists
        static::saving(function ($customerGroup) {
            if ($customerGroup->is_default) {
                // Set all other groups to non-default
                static::where('id', '!=', $customerGroup->id)->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the default customer group.
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Relationship with customers (users).
     */
    public function customers()
    {
        return $this->hasMany(User::class, 'customer_group_id');
    }
}