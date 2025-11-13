<?php

namespace Elevate\Collections\Services;

use Elevate\Collections\Models\Collection;
use Elevate\Collections\Models\CollectionFilter;
use Elevate\Collections\Models\CollectionFilterValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as SupportCollection;

class FilterService
{
    /**
     * Parse filter segments from URL
     * Example: ['color-blue', 'size-11'] => ['color' => 'blue', 'size' => '11']
     */
    public function parseFilterSegments(array $segments, Collection $collection): array
    {
        $appliedFilters = [];
        $filters = $collection->activeFilters()->with('activeValues')->get();
        
        foreach ($segments as $segment) {
            // Try to match segment to filter-value pattern
            foreach ($filters as $filter) {
                $filterSlug = $filter->slug;
                
                // Check if segment starts with filter slug
                if (str_starts_with($segment, $filterSlug . '-')) {
                    $valueSlug = substr($segment, strlen($filterSlug) + 1);
                    
                    // Find matching filter value
                    $filterValue = $filter->activeValues->firstWhere('slug', $valueSlug);
                    
                    if ($filterValue) {
                        $appliedFilters[$filterSlug] = [
                            'filter' => $filter,
                            'value' => $filterValue,
                            'segment' => $segment,
                        ];
                        break;
                    }
                }
            }
        }
        
        return $appliedFilters;
    }

    /**
     * Apply filters to a query builder
     */
    public function applyFilters(Builder $query, array $appliedFilters): Builder
    {
        foreach ($appliedFilters as $filterData) {
            $filter = $filterData['filter'];
            $filterValue = $filterData['value'];
            
            $column = $filter->source_column;
            $value = $filterValue->value;
            
            // Check if this filter is for ProductVariant
            if ($filter->source_model === 'Elevate\Product\Models\ProductVariant') {
                // Filter products that have variants matching this criteria
                $query->whereHas('variants', function ($q) use ($column, $value) {
                    $q->where($column, $value);
                });
            } elseif ($filter->source_relation) {
                // Filter by related model
                $query->whereHas($filter->source_relation, function ($q) use ($column, $value) {
                    $q->where($column, $value);
                });
            } else {
                // Direct column filter on products table
                $query->where($column, $value);
            }
        }
        
        return $query;
    }

    /**
     * Get canonical URL for filters (alphabetically sorted)
     */
    public function getCanonicalFilterPath(array $appliedFilters): string
    {
        $segments = collect($appliedFilters)
            ->map(function($filter) {
                // Support both 'segment' and 'slug' keys for backwards compatibility
                return $filter['segment'] ?? $filter['slug'] ?? null;
            })
            ->filter()
            ->sort()
            ->values()
            ->toArray();
        
        return implode('/', $segments);
    }

    /**
     * Check if current filter path is canonical
     */
    public function isCanonicalPath(array $segments, array $appliedFilters): bool
    {
        $currentPath = implode('/', $segments);
        $canonicalPath = $this->getCanonicalFilterPath($appliedFilters);
        
        return $currentPath === $canonicalPath;
    }

    /**
     * Generate filter URLs for adding/removing filters
     */
    public function generateFilterUrl(
        string $baseUrl,
        array $currentFilters,
        string $filterSlug,
        ?string $valueSlug = null
    ): string {
        $filters = $currentFilters;
        
        if ($valueSlug === null) {
            // Remove filter
            unset($filters[$filterSlug]);
        } else {
            // Add/update filter
            $filters[$filterSlug] = ['segment' => $filterSlug . '-' . $valueSlug];
        }
        
        // Sort alphabetically for canonical URL
        ksort($filters);
        
        $segments = collect($filters)->pluck('segment')->values()->toArray();
        
        if (empty($segments)) {
            return $baseUrl;
        }
        
        return $baseUrl . '/' . implode('/', $segments);
    }

    /**
     * Get available filter values with product counts
     * This dynamically calculates which filter values are available
     * based on currently applied filters
     */
    public function getAvailableFilters(
        Collection $collection,
        array $appliedFilters,
        Builder $baseQuery
    ): SupportCollection {
        $filters = $collection->filters()
            ->wherePivot('is_active', true)
            ->with('values')
            ->orderBy('collection_filters.sort_order')
            ->get();
        
        return $filters->map(function ($filter) use ($appliedFilters, $baseQuery) {
            $values = $filter->values->filter(fn($v) => $v->is_active)->map(function ($value) use ($filter, $appliedFilters, $baseQuery) {
                // Clone query and apply all OTHER filters
                $testQuery = clone $baseQuery;
                
                $otherFilters = collect($appliedFilters)
                    ->reject(fn($f) => $f['filter']->id === $filter->id)
                    ->toArray();
                
                $this->applyFilters($testQuery, $otherFilters);
                
                // Add this specific filter value
                if ($filter->source_model === 'Elevate\Product\Models\ProductVariant') {
                    // Filter products that have variants matching this criteria
                    $testQuery->whereHas('variants', function ($q) use ($filter, $value) {
                        $q->where($filter->source_column, $value->value);
                    });
                } elseif ($filter->source_relation) {
                    $testQuery->whereHas($filter->source_relation, function ($q) use ($filter, $value) {
                        $q->where($filter->source_column, $value->value);
                    });
                } else {
                    $testQuery->where($filter->source_column, $value->value);
                }
                
                // Count products
                $count = $testQuery->count();
                
                return [
                    'label' => $value->label,
                    'slug' => $value->slug,
                    'count' => $count,
                    'is_active' => isset($appliedFilters[$filter->slug]) && 
                                   $appliedFilters[$filter->slug]['value']->id === $value->id,
                ];
            })->filter(fn($v) => $v['count'] > 0); // Only show values with products
            
            return [
                'filter' => $filter,
                'values' => $values,
                'active_values' => collect($appliedFilters)
                    ->filter(fn($f) => $f['filter']->id === $filter->id)
                    ->pluck('value.id')
                    ->toArray(),
            ];
        })->filter(fn($f) => $f['values']->isNotEmpty()); // Only show filters with available values
    }
}
