<?php

use App\Enums\Role;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\ReportService;
use Livewire\Livewire;

// ═══════════════════════════════════════════════════════════
//  PHASE 2 — Analytics Engine Tests
// ═══════════════════════════════════════════════════════════

describe('AnalyticsService', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
        $this->service = app(AnalyticsService::class);
    });

    test('getTaskHealth returns correct structure', function () {
        Task::factory()->count(3)->create(['status' => TaskStatus::TODO]);
        Task::factory()->count(2)->create(['status' => TaskStatus::DONE]);

        $health = $this->service->getTaskHealth();

        expect($health)->toHaveKeys(['total', 'by_status', 'by_priority', 'overdue', 'blocked', 'stuck', 'health_score'])
            ->and($health['total'])->toBe(5)
            ->and($health['by_status'])->toBeArray()
            ->and($health['health_score'])->toBeGreaterThanOrEqual(0)
            ->and($health['health_score'])->toBeLessThanOrEqual(100);
    });

    test('health score decreases with overdue tasks', function () {
        // All healthy tasks
        Task::factory()->count(5)->create(['status' => TaskStatus::DONE]);
        $healthyScore = $this->service->calculateHealthScore(5, 0, 0, 0);

        // Add overdue
        $overdueScore = $this->service->calculateHealthScore(10, 5, 0, 0);

        expect($healthyScore)->toBeGreaterThan($overdueScore);
    });

    test('getCompletionRate calculates correct percentage', function () {
        // 3 completed, 7 open in last 7 days
        Task::factory()->count(3)->create([
            'status' => TaskStatus::DONE,
            'completed_at' => now(),
            'created_at' => now()->subDays(3),
        ]);
        Task::factory()->count(7)->create([
            'status' => TaskStatus::TODO,
            'created_at' => now()->subDays(3),
        ]);

        $rate = $this->service->getCompletionRate(7);

        expect($rate)->toHaveKeys(['completed', 'created', 'rate_percentage'])
            ->and($rate['completed'])->toBe(3)
            ->and($rate['created'])->toBe(10)
            ->and($rate['rate_percentage'])->toBe(30.0);
    });

    test('getWorkloadDistribution flags overloaded users', function () {
        $dev = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);

        // Create tasks exceeding the overload threshold (default: 10)
        Task::factory()->count(12)->create([
            'assigned_to' => $dev->id,
            'status' => TaskStatus::IN_PROGRESS,
        ]);

        $workload = $this->service->getWorkloadDistribution();
        $devWorkload = collect($workload)->firstWhere('user_id', $dev->id);

        expect($devWorkload)->not->toBeNull()
            ->and($devWorkload['open_tasks'])->toBe(12)
            ->and($devWorkload['is_overloaded'])->toBeTrue();
    });

    test('getOverdueReport lists overdue tasks', function () {
        Task::factory()->create([
            'status' => TaskStatus::IN_PROGRESS,
            'due_date' => now()->subDays(3),
        ]);
        Task::factory()->create([
            'status' => TaskStatus::DONE,
            'due_date' => now()->subDays(3),
        ]);

        $overdue = $this->service->getOverdueReport();

        // Only the non-done task should appear
        expect($overdue)->toHaveCount(1)
            ->and($overdue->first()['days_overdue'])->toBeGreaterThanOrEqual(3);
    });

    test('getAgingBuckets groups tasks by age', function () {
        Task::factory()->create([
            'status' => TaskStatus::TODO,
            'created_at' => now()->subDays(2),
        ]);
        Task::factory()->create([
            'status' => TaskStatus::IN_PROGRESS,
            'created_at' => now()->subDays(10),
        ]);

        $buckets = $this->service->getAgingBuckets();

        expect($buckets)->toBeArray();
    });

    test('getSlaCompliance returns per-priority data', function () {
        Task::factory()->create([
            'status' => TaskStatus::DONE,
            'priority' => TaskPriority::CRITICAL,
            'created_at' => now()->subHours(2),
            'completed_at' => now(),
        ]);

        $sla = $this->service->getSlaCompliance();

        expect($sla)->toBeArray();
        // Should have an entry for critical priority (keyed by priority value)
        expect($sla)->toHaveKey(TaskPriority::CRITICAL->value);
        $critical = $sla[TaskPriority::CRITICAL->value];
        expect($critical)->toHaveKeys(['total', 'breached', 'compliant', 'rate', 'sla_hours']);
    });

    test('getDashboardSummary returns compact card data', function () {
        $summary = $this->service->getDashboardSummary();

        expect($summary)->toHaveKeys(['total_tasks', 'overdue', 'blocked', 'completion_rate', 'active_users']);
    });
});

describe('ReportService', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
        $this->service = app(ReportService::class);
    });

    test('weeklyProductivity returns weeks array', function () {
        Task::factory()->count(5)->create([
            'status' => TaskStatus::DONE,
            'completed_at' => now()->subDays(2),
        ]);

        $weeks = $this->service->weeklyProductivity(4);

        expect($weeks)->toBeArray()
            ->and(count($weeks))->toBe(4);

        foreach ($weeks as $week) {
            expect($week)->toHaveKeys(['week_label', 'created', 'completed', 'net', 'avg_cycle_hours']);
        }
    });

    test('priorityVsCompletion returns per-priority data', function () {
        Task::factory()->create([
            'priority' => TaskPriority::HIGH,
            'status' => TaskStatus::DONE,
            'completed_at' => now(),
        ]);

        $data = $this->service->priorityVsCompletion();

        expect($data)->toBeArray();
        $high = collect($data)->firstWhere('priority', TaskPriority::HIGH->value);
        expect($high)->not->toBeNull()
            ->and($high['total'])->toBeGreaterThanOrEqual(1);
    });

    test('generateCsvExport produces valid CSV string', function () {
        Task::factory()->count(3)->create();

        $csv = $this->service->generateCsvExport();

        expect($csv)->toBeString()
            ->toContain('ID,Title')    // header row
            ->toContain("\n");         // multiple lines
    });

    test('getDashboardReportData returns weekly and priority', function () {
        $data = $this->service->getDashboardReportData();

        expect($data)->toHaveKeys(['weekly_productivity', 'priority_vs_completion']);
    });
});

// ═══════════════════════════════════════════════════════════
//  PHASE 3 — Admin Dashboard & Access Control Tests
// ═══════════════════════════════════════════════════════════

describe('Admin Access Control', function () {

    test('admin can access admin dashboard', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    });

    test('team leader can access admin dashboard', function () {
        $lead = User::factory()->create(['role' => Role::TEAM_LEADER]);

        $this->actingAs($lead)
            ->get(route('admin.dashboard'))
            ->assertOk();
    });

    test('developer cannot access admin dashboard', function () {
        $dev = User::factory()->create(['role' => Role::DEVELOPER]);

        $this->actingAs($dev)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    });

    test('guest is redirected from admin routes', function () {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    });

    test('admin can access users page', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.users'))
            ->assertOk();
    });

    test('admin can access teams page', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.teams'))
            ->assertOk();
    });

    test('developer cannot access users page', function () {
        $dev = User::factory()->create(['role' => Role::DEVELOPER]);

        $this->actingAs($dev)
            ->get(route('admin.users'))
            ->assertForbidden();
    });

    test('developer cannot access teams page', function () {
        $dev = User::factory()->create(['role' => Role::DEVELOPER]);

        $this->actingAs($dev)
            ->get(route('admin.teams'))
            ->assertForbidden();
    });
});

// ═══════════════════════════════════════════════════════════
//  PHASE 4 — User & Team Management Tests
// ═══════════════════════════════════════════════════════════

describe('UserManagement Livewire', function () {

    test('can render user management component', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\UserManagement::class)
            ->assertOk();
    });

    test('can create a new user', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\UserManagement::class)
            ->set('newName', 'John Doe')
            ->set('newEmail', 'john@example.com')
            ->set('newPassword', 'secret123')
            ->set('newRole', Role::DEVELOPER->value)
            ->call('createUser')
            ->assertDispatched('toast');

        expect(User::where('email', 'john@example.com')->exists())->toBeTrue();
    });

    test('can toggle user active status', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\UserManagement::class)
            ->call('toggleActive', $user->id);

        expect($user->fresh()->is_active)->toBeFalse();
    });

    test('cannot deactivate self', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\UserManagement::class)
            ->call('toggleActive', $admin->id);

        expect($admin->fresh()->is_active)->toBeTrue();
    });

    test('can change user role', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $user = User::factory()->create(['role' => Role::DEVELOPER]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\UserManagement::class)
            ->call('changeRole', $user->id, Role::TEAM_LEADER->value);

        expect($user->fresh()->role)->toBe(Role::TEAM_LEADER);
    });

    test('can assign user to team', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\UserManagement::class)
            ->call('assignTeam', $user->id, $team->id);

        expect($user->fresh()->team_id)->toBe($team->id);
    });

    test('can update existing user', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $user = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\UserManagement::class)
            ->call('openEditModal', $user->id)
            ->set('editName', 'New Name')
            ->set('editEmail', 'new@example.com')
            ->call('updateUser')
            ->assertDispatched('toast');

        $user->refresh();
        expect($user->name)->toBe('New Name')
            ->and($user->email)->toBe('new@example.com');
    });

    test('filters users by role', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        User::factory()->count(3)->create(['role' => Role::DEVELOPER]);
        User::factory()->count(2)->create(['role' => Role::TEAM_LEADER]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\UserManagement::class)
            ->set('roleFilter', Role::DEVELOPER->value)
            ->assertViewHas('users', function ($users) {
                return $users->every(fn ($u) => $u->role === Role::DEVELOPER);
            });
    });
});

describe('TeamManagement Livewire', function () {

    test('can render team management component', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\TeamManagement::class)
            ->assertOk();
    });

    test('can create a new team', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\TeamManagement::class)
            ->set('newName', 'Backend Team')
            ->set('newDescription', 'Handles APIs')
            ->call('createTeam')
            ->assertDispatched('toast');

        expect(Team::where('name', 'Backend Team')->exists())->toBeTrue();
    });

    test('can update a team', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $team = Team::factory()->create(['name' => 'Old Team']);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\TeamManagement::class)
            ->call('openEditModal', $team->id)
            ->set('editName', 'New Team')
            ->call('updateTeam')
            ->assertDispatched('toast');

        expect($team->fresh()->name)->toBe('New Team');
    });

    test('can delete team and unassign members', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $team = Team::factory()->create();
        $member = User::factory()->create(['team_id' => $team->id]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\TeamManagement::class)
            ->call('deleteTeam', $team->id)
            ->assertDispatched('toast');

        expect(Team::find($team->id))->toBeNull()
            ->and($member->fresh()->team_id)->toBeNull();
    });

    test('can remove member from team', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $team = Team::factory()->create();
        $member = User::factory()->create(['team_id' => $team->id]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\TeamManagement::class)
            ->call('removeMember', $member->id)
            ->assertDispatched('toast');

        expect($member->fresh()->team_id)->toBeNull();
    });

    test('can view team members', function () {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $team = Team::factory()->create();

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\TeamManagement::class)
            ->call('viewMembers', $team->id)
            ->assertSet('viewingTeamId', $team->id);
    });
});
