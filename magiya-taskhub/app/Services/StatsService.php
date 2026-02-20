<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;

/**
 * StatsService — now role-aware.
 *
 * MERN comparison:
 *   In a MERN app you'd filter stats in the API route handler:
 *     if (req.user.role === 'developer') query.assignedTo = req.user._id;
 *
 *   Laravel does the same thing here, but in a dedicated Service class.
 *   The key idea: the Service decides WHAT data to show per role,
 *   the Blade view decides HOW to display it.
 */
class StatsService
{
    /**
     * Get dashboard statistics scoped to the given user's role.
     *
     * Admin & Team Leader → full org-wide stats
     * Developer → only their own assigned tasks
     */
    public function getDashboardStats(User $user): array
    {
        $baseQuery = $this->scopedQuery($user);

        return [
            'total_tasks'     => (clone $baseQuery)->count(),
            'tasks_by_status' => $this->tasksByStatus($user),
            'overdue_tasks'   => $this->overdueTasks($user),
            'tasks_per_user'  => $this->tasksPerUser($user),
            'user_role'       => $user->role->value,  // Pass role to Blade for conditional UI
        ];
    }

    /**
     * Count of tasks grouped by status (scoped to user's role).
     */
    public function tasksByStatus(User $user): array
    {
        $counts = $this->scopedQuery($user)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses present (even if 0)
        $result = [];
        foreach (TaskStatus::cases() as $status) {
            $result[$status->value] = $counts[$status->value] ?? 0;
        }

        return $result;
    }

    /**
     * Count of overdue tasks (scoped to user's role).
     */
    public function overdueTasks(User $user): int
    {
        return $this->scopedQuery($user)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('status', '!=', TaskStatus::DONE)
            ->count();
    }

    /**
     * Tasks count per assigned user.
     *
     * Admin & Team Leader: see all developers' task counts.
     * Developer: just their own count (rendered differently in Blade).
     */
    public function tasksPerUser(User $user): \Illuminate\Support\Collection
    {
        if ($user->role === Role::DEVELOPER) {
            // Developer only sees their own count
            return collect([[
                'name'       => $user->name . ' (You)',
                'task_count' => Task::where('assigned_to', $user->id)->count(),
            ]]);
        }

        // Admin & Team Leader: see all users with assigned tasks
        return User::query()
            ->withCount('assignedTasks')
            ->having('assigned_tasks_count', '>', 0)
            ->orderByDesc('assigned_tasks_count')
            ->get(['id', 'name'])
            ->map(fn (User $u) => [
                'name'       => $u->name,
                'task_count' => $u->assigned_tasks_count,
            ]);
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Build a base Task query scoped to the user's visibility.
     *
     * Admin & Team Leader: no filter (see all).
     * Developer: only tasks assigned to them.
     */
    private function scopedQuery(User $user)
    {
        $query = Task::query();

        if ($user->role === Role::DEVELOPER) {
            $query->where('assigned_to', $user->id);
        }

        return $query;
    }
}
