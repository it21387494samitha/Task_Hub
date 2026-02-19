<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a task is assigned or reassigned to a user.
 *
 * $previousAssignee is null when the task was unassigned before.
 * $newAssignee is null when the task is being unassigned.
 */
class TaskAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task  $task,
        public User  $assigner,
        public ?User $newAssignee,
        public ?User $previousAssignee,
    ) {}
}
