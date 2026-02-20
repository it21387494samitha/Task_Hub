<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to the assignee when their task is updated by someone else.
 *
 * Channel: database only (no email — too noisy for every edit).
 *
 * MERN comparison:
 *   Like pushing to a Socket.IO "notifications" room, except
 *   Laravel persists it in the DB so the user sees it on next page load
 *   even if they were offline.
 */
class TaskUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task  $task,
        public User  $updatedBy,
        public array $changedFields,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $fieldNames = implode(', ', array_keys($this->changedFields));

        return [
            'type'           => 'task_updated',
            'task_id'        => $this->task->id,
            'task_title'     => $this->task->title,
            'updater_id'     => $this->updatedBy->id,
            'updater_name'   => $this->updatedBy->name,
            'changed_fields' => $this->changedFields,
            'message'        => "{$this->updatedBy->name} updated your task: {$this->task->title} ({$fieldNames})",
        ];
    }
}
