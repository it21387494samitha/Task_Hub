<?php

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Console\Command;

/**
 * Scheduled command: sends reminder notifications for tasks
 * whose due dates are approaching.
 *
 * - "imminent": due within 24 hours
 * - "approaching": due within 3 days
 *
 * Schedule: `php artisan tasks:send-reminders` — run daily.
 */
class SendDueDateReminders extends Command
{
    protected $signature = 'tasks:send-reminders';

    protected $description = 'Send notifications for tasks due within 24 hours or 3 days';

    public function handle(): int
    {
        $now = now();
        $sent = 0;

        // ── Imminent: due within 24 hours ──
        $imminent = Task::with('assignee')
            ->whereNotNull('due_date')
            ->whereNotNull('assigned_to')
            ->whereNotIn('status', [TaskStatus::DONE->value, TaskStatus::BLOCKED->value])
            ->whereBetween('due_date', [$now, $now->copy()->addHours(24)])
            ->get();

        foreach ($imminent as $task) {
            if ($task->assignee) {
                $task->assignee->notify(new TaskDueSoonNotification($task, 'imminent'));
                $sent++;
            }
        }
        $this->info("Imminent reminders sent: {$imminent->count()}");

        // ── Approaching: due within 3 days (but not within 24h — avoids duplicates) ──
        $approaching = Task::with('assignee')
            ->whereNotNull('due_date')
            ->whereNotNull('assigned_to')
            ->whereNotIn('status', [TaskStatus::DONE->value, TaskStatus::BLOCKED->value])
            ->whereBetween('due_date', [$now->copy()->addHours(24), $now->copy()->addDays(3)])
            ->get();

        foreach ($approaching as $task) {
            if ($task->assignee) {
                $task->assignee->notify(new TaskDueSoonNotification($task, 'approaching'));
                $sent++;
            }
        }
        $this->info("Approaching reminders sent: {$approaching->count()}");

        $this->info("Total reminders: {$sent}");

        return Command::SUCCESS;
    }
}
