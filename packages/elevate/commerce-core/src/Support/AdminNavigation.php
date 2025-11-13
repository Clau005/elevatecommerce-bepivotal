<?php

namespace Elevate\CommerceCore\Support;

class AdminNavigation
{
    protected array $items = [];
    protected array $groups = [];

    /**
     * Add a navigation item.
     */
    public function add(string $label, string $url, array $options = []): self
    {
        $this->items[] = [
            'label' => $label,
            'url' => $url,
            'icon' => $options['icon'] ?? 'M4 6h16M4 12h16M4 18h16',
            'pattern' => $options['pattern'] ?? null,
            'order' => $options['order'] ?? 100,
            'group' => $options['group'] ?? 'main',
            'permission' => $options['permission'] ?? null,
            'badge' => $options['badge'] ?? null,
            'badge_color' => $options['badge_color'] ?? 'blue',
        ];

        return $this;
    }

    /**
     * Define a navigation group.
     */
    public function group(string $name, string $label, int $order = 100): self
    {
        $this->groups[$name] = [
            'label' => $label,
            'order' => $order,
        ];

        return $this;
    }

    /**
     * Get all navigation items, sorted and grouped.
     */
    public function items(): array
    {
        $items = collect($this->items)
            ->filter(function ($item) {
                // Filter by permission if specified
                if ($item['permission'] && !auth()->guard('staff')->user()?->can($item['permission'])) {
                    return false;
                }
                return true;
            })
            ->sortBy('order')
            ->groupBy('group')
            ->toArray();

        // Sort groups by their order
        $sortedGroups = [];
        foreach ($this->groups as $name => $group) {
            if (isset($items[$name])) {
                $sortedGroups[$name] = [
                    'label' => $group['label'],
                    'order' => $group['order'],
                    'items' => $items[$name],
                ];
            }
        }

        // Add ungrouped items to 'main' group
        if (isset($items['main'])) {
            $sortedGroups = array_merge(['main' => ['label' => null, 'order' => 0, 'items' => $items['main']]], $sortedGroups);
        }

        return collect($sortedGroups)->sortBy('order')->toArray();
    }

    /**
     * Get all registered groups.
     */
    public function groups(): array
    {
        return $this->groups;
    }

    /**
     * Clear all navigation items (useful for testing).
     */
    public function clear(): self
    {
        $this->items = [];
        $this->groups = [];
        return $this;
    }

    /**
     * Check if a navigation item is active based on current path.
     */
    public function isActive(array $item): bool
    {
        $currentPath = request()->path();
        
        if (!$item['pattern']) {
            return false;
        }

        // Exact match for dashboard
        if ($item['pattern'] === 'admin' && $currentPath === 'admin') {
            return true;
        }

        // Prefix match for other pages
        if ($item['pattern'] !== 'admin' && str_starts_with($currentPath, $item['pattern'])) {
            return true;
        }

        return false;
    }
}
