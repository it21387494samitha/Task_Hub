<?php

namespace App\Livewire\Admin;

use App\Services\AnalyticsService;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin Dashboard — Analytics & Insights.
 *
 * This is the analytics command center for admins & team leaders.
 * All data is pulled from AnalyticsService and ReportService.
 */
class Dashboard extends Component
{
    public string $cycleTimePeriod = '30';
    public string $completionPeriod = '7';

    /**
     * Export tasks as CSV download.
     */
    public function exportCsv(): StreamedResponse
    {
        $report = app(ReportService::class);
        $csv = $report->generateCsvExport();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'taskhub-export-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        $analytics = app(AnalyticsService::class);
        $report = app(ReportService::class);

        $summary    = $analytics->getDashboardSummary();
        $health     = $analytics->getTaskHealth();
        $workload   = $analytics->getWorkloadDistribution();
        $aging      = $analytics->getAgingBuckets();
        $bottlenecks = $analytics->getBottlenecks();
        $teamStats  = $analytics->getTeamStats();
        $sla        = $analytics->getSlaCompliance();
        $overdue    = $analytics->getOverdueReport();
        $dueSoon    = $analytics->getDueSoonTasks(72);
        $completion = $analytics->getCompletionRate((int) $this->completionPeriod);
        $cycleTime  = $analytics->getAverageCycleTime((int) $this->cycleTimePeriod);
        $reportData = $report->getDashboardReportData();

        return view('livewire.admin.dashboard', [
            'summary'      => $summary,
            'health'       => $health,
            'workload'     => $workload,
            'aging'        => $aging,
            'bottlenecks'  => $bottlenecks,
            'teamStats'    => $teamStats,
            'sla'          => $sla,
            'overdue'      => $overdue,
            'dueSoon'      => $dueSoon,
            'completion'   => $completion,
            'cycleTime'    => $cycleTime,
            'reportData'   => $reportData,
            'user'         => Auth::user(),
        ]);
    }
}
