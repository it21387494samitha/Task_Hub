<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Admin can do anything — this runs before all other checks.
     * Returning null lets it fall through to the specific method.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === Role::ADMIN) {
            return true;
        }

        return null; // fall through to specific checks below
    }

    /**
     * Who can view the task list?
     * Admin: all (handled by before)
     * Team Leader: all
     * Developer: only via scoped query (handled in service)
     */
    public function viewAny(User $user): bool
    {
        return true; // everyone can view (scoping handled in service)
    }

    /**
     * Who can view a specific task?
     * Admin: yes (before)
     * Team Leader: yes
     * Developer: only if assigned to them
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->role === Role::TEAM_LEADER) {
            return true;
        }

        return $task->assigned_to === $user->id;
    }

    /**
     * Who can create tasks?
     * Admin: yes (before)
     * Team Leader: yes
     * Developer: no
     */
    public function create(User $user): bool
    {
        return $user->role === Role::TEAM_LEADER;
    }

    /**
     * Who can update a task?
     * Admin: yes (before)
     * Team Leader: yes (any task)
     * Developer: only their assigned task
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->role === Role::TEAM_LEADER) {
            return true;
        }

        return $task->assigned_to === $user->id;
    }

    /**
     * Who can delete a task?
     * Admin only (handled by before, everyone else denied)
     */
    public function delete(User $user, Task $task): bool
    {
        return false; // only Admin via before()
    }

    /**
     * Who can assign/reassign tasks?
     * Admin: yes (before)
     * Team Leader: yes
     * Developer: no
     */
    public function assign(User $user, Task $task): bool
    {
        return $user->role === Role::TEAM_LEADER;
    }
}
