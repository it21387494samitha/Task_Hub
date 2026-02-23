{{--
    Premium Task Index — Glass cards, hover lift, staggered animations, polished badges.
    With view toggle (Grid/Board), bulk operations, and sorting.
--}}
<div>
    {{-- Page Header --}}
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Tasks</h2>
                <p class="mt-1 text-sm text-gray-400">Manage and track all project tasks</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- View Toggle: Board --}}
                <a href="{{ route('tasks.board') }}"
                   class="inline-flex items-center px-4 py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/[0.08] transition-all"
                   title="Kanban Board">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                    Board
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

    {{-- Success Messages (toast dispatch) --}}
    @if (session()->has('success'))
        <div x-init="$dispatch('toast', { message: '{{ session('success') }}', type: 'success' })" class="hidden"></div>
    @endif
    @if (session()->has('error'))
        <div x-init="$dispatch('toast', { message: '{{ session('error') }}', type: 'error' })" class="hidden"></div>
    @endif
    @if ($successMessage)
        <div x-init="$dispatch('toast', { message: '{{ $successMessage }}', type: 'success' })" class="hidden"></div>
    @endif

    {{-- Filters Bar --}}
    <div class="mb-4 glass-card rounded-2xl p-4 hover:transform-none">
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

            {{-- Status Filter --}}
            <div class="sm:w-44">
                <select wire:model.live="statusFilter"
                    class="w-full py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
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

            {{-- Sort --}}
            <div class="sm:w-40">
                <select wire:model.live="sortBy"
                    class="w-full py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                    <option value="latest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="due_date">Due Date</option>
                    <option value="priority">Priority</option>
                    <option value="title">Title A-Z</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Bulk Operations Bar --}}
    @if (count($selected) > 0)
        <div class="mb-4 glass-card rounded-2xl p-4 hover:transform-none border border-indigo-500/20 bg-indigo-500/5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-xs font-bold rounded-full bg-indigo-500/20 text-indigo-400"
                          style="font-family: 'JetBrains Mono', monospace;">{{ count($selected) }}</span>
                    <span class="text-sm text-gray-300 font-medium">selected</span>
                </div>
                <div class="flex flex-1 items-center gap-2">
                    <select wire:model.live="bulkAction"
                        class="py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                        <option value="">Bulk Action...</option>
                        <option value="status">Change Status</option>
                        <option value="priority">Change Priority</option>
                        @can('delete', App\Models\Task::class)
                            <option value="delete">Delete Selected</option>
                        @endcan
                    </select>

                    @if ($bulkAction === 'status')
                        <select wire:model.live="bulkValue"
                            class="py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                            <option value="">Select status...</option>
                            @foreach ($statuses as $s)
                                <option value="{{ $s->value }}">{{ $s->label() }}</option>
                            @endforeach
                        </select>
                    @endif

                    @if ($bulkAction === 'priority')
                        <select wire:model.live="bulkValue"
                            class="py-2 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                            <option value="">Select priority...</option>
                            @foreach ($priorities as $p)
                                <option value="{{ $p->value }}">{{ $p->label() }}</option>
                            @endforeach
                        </select>
                    @endif

                    <button wire:click="executeBulk"
                        @if (!$bulkAction || ($bulkAction !== 'delete' && !$bulkValue)) disabled @endif
                        wire:confirm="{{ $bulkAction === 'delete' ? 'Are you sure you want to delete ' . count($selected) . ' task(s)?' : '' }}"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-xl text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                        Apply
                    </button>

                    <button wire:click="$set('selected', []); $set('selectAll', false)"
                        class="px-3 py-2 text-sm text-gray-400 hover:text-white transition">
                        Clear
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Task Cards Grid --}}
    @php
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
        $statusBadge = [
            'gray'  => 'bg-gray-500/10 text-gray-400 ring-gray-500/20',
            'blue'  => 'bg-blue-500/10 text-blue-400 ring-blue-500/20',
            'green' => 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
            'red'   => 'bg-red-500/10 text-red-400 ring-red-500/20',
        ];
    @endphp

    {{-- Select All Toggle --}}
    <div class="mb-4 flex items-center space-x-3 px-1">
        <label class="flex items-center space-x-2 cursor-pointer group">
            <input type="checkbox" wire:model.live="selectAll"
                class="rounded bg-white/[0.04] border-white/10 text-indigo-500 focus:ring-indigo-500/20 focus:ring-offset-0 transition">
            <span class="text-xs text-gray-500 group-hover:text-gray-300 transition">Select all</span>
        </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 animate-stagger">
        @forelse ($tasks as $task)
            <div class="group glass-card rounded-2xl overflow-hidden relative {{ in_array((string)$task->id, $selected) ? 'ring-1 ring-indigo-500/40 bg-indigo-500/5' : '' }}">
                {{-- Selection checkbox --}}
                <div class="absolute top-3 right-3 z-10">
                    <input type="checkbox" wire:model.live="selected" value="{{ $task->id }}"
                        class="rounded bg-white/[0.04] border-white/10 text-indigo-500 focus:ring-indigo-500/20 focus:ring-offset-0 transition opacity-0 group-hover:opacity-100 {{ in_array((string)$task->id, $selected) ? '!opacity-100' : '' }}">
                </div>

                {{-- Priority accent bar at top --}}
                <div class="h-1 bg-gradient-to-r {{ $priorityAccent[$task->priority->color()] ?? 'from-gray-500 to-gray-400' }}"></div>

                {{-- Card Body --}}
                <div class="p-5">
                    {{-- Top: Priority + Status badges --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold ring-1 ring-inset {{ $priorityBadge[$task->priority->color()] ?? 'bg-gray-500/10 text-gray-400 ring-gray-500/20' }}">
                            {{ $task->priority->label() }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold ring-1 ring-inset {{ $statusBadge[$task->status->color()] ?? 'bg-gray-500/10 text-gray-400 ring-gray-500/20' }}">
                            {{ $task->status->label() }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <a href="{{ route('tasks.show', $task) }}" class="block">
                        <h3 class="text-base font-semibold text-white mb-2 line-clamp-1 group-hover:text-indigo-400 transition-colors duration-200">
                            {{ $task->title }}
                        </h3>
                    </a>

                    {{-- Description --}}
                    @if ($task->description)
                        <p class="text-sm text-gray-400 mb-4 line-clamp-2 leading-relaxed">{{ $task->description }}</p>
                    @else
                        <p class="text-sm text-gray-600 italic mb-4">No description</p>
                    @endif

                    {{-- Due Date --}}
                    <div class="flex items-center text-xs mb-4 {{ $task->isOverdue() ? 'text-red-400' : 'text-gray-500' }}">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        @if ($task->due_date)
                            <span style="font-family: 'JetBrains Mono', monospace;" class="text-[11px]">{{ $task->due_date->format('M d, Y') }}</span>
                            @if ($task->isOverdue())
                                <span class="ml-1.5 px-1.5 py-0.5 bg-red-500/10 text-red-400 rounded-md text-[10px] font-bold uppercase ring-1 ring-inset ring-red-500/20">Overdue</span>
                            @endif
                        @else
                            No due date
                        @endif
                    </div>
                </div>

                {{-- Card Footer --}}
                <div class="px-5 py-3.5 border-t border-white/[0.04] flex items-center justify-between bg-white/[0.01]">
                    {{-- Assignee --}}
                    <div class="flex items-center space-x-2.5">
                        @if ($task->assignee)
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-bold shadow-md shadow-indigo-500/20">
                                {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
                            </div>
                            <span class="text-xs text-gray-400 font-medium">{{ $task->assignee->name }}</span>
                        @else
                            <div class="w-7 h-7 rounded-lg bg-white/[0.04] border border-dashed border-white/10 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="text-xs text-gray-600">Unassigned</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        @can('update', $task)
                            <a href="{{ route('tasks.edit', $task) }}"
                               class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-400 hover:bg-indigo-500/10 transition-all"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        @endcan
                        @can('delete', $task)
                            <button wire:click="deleteTask({{ $task->id }})"
                                    wire:confirm="Are you sure you want to delete '{{ $task->title }}'?"
                                    class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all"
                                    title="Delete">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="col-span-full flex flex-col items-center justify-center py-20 glass-card rounded-2xl hover:transform-none">
                <div class="w-20 h-20 rounded-2xl bg-white/[0.04] flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-300 mb-1">No tasks found</h3>
                <p class="text-sm text-gray-500 max-w-xs text-center">Try adjusting your filters or create a new task to get started.</p>
            </div>
        @endforelse
    </div>
</div>
