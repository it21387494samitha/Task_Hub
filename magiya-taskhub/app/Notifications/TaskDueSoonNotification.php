<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when a task's due date is approaching (within 24h or 3 days).
 */
class TaskDueSoonNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task   $task,
        public string $urgency = 'approaching', // 'approaching' (3d) or 'imminent' (24h)
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->urgency === 'imminent' ? 'due within 24 hours' : 'due within 3 days';

        return [
            'type'       => 'task_due_soon',
            'task_id'    => $this->task->id,
            'task_title' => $this->task->title,
            'urgency'    => $this->urgency,
            'due_date'   => $this->task->due_date->toDateString(),
            'message'    => "Task \"{$this->task->title}\" is {$label}.",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->urgency === 'imminent' ? 'Due Within 24 Hours' : 'Due Within 3 Days';

        return (new MailMessage)
            ->subject("[TaskHub] {$label}: {$this->task->title}")
            ->greeting("Task Due Reminder")
            ->line("The task \"{$this->task->title}\" is {$label}.")
            ->line("Due date: {$this->task->due_date->format('M d, Y')}")
            ->action('View Task', url("/tasks/{$this->task->id}"))
            ->line('Please ensure this task is completed on time.');
    }
}
