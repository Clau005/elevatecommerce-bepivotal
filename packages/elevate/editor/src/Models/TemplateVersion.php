<?php

namespace Elevate\Editor\Models;

use Elevate\CommerceCore\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateVersion extends Model
{
    protected $fillable = [
        'template_id',
        'created_by',
        'version_number',
        'configuration',
        'change_notes',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    /**
     * Get the template this version belongs to
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the user who created this version
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    /**
     * Restore this version (make it the current configuration)
     */
    public function restore(): void
    {
        $this->template->update([
            'draft_configuration' => $this->configuration,
        ]);
    }
}
