<?php

use App\Enums\Role;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

// ═══════════════════════════════════════════════════════════
//  PHASE 5 — Task Detail View & Interactions Tests
// ═══════════════════════════════════════════════════════════

describe('Task Show Page', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
        $this->developer = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        $this->task = Task::factory()->create([
            'assigned_to' => $this->developer->id,
            'created_by'  => $this->admin->id,
            'status'      => TaskStatus::TODO,
            'priority'    => TaskPriority::HIGH,
        ]);
    });

    test('admin can view task detail page', function () {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->assertStatus(200)
            ->assertSee($this->task->title);
    });

    test('assigned developer can view task detail page', function () {
        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->assertStatus(200)
            ->assertSee($this->task->title);
    });

    test('show page displays task metadata', function () {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->assertSee($this->task->priority->label())
            ->assertSee($this->task->status->label());
    });
});

// ═══════════════════════════════════════════════════════════
//  Comments
// ═══════════════════════════════════════════════════════════

describe('Comments', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
        $this->developer = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        $this->task = Task::factory()->create([
            'assigned_to' => $this->developer->id,
            'created_by'  => $this->admin->id,
        ]);
    });

    test('user can add a comment to a task', function () {
        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->set('commentBody', 'This is a test comment.')
            ->call('addComment')
            ->assertDispatched('toast');

        expect(Comment::where('task_id', $this->task->id)->count())->toBe(1);
        expect(Comment::first()->body)->toBe('This is a test comment.');
    });

    test('comment requires non-empty body', function () {
        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->set('commentBody', '')
            ->call('addComment')
            ->assertHasErrors(['commentBody']);
    });

    test('user can reply to a comment', function () {
        $comment = Comment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->admin->id,
            'body'    => 'Parent comment',
        ]);

        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('startReply', $comment->id)
            ->assertSet('replyingTo', $comment->id)
            ->set('commentBody', 'Reply to parent')
            ->call('addComment');

        $reply = Comment::where('parent_id', $comment->id)->first();
        expect($reply)->not->toBeNull()
            ->and($reply->body)->toBe('Reply to parent');
    });

    test('user can cancel a reply', function () {
        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('startReply', 999)
            ->assertSet('replyingTo', 999)
            ->call('cancelReply')
            ->assertSet('replyingTo', null);
    });

    test('user can delete own comment', function () {
        $comment = Comment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->developer->id,
            'body'    => 'My comment',
        ]);

        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('deleteComment', $comment->id);

        expect(Comment::find($comment->id))->toBeNull();
    });

    test('admin can delete any comment', function () {
        $comment = Comment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->developer->id,
            'body'    => 'Dev comment',
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('deleteComment', $comment->id);

        expect(Comment::find($comment->id))->toBeNull();
    });

    test('developer cannot delete another users comment', function () {
        $otherDev = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        $comment = Comment::create([
            'task_id' => $this->task->id,
            'user_id' => $otherDev->id,
            'body'    => 'Other dev comment',
        ]);

        // This developer can view (they are assigned), but shouldn't delete others' comments
        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('deleteComment', $comment->id);

        expect(Comment::find($comment->id))->not->toBeNull();
    });

    test('comment model has correct relationships', function () {
        $comment = Comment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->developer->id,
            'body'    => 'Test',
        ]);

        expect($comment->task->id)->toBe($this->task->id)
            ->and($comment->user->id)->toBe($this->developer->id);
    });
});

// ═══════════════════════════════════════════════════════════
//  Quick Status Changes
// ═══════════════════════════════════════════════════════════

describe('Quick Status Changes', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
        $this->task = Task::factory()->create([
            'created_by' => $this->admin->id,
            'status'     => TaskStatus::TODO,
        ]);
    });

    test('admin can change task status from show page', function () {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('changeStatus', TaskStatus::IN_PROGRESS->value);

        $this->task->refresh();
        expect($this->task->status)->toBe(TaskStatus::IN_PROGRESS);
    });

    test('blocking a task opens the block modal', function () {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('changeStatus', TaskStatus::BLOCKED->value)
            ->assertSet('showBlockModal', true);
    });

    test('confirming block with reason sets task as blocked', function () {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('changeStatus', TaskStatus::BLOCKED->value)
            ->set('blockReason', 'Waiting for API access')
            ->call('confirmBlock');

        $this->task->refresh();
        expect($this->task->status)->toBe(TaskStatus::BLOCKED)
            ->and($this->task->block_reason)->toBe('Waiting for API access');
    });

    test('block reason is required', function () {
        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('changeStatus', TaskStatus::BLOCKED->value)
            ->set('blockReason', '')
            ->call('confirmBlock')
            ->assertHasErrors(['blockReason']);
    });
});

// ═══════════════════════════════════════════════════════════
//  Attachments
// ═══════════════════════════════════════════════════════════

describe('Attachments', function () {

    beforeEach(function () {
        $this->admin = User::factory()->create(['role' => Role::ADMIN, 'is_active' => true]);
        $this->developer = User::factory()->create(['role' => Role::DEVELOPER, 'is_active' => true]);
        $this->task = Task::factory()->create([
            'assigned_to' => $this->developer->id,
            'created_by'  => $this->admin->id,
        ]);
    });

    test('attachment model has correct relationships', function () {
        $attachment = Attachment::create([
            'task_id'       => $this->task->id,
            'user_id'       => $this->developer->id,
            'original_name' => 'test.pdf',
            'stored_path'   => 'task-attachments/1/test.pdf',
            'mime_type'     => 'application/pdf',
            'size_bytes'    => 1024,
        ]);

        expect($attachment->task->id)->toBe($this->task->id)
            ->and($attachment->uploader->id)->toBe($this->developer->id);
    });

    test('attachment humanSize returns correct format', function () {
        $attachment = new Attachment(['size_bytes' => 1536]);
        expect($attachment->humanSize())->toBe('1.5 KB');

        $attachment = new Attachment(['size_bytes' => 2621440]);
        expect($attachment->humanSize())->toBe('2.5 MB');

        $attachment = new Attachment(['size_bytes' => 512]);
        expect($attachment->humanSize())->toBe('512 B');
    });

    test('attachment isImage detects image mime types', function () {
        $image = new Attachment(['mime_type' => 'image/png']);
        $pdf = new Attachment(['mime_type' => 'application/pdf']);

        expect($image->isImage())->toBeTrue()
            ->and($pdf->isImage())->toBeFalse();
    });

    test('user can delete own attachment', function () {
        \Illuminate\Support\Facades\Storage::fake('public');

        $attachment = Attachment::create([
            'task_id'       => $this->task->id,
            'user_id'       => $this->developer->id,
            'original_name' => 'test.txt',
            'stored_path'   => 'task-attachments/1/test.txt',
            'mime_type'     => 'text/plain',
            'size_bytes'    => 100,
        ]);

        Livewire::actingAs($this->developer)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('deleteAttachment', $attachment->id);

        expect(Attachment::find($attachment->id))->toBeNull();
    });

    test('admin can delete any attachment', function () {
        \Illuminate\Support\Facades\Storage::fake('public');

        $attachment = Attachment::create([
            'task_id'       => $this->task->id,
            'user_id'       => $this->developer->id,
            'original_name' => 'test.txt',
            'stored_path'   => 'task-attachments/1/test.txt',
            'mime_type'     => 'text/plain',
            'size_bytes'    => 100,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Livewire\Tasks\Show::class, ['task' => $this->task])
            ->call('deleteAttachment', $attachment->id);

        expect(Attachment::find($attachment->id))->toBeNull();
    });
});
