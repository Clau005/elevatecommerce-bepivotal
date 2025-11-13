<?php

namespace Elevate\CommerceCore\Dashboard;

use Illuminate\Support\Collection;

class DashboardRegistry
{
    /**
     * Registered dashboard lenses
     * 
     * @var array<string, DashboardLens>
     */
    protected array $lenses = [];

    /**
     * Register a dashboard lens
     */
    public function register(DashboardLens $lens): self
    {
        $this->lenses[$lens->id()] = $lens;
        return $this;
    }

    /**
     * Register multiple lenses
     * 
     * @param array<DashboardLens> $lenses
     */
    public function registerMany(array $lenses): self
    {
        foreach ($lenses as $lens) {
            $this->register($lens);
        }
        return $this;
    }

    /**
     * Get all registered lenses
     * 
     * @return Collection<DashboardLens>
     */
    public function all(): Collection
    {
        return collect($this->lenses)
            ->filter(fn(DashboardLens $lens) => $lens->shouldShow())
            ->sortBy(fn(DashboardLens $lens) => $lens->order())
            ->values();
    }

    /**
     * Get a specific lens by ID
     */
    public function get(string $id): ?DashboardLens
    {
        return $this->lenses[$id] ?? null;
    }

    /**
     * Check if a lens is registered
     */
    public function has(string $id): bool
    {
        return isset($this->lenses[$id]);
    }

    /**
     * Remove a lens from registry
     */
    public function remove(string $id): self
    {
        unset($this->lenses[$id]);
        return $this;
    }

    /**
     * Get lenses grouped by row based on width
     * Returns array of rows, each containing lenses that fit in 12-column grid
     */
    public function getRows(): array
    {
        $lenses = $this->all();
        $rows = [];
        $currentRow = [];
        $currentWidth = 0;

        foreach ($lenses as $lens) {
            $lensWidth = $lens->width();

            // If adding this lens would exceed 12 columns, start new row
            if ($currentWidth + $lensWidth > 12) {
                if (!empty($currentRow)) {
                    $rows[] = $currentRow;
                }
                $currentRow = [$lens];
                $currentWidth = $lensWidth;
            } else {
                $currentRow[] = $lens;
                $currentWidth += $lensWidth;
            }
        }

        // Add the last row if not empty
        if (!empty($currentRow)) {
            $rows[] = $currentRow;
        }

        return $rows;
    }
}
