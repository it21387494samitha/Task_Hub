{{--
    Premium Kanban Board — Drag-and-drop columns, glassmorphism, smooth transitions.
    Uses Alpine.js for drag-and-drop and Livewire for persistence.
--}}
<div x-data="{
    dragging: null,
    dragOverColumn: null,

    startDrag(e, taskId) {
        this.dragging = taskId;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', taskId);
        e.target.classList.add('opacity-50', 'scale-95');
    },

    endDrag(e) {
        this.dragging = null;
        this.dragOverColumn = null;
        e.target.classList.remove('opacity-50', 'scale-95');
    },

    dragOver(e, status) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        this.dragOverColumn = status;
    },

    dragLeave(e, status) {
        if (this.dragOverColumn === status) {
            this.dragOverColumn = null;
        }
    },

    drop(e, status) {
        e.preventDefault();
        const taskId = parseInt(e.dataTransfer.getData('text/plain'));
        this.dragOverColumn = null;
        this.dragging = null;
        if (taskId) {
            $wire.moveTask(taskId, status);
        }
    }
}">
    {{-- Page Header --}}
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Kanban Board</h2>
                <p class="mt-1 text-sm text-gray-400">Drag tasks between columns to update status</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- View Toggle --}}
                <a href="{{ route('tasks.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/[0.08] transition-all"
                   title="Grid View">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Grid
                </a>
                @can('create', App\Models\Task::class)
                    <a href="{{ route('tasks.create') }}"
                       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 rounded-xl text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-300 hover:-translate-y-0.5 group">
                        <svg class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90 duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        New Task
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    {{-- Filters Bar --}}
    <div class="mb-6 glass-card rounded-2xl p-4 hover:transform-none">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="flex-1">
                <div class="relative group">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 group-focus-within:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tasks..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 placeholder-gray-500 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                </div>
            </div>

            {{-- Priority Filter --}}
            <div class="sm:w-44">
                <select wire:model.live="priorityFilter"
                    class="w-full py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                    <option value="">All Priorities</option>
                    @foreach ($priorities as $priority)
                        <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Assignee Filter --}}
            <div class="sm:w-48">
                <select wire:model.live="assigneeFilter"
                    class="w-full py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                    <option value="">All Assignees</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @php
        $columnColors = [
            'todo'        => ['border' => 'border-gray-500/30', 'bg' => 'bg-gray-500/5', 'dot' => 'bg-gray-400', 'drop' => 'ring-gray-400/40 bg-gray-500/10'],
            'in_progress' => ['border' => 'border-blue-500/30', 'bg' => 'bg-blue-500/5', 'dot' => 'bg-blue-400', 'drop' => 'ring-blue-400/40 bg-blue-500/10'],
            'done'        => ['border' => 'border-emerald-500/30', 'bg' => 'bg-emerald-500/5', 'dot' => 'bg-emerald-400', 'drop' => 'ring-emerald-400/40 bg-emerald-500/10'],
            'blocked'     => ['border' => 'border-red-500/30', 'bg' => 'bg-red-500/5', 'dot' => 'bg-red-400', 'drop' => 'ring-red-400/40 bg-red-500/10'],
        ];
        $priorityAccent = [
            'gray'   => 'from-gray-500 to-gray-400',
            'yellow' => 'from-yellow-500 to-amber-400',
            'orange' => 'from-orange-500 to-amber-500',
            'red'    => 'from-red-500 to-rose-400',
        ];
        $priorityBadge = [
            'gray'   => 'bg-gray-500/10 text-gray-400 ring-gray-500/20',
            'yellow' => 'bg-yellow-500/10 text-yellow-400 ring-yellow-500/20',
            'orange' => 'bg-orange-500/10 text-orange-400 ring-orange-500/20',
            'red'    => 'bg-red-500/10 text-red-400 ring-red-500/20',
        ];
    @endphp

    {{-- Kanban Columns --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ($statuses as $status)
            @php
                $col = $columnColors[$status->value] ?? $columnColors['todo'];
                $columnTasks = $columns[$status->value] ?? collect();
            @endphp
            <div class="flex flex-col rounded-2xl border {{ $col['border'] }} {{ $col['bg'] }} backdrop-blur-sm min-h-[400px] transition-all duration-200"
                 :class="dragOverColumn === '{{ $status->value }}' && '{{ $col['drop'] }} ring-2 scale-[1.01]'"
                 @dragover.prevent="dragOver($event, '{{ $status->value }}')"
                 @dragleave="dragLeave($event, '{{ $status->value }}')"
                 @drop="drop($event, '{{ $status->value }}')">

                {{-- Column Header --}}
                <div class="flex items-center justify-between px-4 py-3 border-b {{ $col['border'] }}">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-2.5 h-2.5 rounded-full {{ $col['dot'] }}"></div>
                        <h3 class="text-sm font-semibold text-white">{{ $status->label() }}</h3>
                    </div>
                    <span class="inline-flex items-center justify-center min-w-[1.5rem] px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-white/10 text-gray-300"
                          style="font-family: 'JetBrains Mono', monospace;">
                        {{ $columnTasks->count() }}
                    </span>
                </div>

                {{-- Column Body (droppable) --}}
                <div class="flex-1 p-3 space-y-3 overflow-y-auto">
                    @forelse ($columnTasks as $task)
                        <div class="group glass-card rounded-xl overflow-hidden cursor-grab active:cursor-grabbing transition-all duration-200 hover:-translate-y-0.5"
                             draggable="true"
                             @dragstart="startDrag($event, {{ $task->id }})"
                             @dragend="endDrag($event)">
                            {{-- Priority accent bar --}}
                            <div class="h-0.5 bg-gradient-to-r {{ $priorityAccent[$task->priority->color()] ?? 'from-gray-500 to-gray-400' }}"></div>

                            <div class="p-3">
                                {{-- Priority badge --}}
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold ring-1 ring-inset {{ $priorityBadge[$task->priority->color()] ?? 'bg-gray-500/10 text-gray-400 ring-gray-500/20' }}">
                                        {{ $task->priority->label() }}
                                    </span>
                                    @if ($task->isOverdue())
                                        <span class="text-[9px] font-bold uppercase text-red-400 animate-pulse">Overdue</span>
                                    @endif
                                </div>

                                {{-- Title (clickable) --}}
                                <a href="{{ route('tasks.show', $task) }}" class="block mb-2" @click.stop>
                                    <h4 class="text-sm font-medium text-white line-clamp-2 group-hover:text-indigo-400 transition-colors">
                                        {{ $task->title }}
                                    </h4>
                                </a>

                                {{-- Footer: Assignee + Due date --}}
                                <div class="flex items-center justify-between mt-2">
                                    @if ($task->assignee)
                                        <div class="flex items-center space-x-1.5">
                                            <div class="w-5 h-5 rounded-md bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[8px] font-bold">
                                                {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                                            </div>
                                            <span class="text-[11px] text-gray-500 truncate max-w-[80px]">{{ $task->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-gray-600">Unassigned</span>
                                    @endif

                                    @if ($task->due_date)
                                        <span class="text-[10px] {{ $task->isOverdue() ? 'text-red-400' : 'text-gray-500' }}" style="font-family: 'JetBrains Mono', monospace;">
                                            {{ $task->due_date->format('M d') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-10 h-10 rounded-xl bg-white/[0.04] flex items-center justify-center mb-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="text-[11px] text-gray-600">No tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Block Reason Modal --}}
    @if ($showBlockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="cancelBlock">
            <div class="glass-card rounded-2xl w-full max-w-md p-6 hover:transform-none">
                <h3 class="text-lg font-semibold text-white mb-1">Block Task</h3>
                <p class="text-sm text-gray-400 mb-4">Please provide a reason why this task is blocked.</p>

                <textarea wire:model="blockReason" rows="3" autofocus
                    class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 placeholder-gray-500 focus:border-red-500/30 focus:ring-1 focus:ring-red-500/20 transition text-sm"
                    placeholder="What's blocking this task?"></textarea>
                @error('blockReason') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror

                <div class="flex justify-end space-x-3 mt-4">
                    <button wire:click="cancelBlock"
                        class="px-4 py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm font-medium text-gray-300 hover:bg-white/[0.08] transition">
                        Cancel
                    </button>
                    <button wire:click="confirmBlock"
                        class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 rounded-xl text-sm font-semibold text-white shadow-lg shadow-red-500/25 hover:shadow-red-500/40 transition-all">
                        Block Task
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
