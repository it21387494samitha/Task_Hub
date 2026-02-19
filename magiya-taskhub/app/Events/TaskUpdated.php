<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a task is updated.
 *
 * Carries both old and new values so listeners can determine
 * what changed — essential for activity logging and notifications.
 *
 * $changedFields example: ['status' => ['old' => 'todo', 'new' => 'in_progress']]
 */
class TaskUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task  $task,
        public User  $user,
        public array $changedFields,
    ) {}
}
