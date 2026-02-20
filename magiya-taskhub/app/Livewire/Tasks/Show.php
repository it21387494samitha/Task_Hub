<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskStatus;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Task;
use App\Services\ActivityService;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public Task $task;

    // ─── Comment form ────────────────────────────────────
    public string $commentBody = '';
    public ?int $replyingTo = null;

    // ─── Quick status change ─────────────────────────────
    public string $newStatus = '';

    // ─── Block reason modal ──────────────────────────────
    public bool $showBlockModal = false;
    public string $blockReason = '';

    // ─── File upload ─────────────────────────────────────
    public $uploadedFiles = [];

    public function mount(Task $task): void
    {
        $this->authorize('view', $task);
        $this->task = $task->load(['assignee', 'creator', 'comments.user', 'comments.replies.user', 'attachments.uploader']);
        $this->newStatus = $task->status->value;
    }

    // ─── Comments ────────────────────────────────────────

    public function addComment(): void
    {
        $this->validate(['commentBody' => 'required|string|min:1|max:2000']);

        Comment::create([
            'task_id'   => $this->task->id,
            'user_id'   => Auth::id(),
            'body'      => $this->commentBody,
            'parent_id' => $this->replyingTo,
        ]);

        $this->commentBody = '';
        $this->replyingTo = null;
        $this->task->load(['comments.user', 'comments.replies.user']);
        $this->dispatch('toast', message: 'Comment added.', type: 'success');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        // Only the commenter or admin can delete
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return;
        }

        $comment->delete();
        $this->task->load(['comments.user', 'comments.replies.user']);
        $this->dispatch('toast', message: 'Comment deleted.', type: 'success');
    }

    public function startReply(int $commentId): void
    {
        $this->replyingTo = $commentId;
    }

    public function cancelReply(): void
    {
        $this->replyingTo = null;
    }

    // ─── Quick Status Transitions ────────────────────────

    public function changeStatus(string $status): void
    {
        if ($status === TaskStatus::BLOCKED->value) {
            $this->showBlockModal = true;
            return;
        }

        $this->performStatusChange($status);
    }

    public function confirmBlock(): void
    {
        $this->validate(['blockReason' => 'required|string|min:3|max:500']);

        $data = [
            'status'       => TaskStatus::BLOCKED->value,
            'block_reason' => $this->blockReason,
        ];

        app(TaskService::class)->updateTask(Auth::user(), $this->task, $data);

        $this->task->refresh();
        $this->newStatus = $this->task->status->value;
        $this->showBlockModal = false;
        $this->blockReason = '';
        $this->dispatch('toast', message: 'Task blocked.', type: 'warning');
    }

    private function performStatusChange(string $status): void
    {
        app(TaskService::class)->updateTask(Auth::user(), $this->task, ['status' => $status]);

        $this->task->refresh();
        $this->newStatus = $this->task->status->value;

        $label = TaskStatus::from($status)->label();
        $this->dispatch('toast', message: "Status changed to {$label}.", type: 'success');
    }

    // ─── File Uploads ────────────────────────────────────

    public function uploadFiles(): void
    {
        $this->validate([
            'uploadedFiles'   => 'required|array|min:1',
            'uploadedFiles.*' => 'file|max:10240', // 10MB max
        ]);

        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('task-attachments/' . $this->task->id, 'public');

            Attachment::create([
                'task_id'       => $this->task->id,
                'user_id'       => Auth::id(),
                'original_name' => $file->getClientOriginalName(),
                'stored_path'   => $path,
                'mime_type'     => $file->getMimeType(),
                'size_bytes'    => $file->getSize(),
            ]);
        }

        $this->uploadedFiles = [];
        $this->task->load('attachments.uploader');
        $this->dispatch('toast', message: 'Files uploaded.', type: 'success');
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $attachment = Attachment::findOrFail($attachmentId);

        if ($attachment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return;
        }

        Storage::disk('public')->delete($attachment->stored_path);
        $attachment->delete();

        $this->task->load('attachments.uploader');
        $this->dispatch('toast', message: 'Attachment deleted.', type: 'success');
    }

    // ─── Render ──────────────────────────────────────────

    public function render()
    {
        $activityService = app(ActivityService::class);
        $activities = $activityService->getForSubject($this->task);

        // Top-level comments only (replies loaded via relationship)
        $comments = $this->task->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->oldest()
            ->get();

        return view('livewire.tasks.show', [
            'activities' => $activities,
            'comments'   => $comments,
            'statuses'   => TaskStatus::cases(),
        ]);
    }
}
