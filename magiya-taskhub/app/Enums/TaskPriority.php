<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    /**
     * Get a human-readable label for the priority.
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::CRITICAL => 'Critical',
        };
    }

    /**
     * Get a color for UI display (Tailwind classes).
     */
    public function color(): string
    {
        return match ($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'yellow',
            self::HIGH => 'orange',
            self::CRITICAL => 'red',
        };
    }
}
