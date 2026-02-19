<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
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
        // ── Users ──────────────────────────────────────────────
        $admin = User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@taskhub.test',
            'role'  => Role::ADMIN,
        ]);

        $lead = User::factory()->create([
            'name'  => 'Team Leader',
            'email' => 'lead@taskhub.test',
            'role'  => Role::TEAM_LEADER,
        ]);

        $dev1 = User::factory()->create([
            'name'  => 'Alice Developer',
            'email' => 'alice@taskhub.test',
            'role'  => Role::DEVELOPER,
        ]);

        $dev2 = User::factory()->create([
            'name'  => 'Bob Developer',
            'email' => 'bob@taskhub.test',
            'role'  => Role::DEVELOPER,
        ]);

        // ── Tasks ──────────────────────────────────────────────
        Task::create([
            'title'       => 'Set up CI/CD pipeline',
            'description' => 'Configure GitHub Actions for automated testing and deployment.',
            'priority'    => TaskPriority::HIGH,
            'status'      => TaskStatus::IN_PROGRESS,
            'assigned_to' => $dev1->id,
            'created_by'  => $lead->id,
            'due_date'    => now()->addDays(3),
        ]);

        Task::create([
            'title'       => 'Design landing page',
            'description' => 'Create responsive Tailwind layout for the public landing page.',
            'priority'    => TaskPriority::MEDIUM,
            'status'      => TaskStatus::TODO,
            'assigned_to' => $dev2->id,
            'created_by'  => $lead->id,
            'due_date'    => now()->addDays(7),
        ]);

        Task::create([
            'title'       => 'Fix login redirect bug',
            'description' => 'After login, users land on a 404 instead of the dashboard.',
            'priority'    => TaskPriority::CRITICAL,
            'status'      => TaskStatus::TODO,
            'assigned_to' => $dev1->id,
            'created_by'  => $admin->id,
            'due_date'    => now()->subDay(), // overdue!
        ]);

        Task::create([
            'title'       => 'Write API documentation',
            'description' => 'Document all REST endpoints using OpenAPI spec.',
            'priority'    => TaskPriority::LOW,
            'status'      => TaskStatus::DONE,
            'assigned_to' => $dev2->id,
            'created_by'  => $lead->id,
            'due_date'    => now()->subDays(2),
        ]);

        Task::create([
            'title'       => 'Database optimization',
            'description' => 'Add missing indexes and optimize slow queries.',
            'priority'    => TaskPriority::HIGH,
            'status'      => TaskStatus::BLOCKED,
            'assigned_to' => $dev1->id,
            'created_by'  => $lead->id,
            'due_date'    => now()->addDays(5),
        ]);

        Task::create([
            'title'       => 'Implement notification system',
            'description' => 'Email + in-app notifications for task assignments and due dates.',
            'priority'    => TaskPriority::MEDIUM,
            'status'      => TaskStatus::TODO,
            'assigned_to' => null, // unassigned
            'created_by'  => $admin->id,
            'due_date'    => now()->addDays(14),
        ]);
    }
}
