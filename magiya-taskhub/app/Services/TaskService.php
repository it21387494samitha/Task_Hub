<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\TaskStatus;
use App\Events\TaskAssigned;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class TaskService
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    /**
     * Get tasks visible to the given user.
     *
     * - Admin & Team Leader: see all tasks
     * - Developer: only their assigned tasks
     */
    public function getTasksForUser(User $user, array $filters = []): Collection
    {
        // Developers only see their own tasks
        if ($user->role === Role::DEVELOPER) {
            $filters['assigned_to'] = $user->id;
        }

        return $this->taskRepository->query($filters)->get();
    }

    /**
     * Create a new task.
     *
     * Uses Policy: only Admin and Team Leader can create.
     * Fires: TaskCreated event.
     *
     * @param array<string, mixed> $data
     */
    public function createTask(User $creator, array $data): Task
    {
        $this->authorize($creator, 'create', Task::class);

        $data['created_by'] = $creator->id;
        $data['status'] = $data['status'] ?? TaskStatus::TODO;

        $task = $this->taskRepository->create($data);

        // Fire event — listeners handle logging, notifications, etc.
        TaskCreated::dispatch($task, $creator);

        // If the task was assigned during creation, also fire TaskAssigned
        // so the assignee gets notified (same listener handles it).
        if ($task->assigned_to) {
            $assignee = $task->assignee;   // eager-loaded User
            TaskAssigned::dispatch($task, $creator, $assignee, null);
        }

        return $task;
    }

    /**
     * Update an existing task.
     *
     * Uses Policy: Admin/Lead can update any, Developer only their own (status only).
     * Fires: TaskUpdated event (with diff of what changed).
     *
     * @param array<string, mixed> $data
     */
    public function updateTask(User $user, Task $task, array $data): Task
    {
        $this->authorize($user, 'update', $task);

        // Developers can only change status — strip everything else
        if ($user->role === Role::DEVELOPER) {
            $data = array_intersect_key($data, ['status' => true]);
        }

        // Auto-fill timing fields based on status transitions
        if (isset($data['status'])) {
            $data = $this->applyTimingFields($task, $data);
        }

        // Capture old values BEFORE the update (for the diff)
        $oldValues = $task->only(array_keys($data));

        $task = $this->taskRepository->update($task, $data);

        // Build a changed-fields diff: ['field' => ['old' => x, 'new' => y]]
        $changedFields = $this->buildDiff($oldValues, $task->only(array_keys($data)));

        if (! empty($changedFields)) {
            TaskUpdated::dispatch($task, $user, $changedFields);
        }

        return $task;
    }

    /**
     * Delete a task (soft delete).
     *
     * Uses Policy: Admin only.
     * Fires: TaskDeleted event.
     */
    public function deleteTask(User $user, Task $task): bool
    {
        $this->authorize($user, 'delete', $task);

        $result = $this->taskRepository->delete($task);

        TaskDeleted::dispatch($task, $user);

        return $result;
    }

    /**
     * Assign a task to a user.
     *
     * Uses Policy: Admin and Team Leader only.
     * Fires: TaskAssigned event (with previous and new assignee).
     */
    public function assignTask(User $assigner, Task $task, ?User $assignee): Task
    {
        $this->authorize($assigner, 'assign', $task);

        // Remember the previous assignee before we overwrite
        $previousAssignee = $task->assignee;

        $task = $this->taskRepository->update($task, [
            'assigned_to' => $assignee?->id,
        ]);

        TaskAssigned::dispatch($task, $assigner, $assignee, $previousAssignee);

        return $task;
    }

    // ─── Private Helpers ─────────────────────────────────

    /**
     * Check authorization using Laravel's Gate/Policy system.
     *
     * @param User $user
     * @param string $ability   e.g. 'create', 'update', 'delete', 'assign'
     * @param mixed $target     Task instance or Task::class
     */
    private function authorize(User $user, string $ability, mixed $target): void
    {
        $response = Gate::forUser($user)->inspect($ability, $target);

        if ($response->denied()) {
            throw new AuthorizationException(
                $response->message() ?? 'This action is unauthorized.'
            );
        }
    }

    /**
     * Apply automatic timing fields based on status transitions.
     *
     * - To In Progress → set started_at (if not already set)
     * - To Done → set completed_at
     * - To Blocked → set blocked_at
     * - From Blocked to anything else → clear blocked_at/block_reason
     *
     * @param Task $task Current task (before update)
     * @param array<string, mixed> $data Incoming update data
     * @return array<string, mixed> Modified data with timing fields
     */
    private function applyTimingFields(Task $task, array $data): array
    {
        $newStatus = $data['status'] instanceof TaskStatus
            ? $data['status']
            : TaskStatus::tryFrom($data['status']);

        $oldStatus = $task->status;

        if ($newStatus === null || $newStatus === $oldStatus) {
            return $data;
        }

        // Moving TO In Progress → stamp started_at (only first time)
        if ($newStatus === TaskStatus::IN_PROGRESS && ! $task->started_at) {
            $data['started_at'] = now();
        }

        // Moving TO Done → stamp completed_at
        if ($newStatus === TaskStatus::DONE) {
            $data['completed_at'] = now();
        }

        // Moving TO Blocked → stamp blocked_at
        if ($newStatus === TaskStatus::BLOCKED) {
            $data['blocked_at'] = now();
        }

        // Moving FROM Blocked → clear block fields
        if ($oldStatus === TaskStatus::BLOCKED && $newStatus !== TaskStatus::BLOCKED) {
            $data['blocked_at'] = null;
            $data['block_reason'] = null;
        }

        return $data;
    }

    /**
     * Build a diff array showing what fields changed.
     *
     * Returns: ['status' => ['old' => 'todo', 'new' => 'in_progress']]
     * Only includes fields that actually changed — skips identical values.
     *
     * We cast enums to their string value for clean logging.
     */
    private function buildDiff(array $oldValues, array $newValues): array
    {
        $diff = [];

        foreach ($oldValues as $key => $oldVal) {
            $newVal = $newValues[$key] ?? null;

            // Cast enums to string for comparison
            $oldStr = $oldVal instanceof \BackedEnum ? $oldVal->value : $oldVal;
            $newStr = $newVal instanceof \BackedEnum ? $newVal->value : $newVal;

            if ((string) $oldStr !== (string) $newStr) {
                $diff[$key] = [
                    'old' => $oldStr,
                    'new' => $newStr,
                ];
            }
        }

        return $diff;
    }
}
