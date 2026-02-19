<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;

class StatsService
{
    /**
     * Get all dashboard statistics.
     *
     * @return array<string, mixed>
     */
    public function getDashboardStats(): array
    {
        return [
            'total_tasks'      => $this->totalTasks(),
            'tasks_by_status'  => $this->tasksByStatus(),
            'overdue_tasks'    => $this->overdueTasks(),
            'tasks_per_user'   => $this->tasksPerUser(),
        ];
    }

    /**
     * Total number of active tasks.
     */
    public function totalTasks(): int
    {
        return Task::count();
    }

    /**
     * Count of tasks grouped by status.
     *
     * @return array<string, int>  e.g. ['todo' => 3, 'in_progress' => 2, ...]
     */
    public function tasksByStatus(): array
    {
        $counts = Task::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses are present (even if 0)
        $result = [];
        foreach (TaskStatus::cases() as $status) {
            $result[$status->value] = $counts[$status->value] ?? 0;
        }

        return $result;
    }

    /**
     * Count of overdue tasks (past due date and not done).
     */
    public function overdueTasks(): int
    {
        return Task::query()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('status', '!=', TaskStatus::DONE)
            ->count();
    }

    /**
     * Tasks count per assigned user.
     *
     * @return \Illuminate\Support\Collection  [{ name, task_count }, ...]
     */
    public function tasksPerUser(): \Illuminate\Support\Collection
    {
        return User::query()
            ->withCount('assignedTasks')
            ->having('assigned_tasks_count', '>', 0)
            ->orderByDesc('assigned_tasks_count')
            ->get(['id', 'name'])
            ->map(fn (User $user) => [
                'name'       => $user->name,
                'task_count' => $user->assigned_tasks_count,
            ]);
    }
}
