<?php

namespace ElevateCommerce\Editor\Models;

use Elevate\CommerceCore\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorSession extends Model
{
    protected $fillable = [
        'template_id',
        'user_id',
        'configuration',
        'last_activity_at',
    ];

    protected $casts = [
        'configuration' => 'array',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the template being edited
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the user editing
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }

    /**
     * Update activity timestamp
     */
    public function touch($attribute = null)
    {
        if (!$attribute) {
            $this->last_activity_at = now();
        }

        return parent::touch($attribute);
    }

    /**
     * Scope to get active sessions (within last 5 minutes)
     */
    public function scopeActive($query)
    {
        return $query->where('last_activity_at', '>=', now()->subMinutes(5));
    }
}
