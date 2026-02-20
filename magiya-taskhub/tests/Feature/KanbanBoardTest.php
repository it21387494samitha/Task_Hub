<?php

use App\Enums\Role;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

// ═══════════════════════════════════════════════════════════
//  PHASE 6 — Kanban Board & Enhanced Features Tests
// ═══════════════════════════════════════════════════════════

describe('Kanban Board', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
        $this->developer = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
    });

    test('admin can view kanban board', function () {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Board::class)
            ->assertStatus(200);
    });

    test('developer can view kanban board', function () {
        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Board::class)
            ->assertStatus(200);
    });

    test('board groups tasks by status', function () {
        Task::factory()->create(['status' => TaskStatus::TODO, 'created_by' => $this->admin->id]);
        Task::factory()->create(['status' => TaskStatus::IN_PROGRESS, 'created_by' => $this->admin->id]);
        Task::factory()->create(['status' => TaskStatus::DONE, 'created_by' => $this->admin->id]);

        $component = Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Board::class);

        $columns = $component->viewData('columns');

        expect($columns[TaskStatus::TODO->value])->toHaveCount(1)
            ->and($columns[TaskStatus::IN_PROGRESS->value])->toHaveCount(1)
            ->and($columns[TaskStatus::DONE->value])->toHaveCount(1);
    });

    test('admin can move task to a different status', function () {
        $task = Task::factory()->create([
            'status'     => TaskStatus::TODO,
            'created_by' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Board::class)
            ->call('moveTask', $task->id, TaskStatus::IN_PROGRESS->value);

        $task->refresh();
        expect($task->status)->toBe(TaskStatus::IN_PROGRESS);
    });

    test('moving task to blocked opens block modal', function () {
        $task = Task::factory()->create([
            'status'     => TaskStatus::TODO,
            'created_by' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Board::class)
            ->call('moveTask', $task->id, TaskStatus::BLOCKED->value)
            ->assertSet('showBlockModal', true)
            ->assertSet('pendingBlockTaskId', $task->id);
    });

    test('confirm block on board sets task blocked with reason', function () {
        $task = Task::factory()->create([
            'status'     => TaskStatus::TODO,
            'created_by' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Board::class)
            ->call('moveTask', $task->id, TaskStatus::BLOCKED->value)
            ->set('blockReason', 'Blocked by dependency')
            ->call('confirmBlock');

        $task->refresh();
        expect($task->status)->toBe(TaskStatus::BLOCKED)
            ->and($task->block_reason)->toBe('Blocked by dependency');
    });

    test('cancel block resets modal state', function () {
        $task = Task::factory()->create([
            'status'     => TaskStatus::TODO,
            'created_by' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Board::class)
            ->call('moveTask', $task->id, TaskStatus::BLOCKED->value)
            ->call('cancelBlock')
            ->assertSet('showBlockModal', false)
            ->assertSet('blockReason', '')
            ->assertSet('pendingBlockTaskId', null);
    });

    test('board search filter works', function () {
        Task::factory()->create([
            'title'      => 'Fix login bug',
            'created_by' => $this->admin->id,
            'status'     => TaskStatus::TODO,
        ]);
        Task::factory()->create([
            'title'      => 'Design homepage',
            'created_by' => $this->admin->id,
            'status'     => TaskStatus::TODO,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Board::class)
            ->set('search', 'login');

        $columns = $component->viewData('columns');
        $todoTasks = $columns[TaskStatus::TODO->value];

        expect($todoTasks)->toHaveCount(1)
            ->and($todoTasks->first()->title)->toBe('Fix login bug');
    });

    test('board priority filter works', function () {
        Task::factory()->create([
            'priority'   => TaskPriority::CRITICAL,
            'created_by' => $this->admin->id,
            'status'     => TaskStatus::TODO,
        ]);
        Task::factory()->create([
            'priority'   => TaskPriority::LOW,
            'created_by' => $this->admin->id,
            'status'     => TaskStatus::TODO,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Board::class)
            ->set('priorityFilter', TaskPriority::CRITICAL->value);

        $columns = $component->viewData('columns');
        $allTasks = collect($columns)->flatten();

        expect($allTasks)->toHaveCount(1)
            ->and($allTasks->first()->priority)->toBe(TaskPriority::CRITICAL);
    });

    test('developer only sees assigned tasks on board', function () {
        Task::factory()->create([
            'assigned_to' => $this->developer->id,
            'created_by'  => $this->admin->id,
            'status'      => TaskStatus::TODO,
        ]);
        Task::factory()->create([
            'created_by' => $this->admin->id,
            'status'     => TaskStatus::TODO,
        ]);

        $component = Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Board::class);

        $columns = $component->viewData('columns');
        $allTasks = collect($columns)->flatten();

        expect($allTasks)->toHaveCount(1);
    });
});

// ═══════════════════════════════════════════════════════════
//  Bulk Operations
// ═══════════════════════════════════════════════════════════

describe('Bulk Operations', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
    });

    test('admin can bulk change task status', function () {
        $tasks = Task::factory()->count(3)->create([
            'status'     => TaskStatus::TODO,
            'created_by' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Index::class)
            ->set('selected', $tasks->pluck('id')->map(fn ($id) => (string) $id)->toArray())
            ->set('bulkAction', 'status')
            ->set('bulkValue', TaskStatus::IN_PROGRESS->value)
            ->call('executeBulk');

        foreach ($tasks as $task) {
            $task->refresh();
            expect($task->status)->toBe(TaskStatus::IN_PROGRESS);
        }
    });

    test('admin can bulk change task priority', function () {
        $tasks = Task::factory()->count(2)->create([
            'priority'   => TaskPriority::LOW,
            'created_by' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Index::class)
            ->set('selected', $tasks->pluck('id')->map(fn ($id) => (string) $id)->toArray())
            ->set('bulkAction', 'priority')
            ->set('bulkValue', TaskPriority::CRITICAL->value)
            ->call('executeBulk');

        foreach ($tasks as $task) {
            $task->refresh();
            expect($task->priority)->toBe(TaskPriority::CRITICAL);
        }
    });

    test('admin can bulk delete tasks', function () {
        $tasks = Task::factory()->count(3)->create(['created_by' => $this->admin->id]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Index::class)
            ->set('selected', $tasks->pluck('id')->map(fn ($id) => (string) $id)->toArray())
            ->set('bulkAction', 'delete')
            ->call('executeBulk');

        expect(Task::count())->toBe(0);
    });

    test('bulk execute without selection does nothing', function () {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Index::class)
            ->set('selected', [])
            ->set('bulkAction', 'status')
            ->set('bulkValue', TaskStatus::DONE->value)
            ->call('executeBulk');

        // No errors, no changes
    });

    test('select all toggles all visible task ids', function () {
        $tasks = Task::factory()->count(3)->create(['created_by' => $this->admin->id]);

        $component = Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Index::class)
            ->set('selectAll', true);

        expect(count($component->get('selected')))->toBe(3);

        $component->set('selectAll', false);
        expect(count($component->get('selected')))->toBe(0);
    });
});

// ═══════════════════════════════════════════════════════════
//  Sorting
// ═══════════════════════════════════════════════════════════

describe('Task Sorting', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
    });

    test('tasks can be sorted by title', function () {
        Task::factory()->create(['title' => 'Zebra task', 'created_by' => $this->admin->id]);
        Task::factory()->create(['title' => 'Alpha task', 'created_by' => $this->admin->id]);

        $component = Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Index::class)
            ->set('sortBy', 'title');

        $tasks = $component->viewData('tasks');
        expect($tasks->first()->title)->toBe('Alpha task');
    });

    test('tasks can be sorted by due date', function () {
        Task::factory()->create(['due_date' => now()->addDays(10), 'created_by' => $this->admin->id]);
        Task::factory()->create(['due_date' => now()->addDays(1), 'created_by' => $this->admin->id]);

        $component = Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Index::class)
            ->set('sortBy', 'due_date');

        $tasks = $component->viewData('tasks');
        expect($tasks->first()->due_date->lt($tasks->last()->due_date))->toBeTrue();
    });
});
