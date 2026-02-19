<?php

namespace App\Enums;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case BLOCKED = 'blocked';

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::TODO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::DONE => 'Done',
            self::BLOCKED => 'Blocked',
        };
    }

    /**
     * Get a color for UI display (Tailwind classes).
     */
    public function color(): string
    {
        return match ($this) {
            self::TODO => 'gray',
            self::IN_PROGRESS => 'blue',
            self::DONE => 'green',
            self::BLOCKED => 'red',
        };
    }
}
