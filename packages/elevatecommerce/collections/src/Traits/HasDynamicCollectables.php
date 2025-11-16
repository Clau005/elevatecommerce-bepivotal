<?php

namespace ElevateCommerce\Collections\Traits;

trait HasDynamicCollectables
{
    /**
     * Handle dynamic method calls for collectable relationships
     */
    public function __call($method, $parameters)
    {
        // Try to match against registered collectable types
        if ($this->isDynamicCollectableRelation($method)) {
            $relation = $this->createDynamicCollectableRelation($method);
            return $relation;
        }
        
        return parent::__call($method, $parameters);
    }
    
    /**
     * Handle dynamic property access for collectable relationships
     */
    public function __get($key)
    {
        // Check if this is a dynamic collectable relation
        if ($this->isDynamicCollectableRelation($key)) {
            // Create and execute the relationship
            $relation = $this->createDynamicCollectableRelation($key);
            
            if ($relation) {
                // Cache the result in relations array
                return $this->relations[$key] = $relation->get();
            }
        }
        
        return parent::__get($key);
    }
    
    /**
     * Check if the method name matches a registered collectable type
     */
    protected function isDynamicCollectableRelation(string $method): bool
    {
        if (!app()->bound(\ElevateCommerce\Collections\Services\CollectableRegistry::class)) {
            return false;
        }
        
        $registry = app(\ElevateCommerce\Collections\Services\CollectableRegistry::class);
        
        foreach ($registry->all() as $class => $config) {
            $relationName = \Illuminate\Support\Str::plural(
                strtolower(class_basename($class))
            );
            
            if ($method === $relationName) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Create a morphedByMany relationship for the collectable type
     */
    protected function createDynamicCollectableRelation(string $method)
    {
        $registry = app(\ElevateCommerce\Collections\Services\CollectableRegistry::class);
        
        foreach ($registry->all() as $class => $config) {
            $relationName = \Illuminate\Support\Str::plural(
                strtolower(class_basename($class))
            );
            
            if ($method === $relationName) {
                return $this->morphedByMany(
                    $class,
                    'collectable',
                    'collectables'
                )->withTimestamps()
                 ->orderBy('collectables.sort_order');
            }
        }
    }
    
    /**
     * Get collectables filtered by a specific type
     */
    public function getCollectablesByType(string $modelClass)
    {
        return $this->collectables()
            ->where('collectable_type', $modelClass)
            ->with('collectable')
            ->orderBy('sort_order')
            ->get()
            ->pluck('collectable');
    }
}
