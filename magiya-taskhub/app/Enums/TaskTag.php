<?php

namespace App\Enums;

enum TaskTag: string
{
    case PROD_ISSUE = 'prod_issue';
    case HOTFIX = 'hotfix';
    case RELEASE_BLOCKER = 'release_blocker';
    case TECH_DEBT = 'tech_debt';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PROD_ISSUE => 'Prod Issue',
            self::HOTFIX => 'Hotfix',
            self::RELEASE_BLOCKER => 'Release Blocker',
            self::TECH_DEBT => 'Tech Debt',
        };
    }

    /**
     * Get a color for UI display.
     */
    public function color(): string
    {
        return match ($this) {
            self::PROD_ISSUE => 'red',
            self::HOTFIX => 'orange',
            self::RELEASE_BLOCKER => 'purple',
            self::TECH_DEBT => 'yellow',
        };
    }
}
