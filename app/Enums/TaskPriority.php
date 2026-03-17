<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * Get a human-readable label for the priority.
     */
    public function label(): string
    {
        return match($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
        };
    }

    /**
     * Get the numeric value for sorting.
     */
    public function value(): int
    {
        return match($this) {
            self::Low => 1,
            self::Medium => 2,
            self::High => 3,
        };
    }
}
