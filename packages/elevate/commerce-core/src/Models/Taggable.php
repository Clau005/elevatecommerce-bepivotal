<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Taggable extends MorphPivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'taggables';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tag_id',
        'taggable_id',
        'taggable_type',
    ];

    /**
     * Get the owning taggable model.
     */
    public function taggable()
    {
        return $this->morphTo();
    }

    /**
     * Get the tag that owns the taggable.
     */
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
