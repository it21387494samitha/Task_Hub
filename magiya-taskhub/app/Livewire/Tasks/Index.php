<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
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

    // ─── Flash messages ──────────────────────────────────
    public string $successMessage = '';

    public function updatedSearch(): void
    {
        // Livewire auto-calls this when $search changes (like onChange in React)
    }

    /**
     * Delete a task (Admin only).
     */
    public function deleteTask(int $taskId): void
    {
        $repo = app(TaskRepository::class);
        $service = new TaskService($repo);
        $task = $repo->findOrFail($taskId);

        try {
            $service->deleteTask(Auth::user(), $task);
            $this->successMessage = "Task '{$task->title}' deleted.";
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->successMessage = '';
        }
    }

    public function render()
    {
        $repo = app(TaskRepository::class);
        $service = new TaskService($repo);

        $filters = [];
        if ($this->search) {
            $filters['search'] = $this->search;
        }
        if ($this->statusFilter) {
            $filters['status'] = $this->statusFilter;
        }
        if ($this->priorityFilter) {
            $filters['priority'] = $this->priorityFilter;
        }

        $tasks = $service->getTasksForUser(Auth::user(), $filters);

        return view('livewire.tasks.index', [
            'tasks' => $tasks,
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ]);
    }
}
