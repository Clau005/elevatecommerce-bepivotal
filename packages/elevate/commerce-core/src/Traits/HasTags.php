<?php

namespace Elevate\CommerceCore\Traits;

use Elevate\CommerceCore\Models\Tag;
use Elevate\CommerceCore\Models\Taggable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    /**
     * Get all of the tags for the model.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables')
            ->using(Taggable::class)
            ->withTimestamps();
    }

    /**
     * Attach tags to the model.
     * 
     * @param array|string $tags Array of tag values or comma-separated string
     */
    public function attachTags($tags): void
    {
        $tags = $this->normalizeTags($tags);
        
        foreach ($tags as $tagValue) {
            $tag = Tag::findOrCreate($tagValue);
            
            // Only attach if not already attached
            if (!$this->tags()->where('tag_id', $tag->id)->exists()) {
                $this->tags()->attach($tag->id);
            }
        }
    }

    /**
     * Detach tags from the model.
     * 
     * @param array|string|null $tags Array of tag values, comma-separated string, or null to detach all
     */
    public function detachTags($tags = null): void
    {
        if ($tags === null) {
            $this->tags()->detach();
            return;
        }

        $tags = $this->normalizeTags($tags);
        
        foreach ($tags as $tagValue) {
            $tag = Tag::where('value', $tagValue)->first();
            
            if ($tag) {
                $this->tags()->detach($tag->id);
            }
        }
    }

    /**
     * Sync tags with the model (replaces all existing tags).
     * 
     * @param array|string $tags Array of tag values or comma-separated string
     */
    public function syncTags($tags): void
    {
        $tags = $this->normalizeTags($tags);
        $tagIds = [];
        
        foreach ($tags as $tagValue) {
            $tag = Tag::findOrCreate($tagValue);
            $tagIds[] = $tag->id;
        }
        
        $this->tags()->sync($tagIds);
    }

    /**
     * Check if the model has a specific tag.
     * 
     * @param string $tagValue
     */
    public function hasTag(string $tagValue): bool
    {
        return $this->tags()->where('value', $tagValue)->exists();
    }

    /**
     * Check if the model has any of the given tags.
     * 
     * @param array|string $tags
     */
    public function hasAnyTag($tags): bool
    {
        $tags = $this->normalizeTags($tags);
        
        return $this->tags()->whereIn('value', $tags)->exists();
    }

    /**
     * Check if the model has all of the given tags.
     * 
     * @param array|string $tags
     */
    public function hasAllTags($tags): bool
    {
        $tags = $this->normalizeTags($tags);
        
        return $this->tags()->whereIn('value', $tags)->count() === count($tags);
    }

    /**
     * Scope to filter models by tag.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $tags
     */
    public function scopeWithTag($query, $tags)
    {
        $tags = $this->normalizeTags($tags);
        
        return $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('value', $tags);
        });
    }

    /**
     * Scope to filter models by any of the given tags.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $tags
     */
    public function scopeWithAnyTag($query, $tags)
    {
        return $this->scopeWithTag($query, $tags);
    }

    /**
     * Scope to filter models by all of the given tags.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $tags
     */
    public function scopeWithAllTags($query, $tags)
    {
        $tags = $this->normalizeTags($tags);
        
        foreach ($tags as $tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('value', $tag);
            });
        }
        
        return $query;
    }

    /**
     * Get tag values as an array.
     */
    public function getTagValuesAttribute(): array
    {
        return $this->tags->pluck('value')->toArray();
    }

    /**
     * Normalize tags input to an array.
     * 
     * @param array|string $tags
     */
    protected function normalizeTags($tags): array
    {
        if (is_string($tags)) {
            // Split by comma and trim whitespace
            $tags = array_map('trim', explode(',', $tags));
        }
        
        // Remove empty values
        return array_filter($tags);
    }
}
