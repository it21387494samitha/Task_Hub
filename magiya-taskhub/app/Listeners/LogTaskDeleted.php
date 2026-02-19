<?php

namespace App\Listeners;

use App\Events\TaskDeleted;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

/**
 * Logs task deletion for audit trail.
 *
 * Since tasks are soft-deleted, the data is still in the DB.
 * The activity log preserves the record even if someone force-deletes later.
 */
class LogTaskDeleted
{
    public function __construct(
        protected ActivityService $activityService,
    ) {}

    public function handle(TaskDeleted $event): void
    {
        // File log
        Log::info('Task deleted', [
            'task_id'    => $event->task->id,
            'title'      => $event->task->title,
            'deleted_by' => $event->deletedBy->name,
        ]);

        // DB audit trail
        $this->activityService->log(
            user:        $event->deletedBy,
            action:      'deleted',
            subject:     $event->task,
            description: "{$event->deletedBy->name} deleted task: {$event->task->title}",
        );
    }
}
