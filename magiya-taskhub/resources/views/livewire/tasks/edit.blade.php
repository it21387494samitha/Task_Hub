{{-- Premium Edit Task form --}}
<div>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('tasks.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/[0.05] transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Edit Task</h2>
                <p class="mt-0.5 text-sm text-gray-400">{{ $task->title }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="glass-card rounded-2xl p-6 sm:p-8 hover:transform-none">

            <form wire:submit="save" class="space-y-6">

                {{-- Title --}}
                <div class="space-y-1.5">
                    <label for="title" class="block text-sm font-medium text-gray-300">Title <span class="text-red-400">*</span></label>
                    <input wire:model="title" type="text" id="title"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                    @error('title') <p class="text-sm text-red-400 flex items-center"><svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="space-y-1.5">
                    <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                    <textarea wire:model="description" id="description" rows="3"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition"></textarea>
                    @error('description') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Priority (disabled for developers) --}}
                    <div class="space-y-1.5">
                        <label for="priority" class="block text-sm font-medium text-gray-300">Priority</label>
                        <select wire:model="priority" id="priority"
                            @if(auth()->user()->isDeveloper()) disabled @endif
                            class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition @if(auth()->user()->isDeveloper()) opacity-40 cursor-not-allowed @endif">
                            @foreach ($priorities as $p)
                                <option value="{{ $p->value }}">{{ $p->label() }}</option>
                            @endforeach
                        </select>
                        @if(auth()->user()->isDeveloper())
                            <p class="text-xs text-gray-500 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Only leads & admins can change priority
                            </p>
                        @endif
                        @error('priority') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="space-y-1.5">
                        <label for="status" class="block text-sm font-medium text-gray-300">Status</label>
                        <select wire:model="status" id="status"
                            class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                            @foreach ($statuses as $s)
                                <option value="{{ $s->value }}">{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Assigned To (disabled for developers) --}}
                    <div class="space-y-1.5">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-300">Assign To</label>
                        <select wire:model="assigned_to" id="assigned_to"
                            @if(auth()->user()->isDeveloper()) disabled @endif
                            class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition @if(auth()->user()->isDeveloper()) opacity-40 cursor-not-allowed @endif">
                            <option value="">— Unassigned —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @if(auth()->user()->isDeveloper())
                            <p class="text-xs text-gray-500 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Only leads & admins can reassign
                            </p>
                        @endif
                        @error('assigned_to') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Due Date (disabled for developers) --}}
                    <div class="space-y-1.5">
                        <label for="due_date" class="block text-sm font-medium text-gray-300">Due Date</label>
                        <input wire:model="due_date" type="date" id="due_date"
                            @if(auth()->user()->isDeveloper()) disabled @endif
                            class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition @if(auth()->user()->isDeveloper()) opacity-40 cursor-not-allowed @endif">
                        @if(auth()->user()->isDeveloper())
                            <p class="text-xs text-gray-500 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Only leads & admins can change deadlines
                            </p>
                        @endif
                        @error('due_date') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Tags --}}
                @if (!auth()->user()->isDeveloper())
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-gray-300">Tags</label>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $tagColors = [
                                    'red'    => 'peer-checked:bg-red-500/20 peer-checked:text-red-400 peer-checked:ring-red-500/30',
                                    'orange' => 'peer-checked:bg-orange-500/20 peer-checked:text-orange-400 peer-checked:ring-orange-500/30',
                                    'purple' => 'peer-checked:bg-purple-500/20 peer-checked:text-purple-400 peer-checked:ring-purple-500/30',
                                    'yellow' => 'peer-checked:bg-yellow-500/20 peer-checked:text-yellow-400 peer-checked:ring-yellow-500/30',
                                ];
                            @endphp
                            @foreach ($tags as $tag)
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" wire:model="selectedTags" value="{{ $tag->value }}" class="peer sr-only">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold ring-1 ring-inset ring-white/10 bg-white/[0.04] text-gray-400 transition-all {{ $tagColors[$tag->color()] ?? '' }}">
                                        {{ $tag->label() }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Block Reason (shown when status is blocked) --}}
                <div x-show="$wire.status === 'blocked'" x-transition class="space-y-1.5">
                    <label for="block_reason" class="block text-sm font-medium text-red-400">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.068 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            Block Reason
                        </span>
                    </label>
                    <textarea wire:model="block_reason" id="block_reason" rows="2" placeholder="Why is this task blocked?"
                        class="w-full rounded-xl bg-red-500/[0.04] border border-red-500/[0.1] text-gray-200 placeholder-gray-500 focus:border-red-500/30 focus:ring-1 focus:ring-red-500/20 transition"></textarea>
                    @error('block_reason') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-white/[0.06]">
                    <a href="{{ route('tasks.index') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm font-medium text-gray-300 hover:bg-white/[0.08] hover:text-white transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-xl text-sm font-semibold text-white hover:from-indigo-500 hover:to-indigo-400 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-300 hover:-translate-y-0.5"
                        wire:loading.class="opacity-75 cursor-wait">
                        <span wire:loading.remove wire:target="save">Update Task</span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
