<?php

namespace App\Repositories;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Get all tasks (with relationships loaded).
     */
    public function all(): Collection
    {
        return Task::with(['assignee', 'creator'])->latest()->get();
    }

    /**
     * Find a single task by ID.
     */
    public function findOrFail(int $id): Task
    {
        return Task::with(['assignee', 'creator'])->findOrFail($id);
    }

    /**
     * Create a new task.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Update an existing task.
     *
     * @param array<string, mixed> $data
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh(['assignee', 'creator']);
    }

    /**
     * Soft-delete a task.
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Get tasks assigned to a specific user.
     */
    public function getByAssignee(User $user): Collection
    {
        return Task::with(['assignee', 'creator'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->get();
    }

    /**
     * Get tasks filtered by status.
     */
    public function getByStatus(TaskStatus $status): Collection
    {
        return Task::with(['assignee', 'creator'])
            ->where('status', $status)
            ->latest()
            ->get();
    }

    /**
     * Get overdue tasks (past due_date and not done).
     */
    public function getOverdue(): Collection
    {
        return Task::with(['assignee', 'creator'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('status', '!=', TaskStatus::DONE)
            ->latest('due_date')
            ->get();
    }

    /**
     * Build a query with optional filters.
     *
     * @param array<string, mixed> $filters  Possible keys: status, priority, assigned_to, search
     */
    public function query(array $filters = []): Builder
    {
        $query = Task::with(['assignee', 'creator']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['search'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->latest();
    }
}
