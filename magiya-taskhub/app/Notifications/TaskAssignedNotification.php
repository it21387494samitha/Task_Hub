<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when a task is assigned (or reassigned) to a user.
 *
 * Channels:
 *   - database → in-app notification bell
 *   - mail     → email to the new assignee
 *
 * MERN comparison:
 *   In Express you'd call sendgrid/nodemailer manually and insert
 *   a doc into a "notifications" MongoDB collection.
 *   Laravel wraps both into a single Notification class — one place,
 *   multiple channels. Think of it like a "multi-transport message".
 */
class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task  $task,
        public User  $assigner,
        public ?User $previousAssignee = null,
    ) {}

    /**
     * Which channels to deliver on.
     * 'database' = stored in notifications table (in-app bell).
     * 'mail'     = sends an email.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Database representation — stored as JSON in the `data` column.
     * This is what the NotificationBell reads to render the list.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'task_assigned',
            'task_id'     => $this->task->id,
            'task_title'  => $this->task->title,
            'assigner_id' => $this->assigner->id,
            'assigner_name' => $this->assigner->name,
            'message'     => "{$this->assigner->name} assigned you the task: {$this->task->title}",
        ];
    }

    /**
     * Email representation.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/tasks/{$this->task->id}/edit");

        return (new MailMessage)
            ->subject("Task Assigned: {$this->task->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->assigner->name} has assigned you a task:")
            ->line("**{$this->task->title}**")
            ->line("Priority: {$this->task->priority->label()}")
            ->line("Due: " . ($this->task->due_date?->format('M d, Y') ?? 'No deadline'))
            ->action('View Task', $url)
            ->line('Good luck!');
    }
}
