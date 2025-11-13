<?php

namespace Elevate\Editor\Contracts;

interface Templatable
{
    /**
     * Get the template assigned to this model instance
     */
    public function template();
    
    /**
     * Get available templates for this model type
     */
    public static function getAvailableTemplates();
    
    /**
     * Get the default template for this model type
     */
    public static function getDefaultTemplate();
    
    /**
     * Render this model using its assigned template
     */
    public function render(bool $isPreview = false);
    
    /**
     * Get data to pass to the template
     */
    public function getTemplateData(): array;
}
