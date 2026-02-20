<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to the assignee when their task is deleted.
 *
 * Channel: database only.
 */
class TaskDeletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $deletedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'task_deleted',
            'task_id'      => $this->task->id,
            'task_title'   => $this->task->title,
            'deleter_id'   => $this->deletedBy->id,
            'deleter_name' => $this->deletedBy->name,
            'message'      => "{$this->deletedBy->name} deleted the task: {$this->task->title}",
        ];
    }
}
