<?php

use App\Console\Commands\SendDueDateReminders;
use App\Enums\Role;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\NotificationSetting;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

// ═══════════════════════════════════════════════════════════
//  PHASE 7 — Notifications & Settings Tests
// ═══════════════════════════════════════════════════════════

describe('SendDueDateReminders Command', function () {

    test('sends imminent notification for task due within 24 hours', function () {
        Notification::fake();

        $user = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        Task::factory()->create([
            'assigned_to' => $user->id,
            'status'      => TaskStatus::IN_PROGRESS,
            'due_date'    => now()->addHours(12),
        ]);

        $this->artisan('tasks:send-reminders')->assertExitCode(0);

        Notification::assertSentTo($user, TaskDueSoonNotification::class, function ($notification) {
            return $notification->urgency === 'imminent';
        });
    });

    test('sends approaching notification for task due in 2 days', function () {
        Notification::fake();

        $user = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        Task::factory()->create([
            'assigned_to' => $user->id,
            'status'      => TaskStatus::TODO,
            'due_date'    => now()->addDays(2),
        ]);

        $this->artisan('tasks:send-reminders')->assertExitCode(0);

        Notification::assertSentTo($user, TaskDueSoonNotification::class, function ($notification) {
            return $notification->urgency === 'approaching';
        });
    });

    test('does NOT send notification for completed tasks', function () {
        Notification::fake();

        $user = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        Task::factory()->create([
            'assigned_to' => $user->id,
            'status'      => TaskStatus::DONE,
            'due_date'    => now()->addHours(12),
        ]);

        $this->artisan('tasks:send-reminders')->assertExitCode(0);

        Notification::assertNothingSent();
    });

    test('does NOT send notification for tasks without assignee', function () {
        Notification::fake();

        Task::factory()->create([
            'assigned_to' => null,
            'status'      => TaskStatus::TODO,
            'due_date'    => now()->addHours(12),
        ]);

        $this->artisan('tasks:send-reminders')->assertExitCode(0);

        Notification::assertNothingSent();
    });

    test('does NOT send notification for tasks due beyond 3 days', function () {
        Notification::fake();

        $user = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        Task::factory()->create([
            'assigned_to' => $user->id,
            'status'      => TaskStatus::TODO,
            'due_date'    => now()->addDays(5),
        ]);

        $this->artisan('tasks:send-reminders')->assertExitCode(0);

        Notification::assertNothingSent();
    });

    test('does NOT send notification for already overdue tasks', function () {
        Notification::fake();

        $user = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        Task::factory()->create([
            'assigned_to' => $user->id,
            'status'      => TaskStatus::IN_PROGRESS,
            'due_date'    => now()->subDays(1),
        ]);

        $this->artisan('tasks:send-reminders')->assertExitCode(0);

        Notification::assertNothingSent();
    });
});

// ═══════════════════════════════════════════════════════════
//  TaskDueSoonNotification
// ═══════════════════════════════════════════════════════════

describe('TaskDueSoonNotification', function () {

    test('notification returns correct array format', function () {
        $task = Task::factory()->create(['due_date' => now()->addHours(12)]);
        $notification = new TaskDueSoonNotification($task, 'imminent');

        $data = $notification->toArray($task->assignee);

        expect($data)->toHaveKeys(['type', 'task_id', 'task_title', 'urgency', 'due_date', 'message'])
            ->and($data['type'])->toBe('task_due_soon')
            ->and($data['urgency'])->toBe('imminent')
            ->and($data['task_id'])->toBe($task->id);
    });

    test('notification returns database channel', function () {
        $task = Task::factory()->create();
        $notification = new TaskDueSoonNotification($task, 'approaching');

        $user = User::factory()->create();
        $channels = $notification->via($user);

        expect($channels)->toContain('database');
    });
});

// ═══════════════════════════════════════════════════════════
//  NotificationSetting Model
// ═══════════════════════════════════════════════════════════

describe('NotificationSetting Model', function () {

    test('can create a notification setting', function () {
        $user = User::factory()->create();

        $setting = NotificationSetting::create([
            'user_id'          => $user->id,
            'event_type'       => 'task_assigned',
            'email_enabled'    => true,
            'database_enabled' => false,
        ]);

        expect($setting)->not->toBeNull()
            ->and($setting->email_enabled)->toBeTrue()
            ->and($setting->database_enabled)->toBeFalse();
    });

    test('user_id and event_type combination is unique', function () {
        $user = User::factory()->create();

        NotificationSetting::create([
            'user_id'    => $user->id,
            'event_type' => 'task_assigned',
        ]);

        // Attempting to create a duplicate should fail
        expect(fn () => NotificationSetting::create([
            'user_id'    => $user->id,
            'event_type' => 'task_assigned',
        ]))->toThrow(\Illuminate\Database\QueryException::class);
    });

    test('isEnabled returns true when no explicit setting exists', function () {
        $user = User::factory()->create();

        expect(NotificationSetting::isEnabled($user->id, 'task_assigned', 'database'))->toBeTrue()
            ->and(NotificationSetting::isEnabled($user->id, 'task_assigned', 'email'))->toBeTrue();
    });

    test('isEnabled returns correct value for existing setting', function () {
        $user = User::factory()->create();

        NotificationSetting::create([
            'user_id'          => $user->id,
            'event_type'       => 'task_due_soon',
            'email_enabled'    => false,
            'database_enabled' => true,
        ]);

        expect(NotificationSetting::isEnabled($user->id, 'task_due_soon', 'email'))->toBeFalse()
            ->and(NotificationSetting::isEnabled($user->id, 'task_due_soon', 'database'))->toBeTrue();
    });

    test('labelFor returns correct label', function () {
        expect(NotificationSetting::labelFor('task_assigned'))->toBe('Task Assigned to Me')
            ->and(NotificationSetting::labelFor('unknown_event'))->toBe('Unknown event');
    });

    test('user has notificationSettings relationship', function () {
        $user = User::factory()->create();

        NotificationSetting::create([
            'user_id'    => $user->id,
            'event_type' => 'task_assigned',
        ]);

        expect($user->notificationSettings)->toHaveCount(1);
    });

    test('setting belongs to user', function () {
        $user = User::factory()->create();

        $setting = NotificationSetting::create([
            'user_id'    => $user->id,
            'event_type' => 'comment_added',
        ]);

        expect($setting->user->id)->toBe($user->id);
    });
});

// ═══════════════════════════════════════════════════════════
//  Notification Settings Livewire UI
// ═══════════════════════════════════════════════════════════

describe('Notification Settings UI', function () {

    beforeEach(function () {
        $this->user = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
    });

    test('user can view notification settings page', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\NotificationSettings::class)
            ->assertStatus(200)
            ->assertSee('Task Assigned to Me')
            ->assertSee('Task Due Soon');
    });

    test('user can toggle a notification channel', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\NotificationSettings::class)
            ->call('toggle', 'task_assigned', 'email')
            ->assertDispatched('toast');

        $setting = NotificationSetting::where('user_id', $this->user->id)
            ->where('event_type', 'task_assigned')
            ->first();

        // Toggled from default (true) to false
        expect($setting->email_enabled)->toBeFalse();
    });

    test('toggling twice returns to original state', function () {
        $component = Livewire::actingAs($this->user)
            ->test(\App\Livewire\NotificationSettings::class)
            ->call('toggle', 'task_due_soon', 'database')
            ->call('toggle', 'task_due_soon', 'database');

        $setting = NotificationSetting::where('user_id', $this->user->id)
            ->where('event_type', 'task_due_soon')
            ->first();

        expect($setting->database_enabled)->toBeTrue();
    });

    test('enable all sets all channels to true', function () {
        // First disable something
        NotificationSetting::create([
            'user_id'          => $this->user->id,
            'event_type'       => 'task_assigned',
            'email_enabled'    => false,
            'database_enabled' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\NotificationSettings::class)
            ->call('enableAll');

        $setting = NotificationSetting::where('user_id', $this->user->id)
            ->where('event_type', 'task_assigned')
            ->first();

        expect($setting->email_enabled)->toBeTrue()
            ->and($setting->database_enabled)->toBeTrue();
    });

    test('disable all sets all channels to false', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\NotificationSettings::class)
            ->call('disableAll');

        $settings = NotificationSetting::where('user_id', $this->user->id)->get();

        foreach ($settings as $setting) {
            expect($setting->email_enabled)->toBeFalse()
                ->and($setting->database_enabled)->toBeFalse();
        }
    });

    test('invalid event type is ignored', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\NotificationSettings::class)
            ->call('toggle', 'nonexistent_event', 'email');

        expect(NotificationSetting::where('user_id', $this->user->id)->count())->toBe(0);
    });

    test('invalid channel is ignored', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\NotificationSettings::class)
            ->call('toggle', 'task_assigned', 'sms');

        expect(NotificationSetting::where('user_id', $this->user->id)->count())->toBe(0);
    });
});

// ═══════════════════════════════════════════════════════════
//  Route access
// ═══════════════════════════════════════════════════════════

describe('Notification Settings Route', function () {

    test('authenticated user can access notification settings', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get('/notifications/settings')
            ->assertStatus(200);
    });

    test('unauthenticated user is redirected to login', function () {
        $this->get('/notifications/settings')
            ->assertRedirect('/login');
    });
});
