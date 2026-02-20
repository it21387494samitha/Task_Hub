<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskTag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'created_by',
        'due_date',
        'started_at',
        'completed_at',
        'blocked_at',
        'block_reason',
        'tags',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'blocked_at' => 'datetime',
            'tags' => 'array',
        ];
    }

    // ─── Relationships ───────────────────────────────────

    /**
     * The user this task is assigned to.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The user who created this task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Comments on this task.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * File attachments on this task.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    // ─── Helper Methods ──────────────────────────────────

    /**
     * Check if the task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->status !== TaskStatus::DONE;
    }

    /**
     * Get cycle time in hours (created_at → completed_at).
     * Returns null if not yet completed.
     */
    public function cycleTimeInHours(): ?float
    {
        if (! $this->completed_at) {
            return null;
        }

        return round($this->created_at->diffInHours($this->completed_at), 1);
    }

    /**
     * Get the number of days a task has been in its current status.
     */
    public function daysInCurrentStatus(): int
    {
        $reference = match ($this->status) {
            TaskStatus::IN_PROGRESS => $this->started_at,
            TaskStatus::BLOCKED => $this->blocked_at,
            TaskStatus::DONE => $this->completed_at,
            default => $this->created_at,
        };

        return (int) ($reference ?? $this->created_at)->diffInDays(now());
    }

    /**
     * Check if the task is stuck (in progress too long).
     */
    public function isStuck(): bool
    {
        return $this->status === TaskStatus::IN_PROGRESS
            && $this->daysInCurrentStatus() > config('taskhub.stuck_days_threshold', 5);
    }

    /**
     * Check if the task has a specific tag.
     */
    public function hasTag(TaskTag $tag): bool
    {
        return in_array($tag->value, $this->tags ?? []);
    }
}
