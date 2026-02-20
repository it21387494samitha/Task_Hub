<?php

namespace App\Services;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * AnalyticsService — Enterprise analytics engine for the admin dashboard.
 *
 * Provides deep insight into task health, team performance, SLA compliance,
 * workload distribution, bottlenecks, and aging patterns.
 */
class AnalyticsService
{
    // ─── Task Health Overview ────────────────────────────

    /**
     * Get the complete task health snapshot.
     */
    public function getTaskHealth(): array
    {
        $total = Task::count();
        $byStatus = [];
        foreach (TaskStatus::cases() as $status) {
            $byStatus[$status->value] = Task::where('status', $status)->count();
        }
        $byPriority = [];
        foreach (TaskPriority::cases() as $priority) {
            $byPriority[$priority->value] = Task::where('priority', $priority)->count();
        }

        $overdue = Task::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('status', '!=', TaskStatus::DONE)
            ->count();

        $blocked = $byStatus[TaskStatus::BLOCKED->value] ?? 0;

        $stuckTasks = Task::where('status', TaskStatus::IN_PROGRESS)->get()->filter->isStuck();

        return [
            'total'        => $total,
            'by_status'    => $byStatus,
            'by_priority'  => $byPriority,
            'overdue'      => $overdue,
            'blocked'      => $blocked,
            'stuck'        => $stuckTasks->count(),
            'health_score' => $this->calculateHealthScore($total, $overdue, $blocked, $stuckTasks->count()),
        ];
    }

    /**
     * Calculate a 0-100 health score.
     * Perfect = 100. Deductions for overdue, blocked, stuck tasks.
     */
    public function calculateHealthScore(int $total, int $overdue, int $blocked, int $stuck): int
    {
        if ($total === 0) {
            return 100;
        }

        $score = 100;
        $score -= (int) (($overdue / $total) * 40);  // overdue = up to -40
        $score -= (int) (($blocked / $total) * 30);   // blocked = up to -30
        $score -= (int) (($stuck / $total) * 30);     // stuck = up to -30

        return max(0, min(100, $score));
    }

    // ─── Completion Metrics ─────────────────────────────

    /**
     * Get completion rate for a given period.
     *
     * @param int $days Number of past days to analyze
     */
    public function getCompletionRate(int $days = 30): array
    {
        $since = now()->subDays($days);

        $completed = Task::where('status', TaskStatus::DONE)
            ->where('completed_at', '>=', $since)
            ->count();

        $created = Task::where('created_at', '>=', $since)->count();

        return [
            'period_days'    => $days,
            'completed'      => $completed,
            'created'        => $created,
            'rate_percentage' => $created > 0 ? round(($completed / $created) * 100, 1) : 0,
        ];
    }

    /**
     * Get average cycle time (created → completed) in hours, grouped by priority.
     *
     * @param int $days Number of past days
     */
    public function getAverageCycleTime(int $days = 30): array
    {
        $since = now()->subDays($days);

        $tasks = Task::where('status', TaskStatus::DONE)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $since)
            ->get();

        $overall = $tasks->avg(fn (Task $t) => $t->cycleTimeInHours()) ?? 0;

        $byPriority = [];
        foreach (TaskPriority::cases() as $priority) {
            $filtered = $tasks->where('priority', $priority);
            $byPriority[$priority->value] = [
                'count'      => $filtered->count(),
                'avg_hours'  => round($filtered->avg(fn (Task $t) => $t->cycleTimeInHours()) ?? 0, 1),
            ];
        }

        return [
            'period_days'   => $days,
            'overall_hours' => round($overall, 1),
            'by_priority'   => $byPriority,
        ];
    }

    // ─── Workload Distribution ──────────────────────────

    /**
     * Get workload distribution per user (open tasks, priority breakdown, overload flag).
     */
    public function getWorkloadDistribution(): Collection
    {
        $thresholds = config('taskhub.overload_threshold');

        return User::active()
            ->with(['assignedTasks' => fn ($q) => $q->where('status', '!=', TaskStatus::DONE)])
            ->get()
            ->map(function (User $user) use ($thresholds) {
                $openTasks = $user->assignedTasks;
                $totalOpen = $openTasks->count();
                $criticalCount = $openTasks->whereIn('priority', [TaskPriority::CRITICAL, TaskPriority::HIGH])->count();
                $criticalPercentage = $totalOpen > 0 ? round(($criticalCount / $totalOpen) * 100) : 0;

                return [
                    'user_id'             => $user->id,
                    'name'                => $user->name,
                    'role'                => $user->role->value,
                    'team'                => $user->team?->name ?? 'Unassigned',
                    'open_tasks'          => $totalOpen,
                    'critical_count'      => $criticalCount,
                    'critical_percentage' => $criticalPercentage,
                    'is_overloaded'       => $totalOpen > $thresholds['max_open_tasks']
                                             || $criticalPercentage > $thresholds['critical_percentage'],
                    'by_status'           => [
                        'todo'        => $openTasks->where('status', TaskStatus::TODO)->count(),
                        'in_progress' => $openTasks->where('status', TaskStatus::IN_PROGRESS)->count(),
                        'blocked'     => $openTasks->where('status', TaskStatus::BLOCKED)->count(),
                    ],
                ];
            })
            ->sortByDesc('open_tasks')
            ->values();
    }

    // ─── Overdue & Due-Soon Reports ─────────────────────

    /**
     * Get detailed overdue report.
     */
    public function getOverdueReport(): Collection
    {
        return Task::with(['assignee', 'creator'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('status', '!=', TaskStatus::DONE)
            ->orderBy('due_date')
            ->get()
            ->map(fn (Task $t) => [
                'id'            => $t->id,
                'title'         => $t->title,
                'priority'      => $t->priority->value,
                'status'        => $t->status->value,
                'assignee'      => $t->assignee?->name ?? 'Unassigned',
                'due_date'      => $t->due_date->toDateString(),
                'days_overdue'  => (int) $t->due_date->diffInDays(now()),
                'sla_breached'  => $this->isSlaBreached($t),
            ]);
    }

    /**
     * Get tasks due within a given window.
     *
     * @param int $hours Hours from now
     */
    public function getDueSoonTasks(int $hours = 72): Collection
    {
        return Task::with(['assignee'])
            ->whereNotNull('due_date')
            ->where('due_date', '>', now())
            ->where('due_date', '<=', now()->addHours($hours))
            ->where('status', '!=', TaskStatus::DONE)
            ->orderBy('due_date')
            ->get()
            ->map(fn (Task $t) => [
                'id'         => $t->id,
                'title'      => $t->title,
                'priority'   => $t->priority->value,
                'status'     => $t->status->value,
                'assignee'   => $t->assignee?->name ?? 'Unassigned',
                'due_date'   => $t->due_date->toDateString(),
                'hours_left' => round(now()->diffInHours($t->due_date, false), 1),
            ]);
    }

    // ─── Task Aging ─────────────────────────────────────

    /**
     * Group open tasks into aging buckets (defined in config/taskhub.php).
     */
    public function getAgingBuckets(): array
    {
        $buckets = config('taskhub.aging_buckets');
        $openTasks = Task::where('status', '!=', TaskStatus::DONE)->get();

        return array_map(function ($bucket) use ($openTasks) {
            $count = $openTasks->filter(function (Task $t) use ($bucket) {
                $days = $t->daysInCurrentStatus();
                return $days >= $bucket['min'] && $days <= $bucket['max'];
            })->count();

            return [
                'label' => $bucket['label'],
                'color' => $bucket['color'],
                'count' => $count,
            ];
        }, $buckets);
    }

    // ─── Bottleneck Detection ───────────────────────────

    /**
     * Identify bottlenecks: stuck tasks, blocked tasks, overloaded users.
     */
    public function getBottlenecks(): array
    {
        $stuckTasks = Task::with('assignee')
            ->where('status', TaskStatus::IN_PROGRESS)
            ->get()
            ->filter->isStuck()
            ->map(fn (Task $t) => [
                'id'       => $t->id,
                'title'    => $t->title,
                'assignee' => $t->assignee?->name ?? 'Unassigned',
                'days'     => $t->daysInCurrentStatus(),
            ])
            ->values();

        $blockedTasks = Task::with('assignee')
            ->where('status', TaskStatus::BLOCKED)
            ->get()
            ->map(fn (Task $t) => [
                'id'           => $t->id,
                'title'        => $t->title,
                'assignee'     => $t->assignee?->name ?? 'Unassigned',
                'block_reason' => $t->block_reason,
                'days_blocked' => $t->daysInCurrentStatus(),
            ]);

        $overloadedUsers = $this->getWorkloadDistribution()
            ->where('is_overloaded', true)
            ->values();

        return [
            'stuck_tasks'     => $stuckTasks,
            'blocked_tasks'   => $blockedTasks,
            'overloaded_users' => $overloadedUsers,
            'total_issues'    => $stuckTasks->count() + $blockedTasks->count() + $overloadedUsers->count(),
        ];
    }

    // ─── Team Stats ─────────────────────────────────────

    /**
     * Get analytics for a specific team (or all teams).
     */
    public function getTeamStats(?int $teamId = null): Collection
    {
        $teams = $teamId ? Team::where('id', $teamId)->get() : Team::all();

        return $teams->map(function (Team $team) {
            $memberIds = $team->members()->pluck('id');
            $tasks = Task::whereIn('assigned_to', $memberIds)->get();

            $total = $tasks->count();
            $done = $tasks->where('status', TaskStatus::DONE)->count();
            $overdue = $tasks->filter->isOverdue()->count();
            $inProgress = $tasks->where('status', TaskStatus::IN_PROGRESS)->count();
            $blocked = $tasks->where('status', TaskStatus::BLOCKED)->count();

            $completedTasks = $tasks->where('status', TaskStatus::DONE)->filter(fn ($t) => $t->completed_at);
            $avgCycleTime = $completedTasks->count() > 0
                ? round($completedTasks->avg(fn (Task $t) => $t->cycleTimeInHours()), 1)
                : 0;

            return [
                'team_id'        => $team->id,
                'name'           => $team->name,
                'slug'           => $team->slug,
                'member_count'   => $team->activeMembers()->count(),
                'leader'         => $team->leader()?->name ?? 'None',
                'total_tasks'    => $total,
                'completed'      => $done,
                'in_progress'    => $inProgress,
                'blocked'        => $blocked,
                'overdue'        => $overdue,
                'completion_rate' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
                'avg_cycle_hours' => $avgCycleTime,
            ];
        });
    }

    // ─── SLA Compliance ─────────────────────────────────

    /**
     * Get SLA compliance report.
     */
    public function getSlaCompliance(): array
    {
        $slaHours = config('taskhub.sla_hours');
        $results = [];

        foreach (TaskPriority::cases() as $priority) {
            $tasks = Task::where('priority', $priority)->get();
            $total = $tasks->count();
            $breached = $tasks->filter(fn (Task $t) => $this->isSlaBreached($t))->count();

            $results[$priority->value] = [
                'total'       => $total,
                'breached'    => $breached,
                'compliant'   => $total - $breached,
                'rate'        => $total > 0 ? round((($total - $breached) / $total) * 100, 1) : 100,
                'sla_hours'   => $slaHours[$priority->value] ?? 168,
            ];
        }

        return $results;
    }

    /**
     * Check if a task has breached its SLA.
     */
    private function isSlaBreached(Task $task): bool
    {
        $slaHours = config('taskhub.sla_hours');
        $limit = $slaHours[$task->priority->value] ?? 168;

        // Done tasks: check if cycle time exceeded SLA
        if ($task->status === TaskStatus::DONE && $task->completed_at) {
            return $task->cycleTimeInHours() > $limit;
        }

        // Open tasks: check if elapsed time exceeds SLA
        return $task->created_at->diffInHours(now()) > $limit;
    }

    // ─── Summary for Dashboard Cards ────────────────────

    /**
     * Get a compact summary for the admin dashboard top cards.
     */
    public function getDashboardSummary(): array
    {
        $health = $this->getTaskHealth();
        $completion = $this->getCompletionRate(7);
        $cycleTime = $this->getAverageCycleTime(30);

        return [
            'health_score'    => $health['health_score'],
            'total_tasks'     => $health['total'],
            'overdue'         => $health['overdue'],
            'blocked'         => $health['blocked'],
            'stuck'           => $health['stuck'],
            'weekly_completed' => $completion['completed'],
            'weekly_created'  => $completion['created'],
            'completion_rate' => $completion['rate_percentage'],
            'avg_cycle_hours' => $cycleTime['overall_hours'],
            'active_users'    => User::active()->count(),
            'total_teams'     => Team::count(),
        ];
    }
}
