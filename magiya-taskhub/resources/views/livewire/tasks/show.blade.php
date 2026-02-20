{{--
    Premium Task Detail View — Glassmorphism, full task info, comments, activity, attachments.
--}}
<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('tasks.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/[0.05] transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Task Detail</h2>
                    <p class="mt-0.5 text-sm text-gray-400">View and interact with this task</p>
                </div>
            </div>

            @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}"
                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 rounded-xl text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-300 hover:-translate-y-0.5 group">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Task
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $priorityBadge = [
            'gray'   => 'bg-gray-500/10 text-gray-400 ring-gray-500/20',
            'yellow' => 'bg-yellow-500/10 text-yellow-400 ring-yellow-500/20',
            'orange' => 'bg-orange-500/10 text-orange-400 ring-orange-500/20',
            'red'    => 'bg-red-500/10 text-red-400 ring-red-500/20',
        ];
        $statusBadge = [
            'gray'  => 'bg-gray-500/10 text-gray-400 ring-gray-500/20',
            'blue'  => 'bg-blue-500/10 text-blue-400 ring-blue-500/20',
            'green' => 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
            'red'   => 'bg-red-500/10 text-red-400 ring-red-500/20',
        ];
        $tagBadge = [
            'red'    => 'bg-red-500/10 text-red-400 ring-red-500/20',
            'orange' => 'bg-orange-500/10 text-orange-400 ring-orange-500/20',
            'purple' => 'bg-purple-500/10 text-purple-400 ring-purple-500/20',
            'yellow' => 'bg-yellow-500/10 text-yellow-400 ring-yellow-500/20',
        ];
    @endphp

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- ═══ LEFT COLUMN — Main task info ═══ --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Task Header Card --}}
            <div class="glass-card rounded-2xl overflow-hidden hover:transform-none">
                {{-- Priority accent bar --}}
                @php
                    $priorityAccent = ['gray' => 'from-gray-500 to-gray-400', 'yellow' => 'from-yellow-500 to-amber-400', 'orange' => 'from-orange-500 to-amber-500', 'red' => 'from-red-500 to-rose-400'];
                @endphp
                <div class="h-1 bg-gradient-to-r {{ $priorityAccent[$task->priority->color()] ?? 'from-gray-500 to-gray-400' }}"></div>

                <div class="p-6">
                    {{-- Badges row --}}
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold ring-1 ring-inset {{ $priorityBadge[$task->priority->color()] ?? 'bg-gray-500/10 text-gray-400 ring-gray-500/20' }}">
                            {{ $task->priority->label() }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold ring-1 ring-inset {{ $statusBadge[$task->status->color()] ?? 'bg-gray-500/10 text-gray-400 ring-gray-500/20' }}">
                            {{ $task->status->label() }}
                        </span>
                        @if ($task->isOverdue())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase ring-1 ring-inset bg-red-500/10 text-red-400 ring-red-500/20 animate-pulse">
                                Overdue
                            </span>
                        @endif
                        @if ($task->isStuck())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase ring-1 ring-inset bg-orange-500/10 text-orange-400 ring-orange-500/20">
                                Stuck
                            </span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h1 class="text-xl font-bold text-white mb-3">{{ $task->title }}</h1>

                    {{-- Description --}}
                    @if ($task->description)
                        <p class="text-sm text-gray-300 leading-relaxed whitespace-pre-line">{{ $task->description }}</p>
                    @else
                        <p class="text-sm text-gray-600 italic">No description provided</p>
                    @endif

                    {{-- Tags --}}
                    @if (!empty($task->tags))
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach ($task->tags as $tagValue)
                                @php
                                    $tag = \App\Enums\TaskTag::tryFrom($tagValue);
                                @endphp
                                @if ($tag)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold ring-1 ring-inset {{ $tagBadge[$tag->color()] ?? 'bg-gray-500/10 text-gray-400 ring-gray-500/20' }}">
                                        {{ $tag->label() }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Block Reason --}}
                    @if ($task->status === \App\Enums\TaskStatus::BLOCKED && $task->block_reason)
                        <div class="mt-4 p-3 bg-red-500/5 border border-red-500/10 rounded-xl">
                            <div class="flex items-center mb-1">
                                <svg class="w-4 h-4 text-red-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.068 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <span class="text-xs font-semibold text-red-400 uppercase tracking-wider">Block Reason</span>
                            </div>
                            <p class="text-sm text-red-300/80">{{ $task->block_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ═══ Quick Status Transitions ═══ --}}
            @can('update', $task)
                <div class="glass-card rounded-2xl p-5 hover:transform-none">
                    <h3 class="text-sm font-semibold text-gray-300 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Quick Actions
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($statuses as $s)
                            @if ($s->value !== $task->status->value)
                                <button wire:click="changeStatus('{{ $s->value }}')"
                                    class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200
                                        {{ $s === \App\Enums\TaskStatus::DONE ? 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 ring-1 ring-inset ring-emerald-500/20' :
                                           ($s === \App\Enums\TaskStatus::BLOCKED ? 'bg-red-500/10 text-red-400 hover:bg-red-500/20 ring-1 ring-inset ring-red-500/20' :
                                           ($s === \App\Enums\TaskStatus::IN_PROGRESS ? 'bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 ring-1 ring-inset ring-blue-500/20' :
                                           'bg-gray-500/10 text-gray-400 hover:bg-gray-500/20 ring-1 ring-inset ring-gray-500/20')) }}">
                                    @if ($s === \App\Enums\TaskStatus::IN_PROGRESS)
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif ($s === \App\Enums\TaskStatus::DONE)
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif ($s === \App\Enums\TaskStatus::BLOCKED)
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                    Move to {{ $s->label() }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endcan

            {{-- ═══ Comments Section ═══ --}}
            <div class="glass-card rounded-2xl overflow-hidden hover:transform-none">
                <div class="p-5 border-b border-white/[0.06]">
                    <h3 class="text-sm font-semibold text-gray-300 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Discussion
                        <span class="ml-2 px-2 py-0.5 text-[10px] bg-cyan-500/10 text-cyan-400 rounded-full font-bold">{{ $comments->count() }}</span>
                    </h3>
                </div>

                <div class="p-5 space-y-5">
                    @forelse ($comments as $comment)
                        <div class="group/comment" id="comment-{{ $comment->id }}">
                            <div class="flex space-x-3">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white text-[10px] font-bold shadow-md shadow-cyan-500/20">
                                    {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-semibold text-white">{{ $comment->user->name }}</span>
                                            <span class="text-[11px] text-gray-500" style="font-family: 'JetBrains Mono', monospace;">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1 opacity-0 group-hover/comment:opacity-100 transition-opacity">
                                            <button wire:click="startReply({{ $comment->id }})" class="p-1 rounded text-gray-500 hover:text-cyan-400 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            </button>
                                            @if ($comment->user_id === auth()->id() || auth()->user()->isAdmin())
                                                <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="Delete this comment?" class="p-1 rounded text-gray-500 hover:text-red-400 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-300 leading-relaxed whitespace-pre-line">{{ $comment->body }}</p>

                                    {{-- Replies --}}
                                    @if ($comment->replies->count())
                                        <div class="mt-3 ml-2 pl-3 border-l-2 border-white/[0.06] space-y-3">
                                            @foreach ($comment->replies as $reply)
                                                <div class="group/reply flex space-x-3">
                                                    <div class="flex-shrink-0 w-6 h-6 rounded-md bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-[8px] font-bold">
                                                        {{ strtoupper(substr($reply->user->name, 0, 2)) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-xs font-semibold text-white">{{ $reply->user->name }}</span>
                                                            <span class="text-[10px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                            @if ($reply->user_id === auth()->id() || auth()->user()->isAdmin())
                                                                <button wire:click="deleteComment({{ $reply->id }})" wire:confirm="Delete this reply?"
                                                                    class="opacity-0 group-hover/reply:opacity-100 p-0.5 rounded text-gray-500 hover:text-red-400 transition">
                                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <p class="mt-0.5 text-xs text-gray-400 leading-relaxed">{{ $reply->body }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Reply form (inline) --}}
                                    @if ($replyingTo === $comment->id)
                                        <div class="mt-3 ml-2 pl-3 border-l-2 border-cyan-500/30">
                                            <form wire:submit="addComment" class="flex space-x-2">
                                                <input wire:model="commentBody" type="text" placeholder="Write a reply..."
                                                    class="flex-1 px-3 py-1.5 bg-white/[0.04] border border-white/[0.06] rounded-lg text-xs text-gray-200 placeholder-gray-500 focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20">
                                                <button type="submit" class="px-3 py-1.5 bg-cyan-500/20 text-cyan-400 rounded-lg text-xs font-semibold hover:bg-cyan-500/30 transition">Reply</button>
                                                <button type="button" wire:click="cancelReply" class="px-2 py-1.5 text-gray-500 hover:text-white text-xs transition">Cancel</button>
                                            </form>
                                            @error('commentBody') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-10 h-10 text-gray-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="text-sm text-gray-500">No comments yet. Start the discussion!</p>
                        </div>
                    @endforelse

                    {{-- New comment form --}}
                    @if (!$replyingTo)
                        <form wire:submit="addComment" class="pt-4 border-t border-white/[0.06]">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="flex-1">
                                    <textarea wire:model="commentBody" rows="2" placeholder="Add a comment..."
                                        class="w-full px-3 py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 placeholder-gray-500 focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 transition resize-none"></textarea>
                                    @error('commentBody') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                                    <div class="flex justify-end mt-2">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 rounded-xl text-xs font-semibold text-white shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/30 transition-all duration-300"
                                            wire:loading.class="opacity-75 cursor-wait" wire:target="addComment">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                            Post Comment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══ RIGHT COLUMN — Sidebar info ═══ --}}
        <div class="space-y-6">

            {{-- Task Metadata --}}
            <div class="glass-card rounded-2xl p-5 hover:transform-none">
                <h3 class="text-sm font-semibold text-gray-300 mb-4">Details</h3>
                <dl class="space-y-4">
                    {{-- Assignee --}}
                    <div>
                        <dt class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Assigned To</dt>
                        <dd class="flex items-center space-x-2">
                            @if ($task->assignee)
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-bold shadow-md shadow-indigo-500/20">
                                    {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                </div>
                                <span class="text-sm text-white font-medium">{{ $task->assignee->name }}</span>
                            @else
                                <span class="text-sm text-gray-500 italic">Unassigned</span>
                            @endif
                        </dd>
                    </div>

                    {{-- Creator --}}
                    <div>
                        <dt class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Created By</dt>
                        <dd class="flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-[10px] font-bold">
                                {{ strtoupper(substr($task->creator->name, 0, 2)) }}
                            </div>
                            <span class="text-sm text-white font-medium">{{ $task->creator->name }}</span>
                        </dd>
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <dt class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Due Date</dt>
                        <dd class="text-sm {{ $task->isOverdue() ? 'text-red-400 font-semibold' : 'text-gray-300' }}" style="font-family: 'JetBrains Mono', monospace;">
                            @if ($task->due_date)
                                {{ $task->due_date->format('M d, Y') }}
                                @if ($task->isOverdue())
                                    <span class="text-red-400 text-[10px] ml-1">({{ $task->due_date->diffForHumans() }})</span>
                                @endif
                            @else
                                <span class="text-gray-500 italic" style="font-family: inherit;">No due date</span>
                            @endif
                        </dd>
                    </div>

                    {{-- Created --}}
                    <div>
                        <dt class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Created</dt>
                        <dd class="text-sm text-gray-300" style="font-family: 'JetBrains Mono', monospace;">{{ $task->created_at->format('M d, Y H:i') }}</dd>
                    </div>

                    {{-- Timing info --}}
                    @if ($task->started_at)
                        <div>
                            <dt class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Started At</dt>
                            <dd class="text-sm text-blue-300" style="font-family: 'JetBrains Mono', monospace;">{{ $task->started_at->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif

                    @if ($task->completed_at)
                        <div>
                            <dt class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Completed At</dt>
                            <dd class="text-sm text-emerald-300" style="font-family: 'JetBrains Mono', monospace;">{{ $task->completed_at->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif

                    @if ($task->cycleTimeInHours())
                        <div>
                            <dt class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Cycle Time</dt>
                            <dd class="text-sm text-emerald-400 font-semibold" style="font-family: 'JetBrains Mono', monospace;">{{ $task->cycleTimeInHours() }}h</dd>
                        </div>
                    @endif

                    {{-- Days in current status --}}
                    <div>
                        <dt class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">In Current Status</dt>
                        <dd class="text-sm text-gray-300" style="font-family: 'JetBrains Mono', monospace;">{{ $task->daysInCurrentStatus() }} day(s)</dd>
                    </div>
                </dl>
            </div>

            {{-- ═══ Attachments ═══ --}}
            <div class="glass-card rounded-2xl overflow-hidden hover:transform-none">
                <div class="p-5 border-b border-white/[0.06]">
                    <h3 class="text-sm font-semibold text-gray-300 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        Attachments
                        <span class="ml-2 px-2 py-0.5 text-[10px] bg-amber-500/10 text-amber-400 rounded-full font-bold">{{ $task->attachments->count() }}</span>
                    </h3>
                </div>

                <div class="p-5 space-y-3">
                    @forelse ($task->attachments as $attachment)
                        <div class="group/file flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] hover:bg-white/[0.05] transition">
                            <div class="flex items-center space-x-3 min-w-0">
                                @if ($attachment->isImage())
                                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-xs text-white font-medium truncate">{{ $attachment->original_name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $attachment->humanSize() }} · {{ $attachment->uploader->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1 opacity-0 group-hover/file:opacity-100 transition">
                                <a href="{{ $attachment->downloadUrl() }}" target="_blank" class="p-1.5 rounded-lg text-gray-500 hover:text-amber-400 hover:bg-amber-500/10 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                                @if ($attachment->user_id === auth()->id() || auth()->user()->isAdmin())
                                    <button wire:click="deleteAttachment({{ $attachment->id }})" wire:confirm="Delete this file?" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-2">No attachments</p>
                    @endforelse

                    {{-- Upload form --}}
                    @can('update', $task)
                        <form wire:submit="uploadFiles" class="pt-3 border-t border-white/[0.06]">
                            <input wire:model="uploadedFiles" type="file" multiple
                                class="w-full text-xs text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-500/10 file:text-amber-400 hover:file:bg-amber-500/20 file:cursor-pointer file:transition cursor-pointer">
                            @error('uploadedFiles.*') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            <div wire:loading wire:target="uploadedFiles" class="mt-2 text-xs text-amber-400">Uploading...</div>
                            @if (count($uploadedFiles ?? []) > 0)
                                <button type="submit" class="mt-2 w-full inline-flex items-center justify-center px-3 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-xs font-semibold hover:bg-amber-500/20 ring-1 ring-inset ring-amber-500/20 transition">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Upload {{ count($uploadedFiles) }} file(s)
                                </button>
                            @endif
                        </form>
                    @endcan
                </div>
            </div>

            {{-- ═══ Activity Timeline ═══ --}}
            <div class="glass-card rounded-2xl overflow-hidden hover:transform-none">
                <div class="p-5 border-b border-white/[0.06]">
                    <h3 class="text-sm font-semibold text-gray-300 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Activity
                    </h3>
                </div>

                <div class="p-5">
                    @forelse ($activities as $activity)
                        <div class="flex space-x-3 {{ !$loop->last ? 'pb-4 mb-4 border-b border-white/[0.03]' : '' }}">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center
                                {{ $activity->action === 'created' ? 'bg-emerald-500/20 text-emerald-400' :
                                   ($activity->action === 'deleted' ? 'bg-red-500/20 text-red-400' :
                                   ($activity->action === 'assigned' ? 'bg-blue-500/20 text-blue-400' :
                                   'bg-purple-500/20 text-purple-400')) }}">
                                @if ($activity->action === 'created')
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                @elseif ($activity->action === 'deleted')
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                @elseif ($activity->action === 'assigned')
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @else
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-300">{{ $activity->description }}</p>
                                <p class="text-[10px] text-gray-500 mt-0.5" style="font-family: 'JetBrains Mono', monospace;">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No activity recorded yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Block Reason Modal ═══ --}}
    @if ($showBlockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="$el.querySelector('textarea').focus()">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showBlockModal', false)"></div>
            <div class="relative w-full max-w-md glass-card rounded-2xl p-6 shadow-2xl">
                <h3 class="text-lg font-bold text-white mb-1">Block Task</h3>
                <p class="text-sm text-gray-400 mb-4">Please provide a reason for blocking this task.</p>

                <form wire:submit="confirmBlock">
                    <textarea wire:model="blockReason" rows="3" placeholder="Why is this task blocked?"
                        class="w-full px-3 py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 placeholder-gray-500 focus:border-red-500/30 focus:ring-1 focus:ring-red-500/20 transition resize-none"></textarea>
                    @error('blockReason') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" wire:click="$set('showBlockModal', false)"
                            class="px-4 py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-300 hover:bg-white/[0.08] transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 rounded-xl text-sm font-semibold text-white hover:from-red-500 hover:to-red-400 shadow-lg shadow-red-500/20 transition-all">Block Task</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
