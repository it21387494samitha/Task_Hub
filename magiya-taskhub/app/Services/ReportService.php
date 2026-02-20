<?php

namespace App\Services;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * ReportService — Generates structured reports and CSV exports.
 */
class ReportService
{
    public function __construct(
        protected AnalyticsService $analytics
    ) {}

    // ─── Weekly Productivity Report ─────────────────────

    /**
     * Weekly productivity: tasks created, completed, blocked, avg cycle time.
     *
     * @param int $weeks Number of past weeks to include
     */
    public function weeklyProductivity(int $weeks = 4): array
    {
        $data = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = now()->subWeeks($i)->endOfWeek();

            $created = Task::whereBetween('created_at', [$start, $end])->count();
            $completed = Task::where('status', TaskStatus::DONE)
                ->whereBetween('completed_at', [$start, $end])
                ->count();

            $completedTasks = Task::where('status', TaskStatus::DONE)
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$start, $end])
                ->get();

            $avgCycle = $completedTasks->count() > 0
                ? round($completedTasks->avg(fn (Task $t) => $t->cycleTimeInHours()), 1)
                : 0;

            $data[] = [
                'week_label'      => $start->format('M d') . ' – ' . $end->format('M d'),
                'week_start'      => $start->toDateString(),
                'created'         => $created,
                'completed'       => $completed,
                'avg_cycle_hours' => $avgCycle,
                'net'             => $completed - $created,
            ];
        }

        return $data;
    }

    // ─── Priority vs Completion ─────────────────────────

    /**
     * Priority breakdown: how each priority level performs on completion rate and SLA.
     */
    public function priorityVsCompletion(): array
    {
        $slaHours = config('taskhub.sla_hours');
        $results = [];

        foreach (TaskPriority::cases() as $priority) {
            $tasks = Task::where('priority', $priority)->get();
            $total = $tasks->count();
            $done = $tasks->where('status', TaskStatus::DONE)->count();
            $overdue = $tasks->filter->isOverdue()->count();

            $completedTasks = $tasks->where('status', TaskStatus::DONE)->filter(fn ($t) => $t->completed_at);
            $avgCycle = $completedTasks->count() > 0
                ? round($completedTasks->avg(fn (Task $t) => $t->cycleTimeInHours()), 1)
                : 0;

            $results[] = [
                'priority'        => $priority->value,
                'label'           => $priority->label(),
                'color'           => $priority->color(),
                'total'           => $total,
                'completed'       => $done,
                'overdue'         => $overdue,
                'completion_rate' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
                'avg_cycle_hours' => $avgCycle,
                'sla_target'      => $slaHours[$priority->value] ?? 168,
            ];
        }

        return $results;
    }

    // ─── CSV Export ─────────────────────────────────────

    /**
     * Generate CSV content for task export.
     *
     * @param array $filters Optional filters: status, priority, team_id
     */
    public function generateCsvExport(array $filters = []): string
    {
        $query = Task::with(['assignee', 'creator']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        if (! empty($filters['team_id'])) {
            $teamUserIds = User::where('team_id', $filters['team_id'])->pluck('id');
            $query->whereIn('assigned_to', $teamUserIds);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'ID', 'Title', 'Status', 'Priority', 'Assignee', 'Creator',
            'Due Date', 'Created', 'Started', 'Completed', 'Cycle Time (h)',
            'Days in Status', 'Overdue', 'Tags',
        ];

        $rows = $tasks->map(fn (Task $t) => [
            $t->id,
            '"' . str_replace('"', '""', $t->title) . '"',
            $t->status->label(),
            $t->priority->label(),
            $t->assignee?->name ?? 'Unassigned',
            $t->creator?->name ?? 'System',
            $t->due_date?->toDateString() ?? '',
            $t->created_at->toDateString(),
            $t->started_at?->toDateString() ?? '',
            $t->completed_at?->toDateString() ?? '',
            $t->cycleTimeInHours() ?? '',
            $t->daysInCurrentStatus(),
            $t->isOverdue() ? 'Yes' : 'No',
            implode('; ', $t->tags ?? []),
        ]);

        $csv = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }

    // ─── Dashboard Report Data ──────────────────────────

    /**
     * Get all report data for admin dashboard (combined).
     */
    public function getDashboardReportData(): array
    {
        return [
            'weekly_productivity'    => $this->weeklyProductivity(4),
            'priority_vs_completion' => $this->priorityVsCompletion(),
        ];
    }
}
