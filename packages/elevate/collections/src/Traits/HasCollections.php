<?php

namespace Elevate\Collections\Traits;

use Elevate\Collections\Models\Collection;

trait HasCollections
{
    /**
     * Get all collections this model belongs to
     */
    public function collections()
    {
        return $this->morphToMany(
            Collection::class,
            'collectable',
            'collectables'
        )->withTimestamps();
    }

    /**
     * Check if this model is in a specific collection
     */
    public function isInCollection(Collection $collection): bool
    {
        return $this->collections()->where('collections.id', $collection->id)->exists();
    }

    /**
     * Add this model to a collection
     */
    public function addToCollection(Collection $collection, int $sortOrder = null): void
    {
        if (!$this->isInCollection($collection)) {
            $sortOrder = $sortOrder ?? $collection->collectables()->count();
            
            $collection->collectables()->create([
                'collectable_type' => get_class($this),
                'collectable_id' => $this->id,
                'sort_order' => $sortOrder,
            ]);
        }
    }

    /**
     * Remove this model from a collection
     */
    public function removeFromCollection(Collection $collection): void
    {
        $collection->collectables()
            ->where('collectable_type', get_class($this))
            ->where('collectable_id', $this->id)
            ->delete();
    }

    /**
     * Sync this model to specific collections
     */
    public function syncCollections(array $collectionIds): void
    {
        // Get current collections
        $currentCollectionIds = $this->collections()->pluck('collections.id')->toArray();
        
        // Determine which to add and remove
        $toAdd = array_diff($collectionIds, $currentCollectionIds);
        $toRemove = array_diff($currentCollectionIds, $collectionIds);
        
        // Remove from collections
        foreach ($toRemove as $collectionId) {
            $collection = Collection::find($collectionId);
            if ($collection) {
                $this->removeFromCollection($collection);
            }
        }
        
        // Add to collections
        foreach ($toAdd as $collectionId) {
            $collection = Collection::find($collectionId);
            if ($collection) {
                $this->addToCollection($collection);
            }
        }
    }
}
