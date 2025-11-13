<?php

namespace Elevate\Collections\Services;

class CollectableRegistry
{
    protected array $collectableTypes = [];

    /**
     * Register a model as collectable
     *
     * @param string $modelClass Fully qualified class name
     * @param array $config Configuration for this collectable type
     *   - 'label': Display name (e.g., 'Products')
     *   - 'singular': Singular display name (e.g., 'Product')
     *   - 'icon': SVG path for icon
     */
    public function register(string $modelClass, array $config): void
    {
        $this->collectableTypes[$modelClass] = array_merge([
            'label' => str(class_basename($modelClass))->plural()->toString(),
            'singular' => class_basename($modelClass),
            'icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
        ], $config);
    }

    /**
     * Get all registered collectable types
     */
    public function all(): array
    {
        return $this->collectableTypes;
    }

    /**
     * Get configuration for a specific collectable type
     */
    public function get(string $modelClass): ?array
    {
        return $this->collectableTypes[$modelClass] ?? null;
    }

    /**
     * Check if a model is registered as collectable
     */
    public function has(string $modelClass): bool
    {
        return isset($this->collectableTypes[$modelClass]);
    }

    /**
     * Get collectable type by key (lowercase class basename)
     */
    public function getByKey(string $key): ?string
    {
        foreach ($this->collectableTypes as $class => $config) {
            if (strtolower(class_basename($class)) === strtolower($key)) {
                return $class;
            }
        }
        return null;
    }

    /**
     * Get all registered types as options for UI
     */
    public function getOptions(): array
    {
        $options = [];
        foreach ($this->collectableTypes as $class => $config) {
            $options[] = [
                'class' => $class,
                'key' => strtolower(class_basename($class)),
                'label' => $config['label'],
                'singular' => $config['singular'],
                'icon' => $config['icon'],
            ];
        }
        return $options;
    }
}
