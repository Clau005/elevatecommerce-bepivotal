<?php

namespace Elevate\CommerceCore\Dashboard\Lenses;

use Elevate\CommerceCore\Dashboard\DashboardLens;

class StatsCardLens extends DashboardLens
{
    public function __construct(
        protected string $identifier,
        protected string $title,
        protected string $value,
        protected ?string $change = null,
        protected ?string $changeType = null, // 'increase', 'decrease', 'neutral'
        protected ?string $icon = null,
        protected int $gridWidth = 3,
        protected int $priority = 100,
    ) {}

    public function id(): string
    {
        return $this->identifier;
    }

    public function name(): string
    {
        return $this->title;
    }

    public function width(): int
    {
        return $this->gridWidth;
    }

    public function order(): int
    {
        return $this->priority;
    }

    public function data(): array
    {
        return [
            'title' => $this->title,
            'value' => $this->value,
            'change' => $this->change,
            'changeType' => $this->changeType,
            'icon' => $this->icon,
        ];
    }

    public function view(): ?string
    {
        return 'commerce::dashboard.lenses.stats-card';
    }

    public function render(): string
    {
        $changeColor = match($this->changeType) {
            'increase' => 'text-green-600',
            'decrease' => 'text-red-600',
            default => 'text-gray-600',
        };

        $changeIcon = match($this->changeType) {
            'increase' => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>',
            'decrease' => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>',
            default => '',
        };

        $change = $this->change ? "<div class='flex items-center gap-1 {$changeColor} text-sm font-medium'>{$changeIcon}<span>{$this->change}</span></div>" : '';

        return "
            <div>
                <div class='text-sm font-medium text-gray-600'>{$this->title}</div>
                <div class='mt-2 flex items-baseline justify-between'>
                    <div class='text-3xl font-bold text-gray-900'>{$this->value}</div>
                    {$change}
                </div>
            </div>
        ";
    }
}
