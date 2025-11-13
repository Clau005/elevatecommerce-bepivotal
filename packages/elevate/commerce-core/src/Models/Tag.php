<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = [
        'value',
        'handle',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->handle)) {
                $tag->handle = static::generateUniqueHandle($tag->value);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('value') && empty($tag->handle)) {
                $tag->handle = static::generateUniqueHandle($tag->value);
            }
        });
    }

    /**
     * Get all of the models that are assigned this tag (polymorphic).
     */
    public function taggables()
    {
        return $this->hasMany(Taggable::class);
    }

    /**
     * Scope to order tags alphabetically.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('value');
    }

    /**
     * Find or create a tag by value.
     */
    public static function findOrCreate(string $value): self
    {
        $handle = Str::slug($value);
        
        return static::firstOrCreate(
            ['handle' => $handle],
            ['value' => $value]
        );
    }

    /**
     * Generate a unique handle for the tag.
     */
    protected static function generateUniqueHandle(string $value): string
    {
        $handle = Str::slug($value);
        $originalHandle = $handle;
        $counter = 1;

        while (static::where('handle', $handle)->exists()) {
            $handle = $originalHandle . '-' . $counter;
            $counter++;
        }

        return $handle;
    }
}
