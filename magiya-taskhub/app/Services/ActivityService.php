<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * ActivityService — writes and reads audit trail entries.
 *
 * This service sits between the Listeners and the ActivityLog model.
 * Listeners call `ActivityService::log()`, this service creates the record.
 *
 * Why a service instead of writing directly in the listener?
 *   1. Single place to add extras (e.g. IP address, request ID)
 *   2. Easy to mock in tests
 *   3. Reusable from controllers, commands, or anywhere else
 */
class ActivityService
{
    /**
     * Log an activity.
     *
     * @param User|null   $user        Who performed the action (null = system)
     * @param string      $action      What happened: created, updated, deleted, assigned
     * @param Model       $subject     The model being acted on (Task, User, etc.)
     * @param string      $description Human-readable summary
     * @param array|null  $changes     Diff of what changed: ['field' => ['old' => x, 'new' => y]]
     */
    public function log(
        ?User  $user,
        string $action,
        Model  $subject,
        string $description,
        ?array $changes = null,
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'      => $user?->id,
            'action'       => $action,
            'subject_type' => get_class($subject),
            'subject_id'   => $subject->getKey(),
            'description'  => $description,
            'changes'      => $changes,
        ]);
    }

    /**
     * Get recent activity for the dashboard.
     *
     * @param int $limit How many entries to return
     */
    public function getRecent(int $limit = 20): Collection
    {
        return ActivityLog::with('user')
            ->latestFirst()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all activity for a specific model instance.
     *
     * Usage: $service->getForSubject($task) → all logs about this task
     */
    public function getForSubject(Model $subject): Collection
    {
        return ActivityLog::where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey())
            ->with('user')
            ->latestFirst()
            ->get();
    }
}
