<?php

namespace App\Enums;

enum TemplateType: string
{
    case BUG = 'bug';
    case FEATURE = 'feature';
    case HOTFIX = 'hotfix';
    case CUSTOM = 'custom';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::BUG => 'Bug',
            self::FEATURE => 'Feature',
            self::HOTFIX => 'Hotfix',
            self::CUSTOM => 'Custom',
        };
    }

    /**
     * Get an icon hint for UI.
     */
    public function icon(): string
    {
        return match ($this) {
            self::BUG => 'bug',
            self::FEATURE => 'sparkles',
            self::HOTFIX => 'fire',
            self::CUSTOM => 'document',
        };
    }
}
