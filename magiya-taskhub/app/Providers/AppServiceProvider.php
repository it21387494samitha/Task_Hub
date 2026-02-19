<?php

namespace App\Providers;

use App\Models\Task;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Event → Listener mapping is handled by Laravel's auto-discovery.
     * Laravel 12 scans App\Listeners and matches handle() type-hints
     * to the correct Event class automatically — no manual wiring needed.
     *
     * If you needed manual control, you'd use Event::listen() here.
     */
    public function boot(): void
    {
        // ── Policies ──────────────────────────────────────
        Gate::policy(Task::class, TaskPolicy::class);
    }
}
