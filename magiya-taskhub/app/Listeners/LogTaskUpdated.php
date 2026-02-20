<?php

namespace App\Listeners;

use App\Events\TaskUpdated;
use App\Notifications\TaskUpdatedNotification;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

/**
 * Logs task update details including which fields changed.
 *
 * The $event->changedFields array looks like:
 *   ['status' => ['old' => 'todo', 'new' => 'in_progress'], 'priority' => [...]]
 */
class LogTaskUpdated
{
    public function __construct(
        protected ActivityService $activityService,
    ) {}

    public function handle(TaskUpdated $event): void
    {
        // File log
        Log::info('Task updated', [
            'task_id'        => $event->task->id,
            'title'          => $event->task->title,
            'updated_by'     => $event->user->name,
            'changed_fields' => $event->changedFields,
        ]);

        // Build human-readable summary of changes
        $fieldNames = implode(', ', array_keys($event->changedFields));

        // DB audit trail
        $this->activityService->log(
            user:        $event->user,
            action:      'updated',
            subject:     $event->task,
            description: "{$event->user->name} updated task: {$event->task->title} (changed: {$fieldNames})",
            changes:     $event->changedFields,
        );

        // Notify the assignee (skip if they updated their own task)
        $assignee = $event->task->assignee;
        if ($assignee && $assignee->id !== $event->user->id) {
            $assignee->notify(
                new TaskUpdatedNotification($event->task, $event->user, $event->changedFields)
            );
        }
    }
}
