<?php

namespace App\Livewire;

use App\Services\ActivityService;
use App\Services\StatsService;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = app(StatsService::class)->getDashboardStats();
        $recentActivity = app(ActivityService::class)->getRecent(10);

        return view('livewire.dashboard', [
            'stats'          => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }
}
