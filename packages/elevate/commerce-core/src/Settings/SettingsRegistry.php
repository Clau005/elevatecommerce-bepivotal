<?php

namespace Elevate\CommerceCore\Settings;

use Illuminate\Support\Collection;

class SettingsRegistry
{
    /**
     * Registered settings sections
     * 
     * @var array<string, SettingsSection>
     */
    protected array $sections = [];

    /**
     * Register a settings section
     */
    public function register(SettingsSection $section): self
    {
        $this->sections[$section->id()] = $section;
        return $this;
    }

    /**
     * Register multiple sections
     * 
     * @param array<SettingsSection> $sections
     */
    public function registerMany(array $sections): self
    {
        foreach ($sections as $section) {
            $this->register($section);
        }
        return $this;
    }

    /**
     * Get all registered sections
     * 
     * @return Collection<SettingsSection>
     */
    public function all(): Collection
    {
        return collect($this->sections)
            ->filter(fn(SettingsSection $section) => $section->shouldShow())
            ->sortBy(fn(SettingsSection $section) => $section->order())
            ->values();
    }

    /**
     * Get sections grouped by category
     * 
     * @return Collection<string, Collection<SettingsSection>>
     */
    public function grouped(): Collection
    {
        return $this->all()
            ->groupBy(fn(SettingsSection $section) => $section->group())
            ->map(fn($sections) => $sections->sortBy(fn($section) => $section->order()));
    }

    /**
     * Get a specific section by ID
     */
    public function get(string $id): ?SettingsSection
    {
        return $this->sections[$id] ?? null;
    }

    /**
     * Check if a section is registered
     */
    public function has(string $id): bool
    {
        return isset($this->sections[$id]);
    }

    /**
     * Remove a section from registry
     */
    public function remove(string $id): self
    {
        unset($this->sections[$id]);
        return $this;
    }

    /**
     * Get sections for a specific group
     */
    public function forGroup(string $group): Collection
    {
        return $this->all()
            ->filter(fn(SettingsSection $section) => $section->group() === $group);
    }
}
