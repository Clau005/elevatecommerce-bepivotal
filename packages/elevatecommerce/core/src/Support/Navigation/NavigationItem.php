<?php

namespace ElevateCommerce\Core\Support\Navigation;

class NavigationItem
{
    public function __construct(
        public string $label,
        public ?string $icon = null,
        public ?string $route = null,
        public ?string $url = null,
        public ?string $badge = null,
        public array $children = [],
        public int $order = 100,
        public array $permissions = [],
        public mixed $active = null,
    ) {}

    /**
     * Create a new navigation item
     */
    public static function make(string $label): static
    {
        return new static($label);
    }

    /**
     * Set the icon
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set the route
     */
    public function route(string $route): static
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Set the URL
     */
    public function url(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Set the badge
     */
    public function badge(string $badge): static
    {
        $this->badge = $badge;
        return $this;
    }

    /**
     * Set the children
     */
    public function children(array $children): static
    {
        $this->children = $children;
        return $this;
    }

    /**
     * Set the order
     */
    public function order(int $order): static
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Set the permissions
     */
    public function permissions(array $permissions): static
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Set the active callback
     */
    public function active(callable|string $active): static
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Check if this item is active
     */
    public function isActive(): bool
    {
        if (is_callable($this->active)) {
            return call_user_func($this->active);
        }

        if (is_string($this->active)) {
            return request()->routeIs($this->active);
        }

        if ($this->route) {
            return request()->routeIs($this->route);
        }

        return false;
    }

    /**
     * Get the URL for this item
     */
    public function getUrl(): string
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->route) {
            return route($this->route);
        }

        return '#';
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'route' => $this->route,
            'url' => $this->url,
            'badge' => $this->badge,
            'children' => $this->children,
            'order' => $this->order,
            'permissions' => $this->permissions,
            'active' => $this->active,
        ];
    }
}
