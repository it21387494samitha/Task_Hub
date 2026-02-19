<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a new task is created.
 *
 * MERN comparison:
 *   In Node.js you'd use EventEmitter:  eventBus.emit('task.created', task)
 *   In Laravel, events are classes — this gives you type safety, IDE autocomplete,
 *   and the ability to queue listeners for heavy work (email, Slack, etc.).
 *
 * Architecture:
 *   Service fires event → Laravel dispatches to all registered Listeners
 *   The service doesn't know WHO is listening — that's the decoupled magic.
 */
class TaskCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task $task,
        public User $creator,
    ) {}
}
