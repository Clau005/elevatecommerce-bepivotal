<?php

namespace Elevate\CommerceCore\Settings;

abstract class SettingsSection
{
    /**
     * Unique identifier for this section
     */
    abstract public function id(): string;

    /**
     * Display name for this section
     */
    abstract public function name(): string;

    /**
     * Description of this section
     */
    abstract public function description(): string;

    /**
     * Icon path (SVG path data for heroicons)
     */
    abstract public function icon(): string;

    /**
     * URL to the section page
     */
    abstract public function url(): string;

    /**
     * Group/category for organizing sections
     * Examples: 'general', 'commerce', 'system', 'advanced'
     */
    public function group(): string
    {
        return 'general';
    }

    /**
     * Order/priority within group (lower = first)
     */
    public function order(): int
    {
        return 100;
    }

    /**
     * Whether this section should be shown
     * Can be used for permission checks
     */
    public function shouldShow(): bool
    {
        return true;
    }

    /**
     * Badge text (optional)
     * Example: "New", "Beta", count of items
     */
    public function badge(): ?string
    {
        return null;
    }

    /**
     * Badge color
     * Options: 'blue', 'green', 'yellow', 'red', 'gray'
     */
    public function badgeColor(): string
    {
        return 'blue';
    }

    /**
     * Whether this section requires special permissions
     */
    public function requiredPermission(): ?string
    {
        return null;
    }

    /**
     * Get section as array for rendering
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'description' => $this->description(),
            'icon' => $this->icon(),
            'url' => $this->url(),
            'group' => $this->group(),
            'order' => $this->order(),
            'badge' => $this->badge(),
            'badgeColor' => $this->badgeColor(),
        ];
    }
}
