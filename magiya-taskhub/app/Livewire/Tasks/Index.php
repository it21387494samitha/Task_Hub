<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    // ─── Filters ─────────────────────────────────────────
    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $sortBy = 'latest';

    // ─── Bulk operations ─────────────────────────────────
    public array $selected = [];
    public bool $selectAll = false;
    public string $bulkAction = '';
    public string $bulkValue = '';

    // ─── Flash messages ──────────────────────────────────
    public string $successMessage = '';

    public function updatedSearch(): void
    {
        // Livewire auto-calls this when $search changes
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            // Select all visible task IDs
            $this->selected = $this->getVisibleTasks()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    /**
     * Execute a bulk action on selected tasks.
     */
    public function executeBulk(): void
    {
        if (empty($this->selected) || !$this->bulkAction) {
            return;
        }

        $count = 0;
        $service = app(TaskService::class);
        $user = Auth::user();

        foreach ($this->selected as $taskId) {
            $task = Task::find($taskId);
            if (!$task) continue;

            try {
                match ($this->bulkAction) {
                    'status' => $service->updateTask($user, $task, ['status' => $this->bulkValue]),
                    'priority' => $service->updateTask($user, $task, ['priority' => $this->bulkValue]),
                    'delete' => $service->deleteTask($user, $task),
                    default => null,
                };
                $count++;
            } catch (\Exception) {
                // Skip tasks user can't modify
            }
        }

        $this->selected = [];
        $this->selectAll = false;
        $this->bulkAction = '';
        $this->bulkValue = '';
        $this->dispatch('toast', message: "{$count} task(s) updated.", type: 'success');
    }

    /**
     * Delete a task (Admin only).
     */
    public function deleteTask(int $taskId): void
    {
        $task = app(TaskRepository::class)->findOrFail($taskId);

        try {
            app(TaskService::class)->deleteTask(Auth::user(), $task);
            $this->successMessage = "Task '{$task->title}' deleted.";
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->successMessage = '';
        }
    }

    private function getVisibleTasks()
    {
        $filters = [];
        if ($this->search) $filters['search'] = $this->search;
        if ($this->statusFilter) $filters['status'] = $this->statusFilter;
        if ($this->priorityFilter) $filters['priority'] = $this->priorityFilter;

        return app(TaskService::class)->getTasksForUser(Auth::user(), $filters);
    }

    public function render()
    {
        $tasks = $this->getVisibleTasks();

        // Apply sorting
        $tasks = match ($this->sortBy) {
            'due_date' => $tasks->sortBy('due_date'),
            'priority' => $tasks->sortByDesc(fn ($t) => array_search($t->priority, TaskPriority::cases())),
            'title'    => $tasks->sortBy('title'),
            'oldest'   => $tasks->sortBy('created_at'),
            default    => $tasks, // 'latest' — already sorted by latest from repo
        };

        return view('livewire.tasks.index', [
            'tasks'      => $tasks->values(),
            'statuses'   => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ]);
    }
}
