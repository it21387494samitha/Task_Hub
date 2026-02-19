<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * ActivityLog model — each row is one audit entry.
 *
 * Uses Laravel's Polymorphic relationship (MorphTo) so the same table
 * can log actions for Tasks, Users, Projects — any model.
 *
 * MERN comparison:
 *   Like a Mongoose model with a discriminator/ref pattern:
 *   { subjectModel: String, subjectId: ObjectId }
 *   Laravel does the same with subject_type + subject_id.
 */
class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'changes',
        'description',
    ];

    protected $casts = [
        'changes' => 'array',  // Auto JSON encode/decode
    ];

    // ─── Relationships ────────────────────────────────────

    /**
     * The user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The model that was acted upon (polymorphic).
     *
     * Usage: $log->subject → returns the Task (or User, etc.)
     * withTrashed() ensures we can still load soft-deleted subjects.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    // ─── Scopes (reusable query filters) ──────────────────

    /**
     * Scope: only logs for a specific model type.
     * Usage: ActivityLog::forModel(Task::class)->get();
     */
    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('subject_type', $modelClass);
    }

    /**
     * Scope: only logs by a specific user.
     * Usage: ActivityLog::byUser($user)->get();
     */
    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope: only a specific action type.
     * Usage: ActivityLog::ofAction('created')->get();
     */
    public function scopeOfAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: most recent first (by id for reliable ordering when timestamps match).
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('id');
    }
}
