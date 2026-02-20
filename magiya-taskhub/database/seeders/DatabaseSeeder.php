<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TemplateType;
use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Teams ──────────────────────────────────────────────

        // We need a user for created_by, so create admin first as a bare user
        $admin = User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@taskhub.test',
            'role'  => Role::ADMIN,
        ]);

        $backendTeam = Team::create([
            'name'        => 'Backend Squad',
            'slug'        => 'backend-squad',
            'description' => 'Server-side development, APIs, and database work.',
            'created_by'  => $admin->id,
        ]);

        $frontendTeam = Team::create([
            'name'        => 'Frontend Squad',
            'slug'        => 'frontend-squad',
            'description' => 'UI/UX, client-side development, and design systems.',
            'created_by'  => $admin->id,
        ]);

        // ── Users ──────────────────────────────────────────────

        // Assign admin to no team (oversees all)
        $admin->update(['team_id' => null]);

        $lead = User::factory()->create([
            'name'    => 'Team Leader',
            'email'   => 'lead@taskhub.test',
            'role'    => Role::TEAM_LEADER,
            'team_id' => $backendTeam->id,
        ]);

        $dev1 = User::factory()->create([
            'name'    => 'Alice Developer',
            'email'   => 'alice@taskhub.test',
            'role'    => Role::DEVELOPER,
            'team_id' => $backendTeam->id,
        ]);

        $dev2 = User::factory()->create([
            'name'    => 'Bob Developer',
            'email'   => 'bob@taskhub.test',
            'role'    => Role::DEVELOPER,
            'team_id' => $frontendTeam->id,
        ]);

        $dev3 = User::factory()->create([
            'name'    => 'Charlie Developer',
            'email'   => 'charlie@taskhub.test',
            'role'    => Role::DEVELOPER,
            'team_id' => $frontendTeam->id,
        ]);

        $leadFront = User::factory()->create([
            'name'    => 'Diana Lead',
            'email'   => 'diana@taskhub.test',
            'role'    => Role::TEAM_LEADER,
            'team_id' => $frontendTeam->id,
        ]);

        // One disabled user for testing
        $disabled = User::factory()->create([
            'name'      => 'Eve Inactive',
            'email'     => 'eve@taskhub.test',
            'role'      => Role::DEVELOPER,
            'team_id'   => $backendTeam->id,
            'is_active' => false,
        ]);

        // ── Tasks (with realistic timing data) ─────────────────

        // 1. In Progress task — started 2 days ago
        Task::create([
            'title'       => 'Set up CI/CD pipeline',
            'description' => 'Configure GitHub Actions for automated testing and deployment.',
            'priority'    => TaskPriority::HIGH,
            'status'      => TaskStatus::IN_PROGRESS,
            'assigned_to' => $dev1->id,
            'created_by'  => $lead->id,
            'due_date'    => now()->addDays(3),
            'started_at'  => now()->subDays(2),
        ]);

        // 2. Todo — not started
        Task::create([
            'title'       => 'Design landing page',
            'description' => 'Create responsive Tailwind layout for the public landing page.',
            'priority'    => TaskPriority::MEDIUM,
            'status'      => TaskStatus::TODO,
            'assigned_to' => $dev2->id,
            'created_by'  => $lead->id,
            'due_date'    => now()->addDays(7),
        ]);

        // 3. Overdue critical — created 3 days ago, still todo
        Task::create([
            'title'       => 'Fix login redirect bug',
            'description' => 'After login, users land on a 404 instead of the dashboard.',
            'priority'    => TaskPriority::CRITICAL,
            'status'      => TaskStatus::TODO,
            'assigned_to' => $dev1->id,
            'created_by'  => $admin->id,
            'due_date'    => now()->subDay(),
            'tags'        => ['prod_issue', 'hotfix'],
        ]);

        // 4. Done — completed yesterday, full cycle time
        Task::create([
            'title'        => 'Write API documentation',
            'description'  => 'Document all REST endpoints using OpenAPI spec.',
            'priority'     => TaskPriority::LOW,
            'status'       => TaskStatus::DONE,
            'assigned_to'  => $dev2->id,
            'created_by'   => $lead->id,
            'due_date'     => now()->subDays(2),
            'started_at'   => now()->subDays(5),
            'completed_at' => now()->subDay(),
        ]);

        // 5. Blocked — with block reason and timing
        Task::create([
            'title'        => 'Database optimization',
            'description'  => 'Add missing indexes and optimize slow queries.',
            'priority'     => TaskPriority::HIGH,
            'status'       => TaskStatus::BLOCKED,
            'assigned_to'  => $dev1->id,
            'created_by'   => $lead->id,
            'due_date'     => now()->addDays(5),
            'started_at'   => now()->subDays(4),
            'blocked_at'   => now()->subDays(1),
            'block_reason' => 'Waiting for DBA approval on index changes',
        ]);

        // 6. Unassigned todo
        Task::create([
            'title'       => 'Implement notification system',
            'description' => 'Email + in-app notifications for task assignments and due dates.',
            'priority'    => TaskPriority::MEDIUM,
            'status'      => TaskStatus::TODO,
            'assigned_to' => null,
            'created_by'  => $admin->id,
            'due_date'    => now()->addDays(14),
        ]);

        // 7. Done task — fast turnaround (frontend team)
        Task::create([
            'title'        => 'Update button component library',
            'description'  => 'Migrate all buttons to the new design system tokens.',
            'priority'     => TaskPriority::MEDIUM,
            'status'       => TaskStatus::DONE,
            'assigned_to'  => $dev3->id,
            'created_by'   => $leadFront->id,
            'due_date'     => now()->subDays(1),
            'started_at'   => now()->subDays(3),
            'completed_at' => now()->subDays(1),
        ]);

        // 8. In Progress — stuck for 7 days (bottleneck)
        Task::create([
            'title'       => 'Refactor authentication middleware',
            'description' => 'Simplify the auth middleware stack and add rate limiting.',
            'priority'    => TaskPriority::HIGH,
            'status'      => TaskStatus::IN_PROGRESS,
            'assigned_to' => $dev1->id,
            'created_by'  => $lead->id,
            'due_date'    => now()->addDays(1),
            'started_at'  => now()->subDays(7),
            'tags'        => ['tech_debt'],
        ]);

        // 9. Critical todo — due in 24h
        Task::create([
            'title'       => 'Patch payment gateway vulnerability',
            'description' => 'Urgent security patch for the Stripe integration.',
            'priority'    => TaskPriority::CRITICAL,
            'status'      => TaskStatus::TODO,
            'assigned_to' => $dev3->id,
            'created_by'  => $admin->id,
            'due_date'    => now()->addDay(),
            'tags'        => ['prod_issue', 'release_blocker'],
        ]);

        // 10. Done — old task for reporting
        Task::create([
            'title'        => 'Set up error monitoring',
            'description'  => 'Integrate Sentry for production error tracking.',
            'priority'     => TaskPriority::HIGH,
            'status'       => TaskStatus::DONE,
            'assigned_to'  => $dev2->id,
            'created_by'   => $lead->id,
            'due_date'     => now()->subDays(10),
            'started_at'   => now()->subDays(14),
            'completed_at' => now()->subDays(10),
        ]);

        // 11. In Progress — frontend team
        Task::create([
            'title'       => 'Build dashboard charts',
            'description' => 'Create reusable chart components for analytics.',
            'priority'    => TaskPriority::MEDIUM,
            'status'      => TaskStatus::IN_PROGRESS,
            'assigned_to' => $dev3->id,
            'created_by'  => $leadFront->id,
            'due_date'    => now()->addDays(5),
            'started_at'  => now()->subDays(1),
        ]);

        // 12. Blocked with release blocker tag
        Task::create([
            'title'        => 'Deploy v2.0 release candidate',
            'description'  => 'Final deployment to staging for UAT approval.',
            'priority'     => TaskPriority::CRITICAL,
            'status'       => TaskStatus::BLOCKED,
            'assigned_to'  => $dev2->id,
            'created_by'   => $admin->id,
            'due_date'     => now()->addDays(2),
            'started_at'   => now()->subDays(3),
            'blocked_at'   => now(),
            'block_reason' => 'Staging environment is down — DevOps investigating',
            'tags'         => ['release_blocker'],
        ]);

        // ── Task Templates ─────────────────────────────────────

        TaskTemplate::create([
            'name'                 => 'Bug Report',
            'type'                 => TemplateType::BUG,
            'default_priority'     => TaskPriority::HIGH,
            'description_template' => "## Bug Description\n\n## Steps to Reproduce\n1. \n2. \n3. \n\n## Expected Behavior\n\n## Actual Behavior\n\n## Environment\n- Browser: \n- OS: ",
            'created_by'           => $admin->id,
        ]);

        TaskTemplate::create([
            'name'                 => 'Feature Request',
            'type'                 => TemplateType::FEATURE,
            'default_priority'     => TaskPriority::MEDIUM,
            'description_template' => "## Feature Summary\n\n## User Story\nAs a [role], I want [feature] so that [benefit].\n\n## Acceptance Criteria\n- [ ] \n- [ ] \n\n## Design Notes\n",
            'created_by'           => $admin->id,
        ]);

        TaskTemplate::create([
            'name'                 => 'Hotfix',
            'type'                 => TemplateType::HOTFIX,
            'default_priority'     => TaskPriority::CRITICAL,
            'description_template' => "## Issue\n\n## Impact\n- Affected users: \n- Severity: \n\n## Root Cause\n\n## Fix\n\n## Rollback Plan\n",
            'created_by'           => $admin->id,
        ]);
    }
}
