<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update request — similar to StoreTaskRequest but with key differences:
 *
 * 1. due_date does NOT require 'after_or_equal:today' — you might
 *    be editing a task that already has a past due date.
 * 2. Rules can reference $this->route('task') to apply conditional
 *    logic based on the task being edited (e.g. unique title per user).
 *
 * Enterprise pattern: Store vs Update requests share similar rules but
 * differ in edge cases. Keeping them separate avoids messy if/else logic.
 */
class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by TaskPolicy via Service layer
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority'    => ['required', Rule::in(array_column(TaskPriority::cases(), 'value'))],
            'status'      => ['required', Rule::in(array_column(TaskStatus::cases(), 'value'))],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'due_date'    => ['nullable', 'date'], // no after_or_equal — existing tasks may have past dates
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'     => 'Every task needs a title.',
            'title.min'          => 'Title must be at least 3 characters.',
            'title.max'          => 'Title cannot exceed 255 characters.',
            'description.max'    => 'Description is too long (max 1000 chars).',
            'priority.required'  => 'Please select a priority level.',
            'priority.in'        => 'Invalid priority. Choose from: low, medium, high, critical.',
            'status.required'    => 'Please select a status.',
            'status.in'          => 'Invalid status. Choose from: todo, in_progress, done, blocked.',
            'assigned_to.exists' => 'The selected user does not exist.',
            'due_date.date'      => 'Please enter a valid date.',
        ];
    }

    public function attributes(): array
    {
        return [
            'assigned_to' => 'assignee',
            'due_date'    => 'due date',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'assigned_to' => $this->assigned_to ?: null,
            'due_date'    => $this->due_date ?: null,
            'description' => $this->description ?: null,
        ]);
    }
}
