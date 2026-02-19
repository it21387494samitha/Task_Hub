<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

/**
 * Logs task assignment changes.
 *
 * Captures both the new assignee and who was previously assigned,
 * making it easy to trace task ownership history.
 */
class LogTaskAssigned
{
    public function __construct(
        protected ActivityService $activityService,
    ) {}

    public function handle(TaskAssigned $event): void
    {
        $newName = $event->newAssignee?->name ?? 'Unassigned';
        $prevName = $event->previousAssignee?->name ?? 'Nobody';

        // File log
        Log::info('Task assigned', [
            'task_id'           => $event->task->id,
            'title'             => $event->task->title,
            'assigned_by'       => $event->assigner->name,
            'new_assignee'      => $newName,
            'previous_assignee' => $prevName,
        ]);

        // DB audit trail
        $this->activityService->log(
            user:        $event->assigner,
            action:      'assigned',
            subject:     $event->task,
            description: "{$event->assigner->name} assigned task: {$event->task->title} to {$newName} (was: {$prevName})",
            changes:     [
                'assigned_to' => [
                    'old' => $prevName,
                    'new' => $newName,
                ],
            ],
        );
    }
}
