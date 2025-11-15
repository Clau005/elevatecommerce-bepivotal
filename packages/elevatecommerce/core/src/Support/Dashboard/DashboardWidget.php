<?php

namespace ElevateCommerce\Core\Support\Dashboard;

class DashboardWidget
{
    public function __construct(
        public ?string $component = null,
        public ?string $view = null,
        public array $data = [],
        public string $position = 'main',
        public int $order = 100,
        public string $width = 'full',
        public array $permissions = [],
        public bool $enabled = true,
    ) {}

    /**
     * Create a new widget
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Set the component
     */
    public function component(string $component): static
    {
        $this->component = $component;
        return $this;
    }

    /**
     * Set the view
     */
    public function view(string $view): static
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Set the data
     */
    public function data(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the position
     */
    public function position(string $position): static
    {
        $this->position = $position;
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
     * Set the width
     */
    public function width(string $width): static
    {
        $this->width = $width;
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
     * Enable the widget
     */
    public function enable(): static
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * Disable the widget
     */
    public function disable(): static
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'component' => $this->component,
            'view' => $this->view,
            'data' => $this->data,
            'position' => $this->position,
            'order' => $this->order,
            'width' => $this->width,
            'permissions' => $this->permissions,
            'enabled' => $this->enabled,
        ];
    }
}
