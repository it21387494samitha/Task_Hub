<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    /**
     * Supported event types for notification preferences.
     */
    public const EVENT_TYPES = [
        'task_assigned'  => 'Task Assigned to Me',
        'task_due_soon'  => 'Task Due Soon',
        'comment_added'  => 'New Comment on My Task',
        'status_changed' => 'Task Status Changed',
        'task_blocked'   => 'Task Blocked',
    ];

    protected $fillable = [
        'user_id',
        'event_type',
        'email_enabled',
        'database_enabled',
    ];

    protected function casts(): array
    {
        return [
            'email_enabled'    => 'boolean',
            'database_enabled' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────

    /**
     * Get label for an event type.
     */
    public static function labelFor(string $eventType): string
    {
        return self::EVENT_TYPES[$eventType] ?? ucfirst(str_replace('_', ' ', $eventType));
    }

    /**
     * Check whether the given user has this event enabled for a channel.
     */
    public static function isEnabled(int $userId, string $eventType, string $channel = 'database'): bool
    {
        $setting = static::where('user_id', $userId)
            ->where('event_type', $eventType)
            ->first();

        // If no explicit setting exists, default to enabled
        if (! $setting) {
            return true;
        }

        return match ($channel) {
            'email'    => $setting->email_enabled,
            'database' => $setting->database_enabled,
            default    => true,
        };
    }
}
