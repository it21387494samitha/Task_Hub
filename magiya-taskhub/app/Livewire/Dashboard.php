<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Models\ActivityLog;
use App\Services\ActivityService;
use App\Services\StatsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        // Stats are now scoped by role (developer sees only their tasks)
        $stats = app(StatsService::class)->getDashboardStats($user);

        // Activity feed: admin/lead see all, developer sees only their own actions
        if ($user->role === Role::DEVELOPER) {
            $recentActivity = ActivityLog::where('user_id', $user->id)
                ->with('user')
                ->latestFirst()
                ->limit(10)
                ->get();
        } else {
            $recentActivity = app(ActivityService::class)->getRecent(10);
        }

        return view('livewire.dashboard', [
            'stats'          => $stats,
            'recentActivity' => $recentActivity,
            'user'           => $user,
        ]);
    }
}
