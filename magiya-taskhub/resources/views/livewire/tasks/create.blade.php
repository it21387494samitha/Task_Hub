{{-- Premium Create Task form --}}
<div>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('tasks.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/[0.05] transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Create Task</h2>
                <p class="mt-0.5 text-sm text-gray-400">Fill in the details to create a new task</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="glass-card rounded-2xl p-6 sm:p-8 hover:transform-none">

            <form wire:submit="save" class="space-y-6">

                {{-- Template Quick Fill --}}
                @if ($templates->count())
                    <div class="space-y-1.5">
                        <label for="templateId" class="block text-sm font-medium text-gray-300">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                Use Template <span class="text-gray-500 text-xs ml-1">(optional)</span>
                            </span>
                        </label>
                        <select wire:model.live="templateId" id="templateId"
                            class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-purple-500/30 focus:ring-1 focus:ring-purple-500/20 transition">
                            <option value="">— Select a template —</option>
                            @foreach ($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->type->value }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Title --}}
                <div class="space-y-1.5">
                    <label for="title" class="block text-sm font-medium text-gray-300">Title <span class="text-red-400">*</span></label>
                    <input wire:model="title" type="text" id="title"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 placeholder-gray-500 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition"
                        placeholder="Enter task title">
                    @error('title') <p class="text-sm text-red-400 flex items-center"><svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="space-y-1.5">
                    <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                    <textarea wire:model="description" id="description" rows="3"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 placeholder-gray-500 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition"
                        placeholder="Describe the task..."></textarea>
                    @error('description') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Priority --}}
                    <div class="space-y-1.5">
                        <label for="priority" class="block text-sm font-medium text-gray-300">Priority</label>
                        <select wire:model="priority" id="priority"
                            class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                            @foreach ($priorities as $p)
                                <option value="{{ $p->value }}">{{ $p->label() }}</option>
                            @endforeach
                        </select>
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

                    {{-- Assigned To --}}
                    <div class="space-y-1.5">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-300">Assign To</label>
                        <select wire:model="assigned_to" id="assigned_to"
                            class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                            <option value="">— Unassigned —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Due Date --}}
                    <div class="space-y-1.5">
                        <label for="due_date" class="block text-sm font-medium text-gray-300">Due Date</label>
                        <input wire:model="due_date" type="date" id="due_date"
                            class="w-full rounded-xl bg-white/[0.04] border border-white/[0.06] text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                        @error('due_date') <p class="text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Tags --}}
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

                {{-- Server / Auth Error Banner --}}
                @if (session()->has('error'))
                    <div class="rounded-xl bg-red-500/10 border border-red-500/30 px-4 py-3 text-sm text-red-400">
                        <strong>Error:</strong> {{ session('error') }}
                    </div>
                @endif

                {{-- Buttons --}}
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-white/[0.06]">
                    <a href="{{ route('tasks.index') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm font-medium text-gray-300 hover:bg-white/[0.08] hover:text-white transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-xl text-sm font-semibold text-white hover:from-indigo-500 hover:to-indigo-400 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-300 hover:-translate-y-0.5"
                        wire:loading.class="opacity-75 cursor-wait">
                        <span wire:loading.remove wire:target="save">Create Task</span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Creating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
