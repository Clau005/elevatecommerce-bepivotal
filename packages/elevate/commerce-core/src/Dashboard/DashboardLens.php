<?php

namespace Elevate\CommerceCore\Dashboard;

abstract class DashboardLens
{
    /**
     * Unique identifier for this lens
     */
    abstract public function id(): string;

    /**
     * Display name for this lens
     */
    abstract public function name(): string;

    /**
     * Description of what this lens shows
     */
    public function description(): ?string
    {
        return null;
    }

    /**
     * Grid width (1-12 columns, Tailwind grid)
     * Default: 4 (1/3 of 12-column grid)
     */
    public function width(): int
    {
        return 4;
    }

    /**
     * Grid height (auto, or specific tailwind height class)
     * Default: 'auto'
     */
    public function height(): string
    {
        return 'auto';
    }

    /**
     * Order/priority for rendering (lower = first)
     */
    public function order(): int
    {
        return 100;
    }

    /**
     * Whether this lens should be shown
     * Can be used for permission checks or conditional display
     */
    public function shouldShow(): bool
    {
        return true;
    }

    /**
     * Get the data for this lens
     * This is where you fetch/compute the data to display
     */
    abstract public function data(): array;

    /**
     * Render the lens HTML
     * Return the HTML string to display
     */
    abstract public function render(): string;

    /**
     * Optional: Blade view to use for rendering
     * If provided, render() will use this view with data()
     */
    public function view(): ?string
    {
        return null;
    }

    /**
     * Get the rendered output
     * Uses view() if provided, otherwise calls render()
     */
    public function toHtml(): string
    {
        if ($view = $this->view()) {
            return view($view, $this->data())->render();
        }

        return $this->render();
    }

    /**
     * CSS classes for the lens container
     */
    public function containerClasses(): string
    {
        return 'bg-white rounded-lg shadow-sm border border-gray-200 p-6';
    }

    /**
     * Whether this lens can be refreshed via AJAX
     */
    public function refreshable(): bool
    {
        return false;
    }

    /**
     * Refresh interval in seconds (if refreshable)
     */
    public function refreshInterval(): ?int
    {
        return null;
    }
}
