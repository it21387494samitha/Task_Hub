<?php

namespace App\Livewire\Tasks;

use App\Enums\Role;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Board extends Component
{
    // ─── Filters ─────────────────────────────────────────
    public string $search = '';
    public string $priorityFilter = '';
    public string $assigneeFilter = '';

    // ─── Block reason modal ──────────────────────────────
    public bool $showBlockModal = false;
    public string $blockReason = '';
    public ?int $pendingBlockTaskId = null;

    /**
     * Move a task to a new status column (called by Alpine drag-and-drop).
     */
    public function moveTask(int $taskId, string $newStatus): void
    {
        $task = Task::findOrFail($taskId);
        $this->authorize('update', $task);

        // If moving to blocked, show the block reason modal
        if ($newStatus === TaskStatus::BLOCKED->value) {
            $this->pendingBlockTaskId = $taskId;
            $this->showBlockModal = true;
            return;
        }

        app(TaskService::class)->updateTask(Auth::user(), $task, [
            'status' => $newStatus,
        ]);

        $this->dispatch('toast', message: "Moved to {$this->statusLabel($newStatus)}.", type: 'success');
    }

    /**
     * Confirm blocking a task with a reason.
     */
    public function confirmBlock(): void
    {
        $this->validate(['blockReason' => 'required|string|min:3|max:500']);

        $task = Task::findOrFail($this->pendingBlockTaskId);
        $this->authorize('update', $task);

        app(TaskService::class)->updateTask(Auth::user(), $task, [
            'status'       => TaskStatus::BLOCKED->value,
            'block_reason' => $this->blockReason,
        ]);

        $this->showBlockModal = false;
        $this->blockReason = '';
        $this->pendingBlockTaskId = null;
        $this->dispatch('toast', message: 'Task blocked.', type: 'warning');
    }

    public function cancelBlock(): void
    {
        $this->showBlockModal = false;
        $this->blockReason = '';
        $this->pendingBlockTaskId = null;
    }

    private function statusLabel(string $value): string
    {
        return TaskStatus::tryFrom($value)?->label() ?? $value;
    }

    public function render()
    {
        $user = Auth::user();
        $query = Task::with(['assignee', 'creator']);

        // Developers only see their assigned tasks
        if ($user->role === Role::DEVELOPER) {
            $query->where('assigned_to', $user->id);
        }

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }
        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }
        if ($this->assigneeFilter) {
            $query->where('assigned_to', $this->assigneeFilter);
        }

        $tasks = $query->latest()->get();

        // Group tasks by status for the columns
        $columns = [];
        foreach (TaskStatus::cases() as $status) {
            $columns[$status->value] = $tasks->where('status', $status)->values();
        }

        return view('livewire.tasks.board', [
            'columns'    => $columns,
            'statuses'   => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
            'users'      => User::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
