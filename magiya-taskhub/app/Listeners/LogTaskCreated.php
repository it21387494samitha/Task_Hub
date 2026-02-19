<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

/**
 * Listener: reacts to TaskCreated events.
 *
 * MERN comparison:
 *   eventBus.on('task.created', (task) => { console.log(...) })
 *
 * Now does TWO things:
 *   1. File log (for quick debugging / server logs)
 *   2. DB activity log (for the audit trail UI on the dashboard)
 */
class LogTaskCreated
{
    public function __construct(
        protected ActivityService $activityService,
    ) {}

    public function handle(TaskCreated $event): void
    {
        // File log (quick grep-able output)
        Log::info('Task created', [
            'task_id'    => $event->task->id,
            'title'      => $event->task->title,
            'created_by' => $event->creator->name,
        ]);

        // DB audit trail
        $assigneeName = $event->task->assignee?->name ?? 'nobody';

        $this->activityService->log(
            user:        $event->creator,
            action:      'created',
            subject:     $event->task,
            description: "{$event->creator->name} created task: {$event->task->title} (assigned to {$assigneeName})",
        );
    }
}
