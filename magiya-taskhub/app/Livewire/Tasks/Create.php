<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    // ─── Form fields (like useState in React) ────────────
    public string $title = '';
    public string $description = '';
    public string $priority = 'medium';
    public string $status = 'todo';
    public string $assigned_to = '';
    public string $due_date = '';

    /**
     * Validation rules — delegated to StoreTaskRequest.
     *
     * This is the enterprise pattern: a single source of truth for rules.
     * If you add an API controller later, it reuses the same Request class.
     */
    protected function rules(): array
    {
        return (new StoreTaskRequest())->rules();
    }

    /**
     * Custom validation messages — also from StoreTaskRequest.
     */
    protected function messages(): array
    {
        return (new StoreTaskRequest())->messages();
    }

    /**
     * Save the task (like handleSubmit in React).
     */
    public function save(): void
    {
        $validated = $this->validate();

        // Sanitize nullable fields (mirrors prepareForValidation in the Request)
        $validated['assigned_to'] = $validated['assigned_to'] ?: null;
        $validated['due_date']    = $validated['due_date'] ?: null;
        $validated['description'] = $validated['description'] ?? null;

        app(TaskService::class)->createTask(Auth::user(), $validated);

        session()->flash('success', "Task '{$validated['title']}' created successfully.");

        $this->redirect(route('tasks.index'));
    }

    public function render()
    {
        return view('livewire.tasks.create', [
            'priorities' => TaskPriority::cases(),
            'statuses'   => TaskStatus::cases(),
            'users'      => User::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
