<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a task is soft-deleted.
 *
 * We pass the task (still accessible because it's soft-deleted, not gone)
 * and the user who performed the delete — needed for audit trails.
 */
class TaskDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task $task,
        public User $deletedBy,
    ) {}
}
