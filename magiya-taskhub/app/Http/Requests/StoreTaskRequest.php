<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request = a dedicated class for validation rules + authorization.
 *
 * MERN comparison:
 *   - Like a Zod / Yup schema, but also handles HTTP authorization.
 *   - In Express you'd do `req.body` validation in middleware; Laravel
 *     does the same thing with this class — it runs before your controller.
 *
 * Why enterprise projects use this:
 *   1. Reusable — the same rules can be shared by Livewire, API, and web controllers.
 *   2. Single Responsibility — validation logic lives here, not in the controller.
 *   3. Custom messages — client-friendly error messages in one place.
 *   4. Testable — you can unit-test the rules independently.
 */
class StoreTaskRequest extends FormRequest
{
    /**
     * Who is allowed to make this request?
     *
     * We return true here because authorization is already handled
     * by our TaskPolicy (via the Service layer). Doing it twice would
     * be redundant. In a pure controller setup you'd check here.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for creating a task.
     *
     * Rule::in() with enum cases is the enterprise way — if you add
     * a new enum case, validation automatically picks it up.
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority'    => ['required', Rule::in(array_column(TaskPriority::cases(), 'value'))],
            'status'      => ['required', Rule::in(array_column(TaskStatus::cases(), 'value'))],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'due_date'    => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Custom human-readable error messages.
     *
     * These replace Laravel's default "The title field is required." with
     * messages that make sense to your team.
     */
    public function messages(): array
    {
        return [
            'title.required'        => 'Every task needs a title.',
            'title.min'             => 'Title must be at least 3 characters.',
            'title.max'             => 'Title cannot exceed 255 characters.',
            'description.max'       => 'Description is too long (max 1000 chars).',
            'priority.required'     => 'Please select a priority level.',
            'priority.in'           => 'Invalid priority. Choose from: low, medium, high, critical.',
            'status.required'       => 'Please select a status.',
            'status.in'             => 'Invalid status. Choose from: todo, in_progress, done, blocked.',
            'assigned_to.exists'    => 'The selected user does not exist.',
            'due_date.date'         => 'Please enter a valid date.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
        ];
    }

    /**
     * Attribute labels for cleaner error messages.
     *
     * Laravel uses these in auto-generated messages, e.g.
     * "The assigned to field..." becomes "The assignee field..."
     */
    public function attributes(): array
    {
        return [
            'assigned_to' => 'assignee',
            'due_date'    => 'due date',
        ];
    }

    /**
     * Prepare data before validation runs.
     *
     * This is like Express middleware that sanitizes req.body before
     * it hits the validator. Converts empty strings to null so the
     * 'nullable' rule works correctly.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'assigned_to' => $this->assigned_to ?: null,
            'due_date'    => $this->due_date ?: null,
            'description' => $this->description ?: null,
        ]);
    }
}
