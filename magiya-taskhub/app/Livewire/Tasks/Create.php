<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskTag;
use App\Http\Requests\StoreTaskRequest;
use App\Models\TaskTemplate;
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
    public array $selectedTags = [];
    public string $templateId = '';

    /**
     * Validation rules — delegated to StoreTaskRequest.
     */
    protected function rules(): array
    {
        return array_merge((new StoreTaskRequest())->rules(), [
            'selectedTags'   => 'array',
            'selectedTags.*' => 'string',
        ]);
    }

    /**
     * Custom validation messages — also from StoreTaskRequest.
     */
    protected function messages(): array
    {
        return (new StoreTaskRequest())->messages();
    }

    /**
     * Apply a template to pre-fill form fields.
     */
    public function updatedTemplateId(string $value): void
    {
        if (!$value) return;

        $template = TaskTemplate::find($value);
        if (!$template) return;

        $this->description = $template->description_template ?? '';
        $this->priority = $template->default_priority->value;
    }

    /**
     * Save the task.
     */
    public function save(): void
    {
        $validated = $this->validate();

        $validated['assigned_to'] = $validated['assigned_to'] ?: null;
        $validated['due_date']    = $validated['due_date'] ?: null;
        $validated['description'] = $validated['description'] ?? null;
        $validated['tags']        = $this->selectedTags;

        // Remove fields not in the Task fillable
        unset($validated['selectedTags']);

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
            'tags'       => TaskTag::cases(),
            'templates'  => TaskTemplate::orderBy('name')->get(),
        ]);
    }
}
