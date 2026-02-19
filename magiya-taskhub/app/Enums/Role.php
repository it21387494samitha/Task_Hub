<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case TEAM_LEADER = 'team_leader';
    case DEVELOPER = 'developer';

    /**
     * Get a human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::TEAM_LEADER => 'Team Leader',
            self::DEVELOPER => 'Developer',
        };
    }
}
