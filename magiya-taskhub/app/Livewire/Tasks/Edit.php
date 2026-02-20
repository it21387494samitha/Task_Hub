<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskTag;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public Task $task;

    // ─── Form fields ─────────────────────────────────────
    public string $title = '';
    public string $description = '';
    public string $priority = '';
    public string $status = '';
    public string $assigned_to = '';
    public string $due_date = '';
    public array $selectedTags = [];
    public string $block_reason = '';

    /**
     * Mount = like useEffect([], ...) in React — runs once when component loads.
     */
    public function mount(Task $task): void
    {
        $this->task = $task;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->priority = $task->priority->value;
        $this->status = $task->status->value;
        $this->assigned_to = (string) ($task->assigned_to ?? '');
        $this->due_date = $task->due_date?->format('Y-m-d') ?? '';
        $this->selectedTags = $task->tags ?? [];
        $this->block_reason = $task->block_reason ?? '';
    }

    /**
     * Validation rules — delegated to UpdateTaskRequest.
     */
    protected function rules(): array
    {
        return array_merge((new UpdateTaskRequest())->rules(), [
            'selectedTags'   => 'array',
            'selectedTags.*' => 'string',
            'block_reason'   => 'nullable|string|max:500',
        ]);
    }

    protected function messages(): array
    {
        return (new UpdateTaskRequest())->messages();
    }

    public function save(): void
    {
        $validated = $this->validate();

        $validated['assigned_to'] = $validated['assigned_to'] ?: null;
        $validated['due_date']    = $validated['due_date'] ?: null;
        $validated['description'] = $validated['description'] ?? null;
        $validated['tags']        = $this->selectedTags;
        $validated['block_reason'] = $this->block_reason ?: null;

        unset($validated['selectedTags']);

        // Developers can only update title, description, and status.
        if (Auth::user()->isDeveloper()) {
            unset($validated['priority'], $validated['assigned_to'], $validated['due_date'], $validated['tags'], $validated['block_reason']);
        }

        app(TaskService::class)->updateTask(Auth::user(), $this->task, $validated);

        session()->flash('success', "Task '{$validated['title']}' updated successfully.");

        $this->redirect(route('tasks.index'));
    }

    public function render()
    {
        return view('livewire.tasks.edit', [
            'priorities' => TaskPriority::cases(),
            'statuses'   => TaskStatus::cases(),
            'users'      => User::orderBy('name')->get(['id', 'name']),
            'tags'       => TaskTag::cases(),
        ]);
    }
}
