<?php

namespace Elevate\Editor\Services;

class TemplateRegistry
{
    protected array $registeredModels = [];

    /**
     * Register a model as templatable
     *
     * @param string $modelClass Fully qualified class name
     * @param array $config Configuration for this model type
     *   - 'label': Display name (e.g., 'Product')
     *   - 'plural_label': Plural display name (e.g., 'Products')
     *   - 'icon': Icon identifier (optional)
     *   - 'description': Description of this model type
     *   - 'default_route_pattern': Default route pattern (e.g., '/products/{slug}')
     *   - 'preview_data_provider': Callback to get sample data for preview
     */
    public function register(string $modelClass, array $config): void
    {
        $this->registeredModels[$modelClass] = array_merge([
            'label' => class_basename($modelClass),
            'plural_label' => str(class_basename($modelClass))->plural()->toString(),
            'icon' => null,
            'description' => null,
            'default_route_pattern' => '/' . str(class_basename($modelClass))->lower()->plural()->toString() . '/{slug}',
            'preview_data_provider' => null,
        ], $config);
    }

    /**
     * Get all registered models
     */
    public function all(): array
    {
        return $this->registeredModels;
    }

    /**
     * Get configuration for a specific model
     */
    public function get(string $modelClass): ?array
    {
        return $this->registeredModels[$modelClass] ?? null;
    }

    /**
     * Check if a model is registered
     */
    public function has(string $modelClass): bool
    {
        return isset($this->registeredModels[$modelClass]);
    }

    /**
     * Get model class by label
     */
    public function getByLabel(string $label): ?string
    {
        foreach ($this->registeredModels as $class => $config) {
            if ($config['label'] === $label || $config['plural_label'] === $label) {
                return $class;
            }
        }
        return null;
    }

    /**
     * Get all registered models as options for dropdowns
     */
    public function getOptions(): array
    {
        $options = [];
        foreach ($this->registeredModels as $class => $config) {
            $options[] = [
                'value' => $class,
                'label' => $config['label'],
                'plural_label' => $config['plural_label'],
                'icon' => $config['icon'],
                'description' => $config['description'],
            ];
        }
        return $options;
    }

    /**
     * Get preview data for a model type
     */
    public function getPreviewData(string $modelClass)
    {
        $config = $this->get($modelClass);
        
        if (!$config || !$config['preview_data_provider']) {
            // Return a random instance if no provider is set
            if (class_exists($modelClass)) {
                return $modelClass::inRandomOrder()->first();
            }
            return null;
        }

        return call_user_func($config['preview_data_provider']);
    }
}
