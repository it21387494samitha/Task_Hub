{{--
    Admin Team Management — Premium glassmorphism cards, create/edit modals,
    expandable member view, remove members, delete teams.
--}}
<div>
    {{-- Page Header --}}
    <div class="mb-8 relative overflow-hidden rounded-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700"></div>
        <div class="absolute inset-0 opacity-20"
             style="background-image: radial-gradient(at 30% 40%, rgba(16,185,129,0.5) 0px, transparent 50%), radial-gradient(at 70% 60%, rgba(6,182,212,0.4) 0px, transparent 50%);">
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white/5 rounded-full blur-xl"></div>

        <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-emerald-200 text-sm font-medium mb-1">Admin Panel</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Team Management</h1>
                <p class="mt-2 text-teal-200/80 text-sm">Organize teams, manage members, and monitor structure.</p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center px-5 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 group">
                <svg class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90 duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Team
            </button>
        </div>
    </div>

    {{-- Team Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 animate-stagger">
        @forelse ($teams as $team)
            <div class="glass-card rounded-2xl p-5 hover:border-emerald-500/20 transition-all duration-300 group">
                {{-- Team Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xs font-bold shadow-lg">
                            {{ strtoupper(substr($team->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white group-hover:text-emerald-300 transition-colors">{{ $team->name }}</h3>
                            @if ($team->description)
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $team->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <button wire:click="openEditModal({{ $team->id }})"
                                class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-400 hover:bg-white/[0.04] transition-colors"
                                title="Edit">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="deleteTeam({{ $team->id }})"
                                wire:confirm="Delete '{{ $team->name }}'? All members will be unassigned."
                                class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-white/[0.04] transition-colors"
                                title="Delete">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center py-2 px-1 bg-white/[0.02] rounded-lg">
                        <p class="text-lg font-bold text-white" style="font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums;">{{ $team->members_count }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Members</p>
                    </div>
                    <div class="text-center py-2 px-1 bg-white/[0.02] rounded-lg">
                        <p class="text-lg font-bold text-emerald-400" style="font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums;">{{ $team->openTaskCount() }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Open Tasks</p>
                    </div>
                    <div class="text-center py-2 px-1 bg-white/[0.02] rounded-lg">
                        <p class="text-xs font-medium text-gray-400 mt-0.5">{{ $team->creator?->name ?? '—' }}</p>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Created By</p>
                    </div>
                </div>

                {{-- Members Toggle --}}
                <button wire:click="viewMembers({{ $team->id }})"
                        class="w-full text-left flex items-center justify-between py-2.5 px-3 rounded-xl bg-white/[0.02] hover:bg-white/[0.04] transition-colors text-xs">
                    <span class="text-gray-400 font-medium">Team Members</span>
                    <svg class="w-4 h-4 text-gray-500 transition-transform {{ $viewingTeamId === $team->id ? 'rotate-180' : '' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                @if ($viewingTeamId === $team->id)
                    <div class="mt-3 space-y-2">
                        @forelse ($team->members as $member)
                            @php
                                $roleBadge = match($member->role->value) {
                                    'admin'       => 'text-purple-400',
                                    'team_leader' => 'text-blue-400',
                                    default       => 'text-emerald-400',
                                };
                            @endphp
                            <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-white/[0.02] hover:bg-white/[0.04] transition-colors">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-[10px] text-white font-semibold">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-white">{{ $member->name }}</p>
                                        <p class="text-[10px] {{ $roleBadge }}">{{ $member->role->label() }}</p>
                                    </div>
                                </div>
                                <button wire:click="removeMember({{ $member->id }})"
                                        wire:confirm="Remove {{ $member->name }} from this team?"
                                        class="p-1 rounded text-gray-600 hover:text-red-400 hover:bg-white/[0.04] transition-colors"
                                        title="Remove from team">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-xs text-gray-600 text-center py-3">No members yet.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full">
                <div class="glass-card rounded-2xl p-12 text-center hover:transform-none">
                    <svg class="mx-auto w-12 h-12 text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    <p class="text-gray-400 font-medium mb-1">No teams yet</p>
                    <p class="text-gray-600 text-sm">Click "New Team" to create your first team.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ═══ Create Team Modal ═══ --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$el.querySelector('input')?.focus()">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative w-full max-w-md mx-4 glass-card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-6">Create New Team</h3>
                <form wire:submit="createTeam" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Team Name</label>
                        <input wire:model="newName" type="text" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 transition" required placeholder="e.g. Backend Team">
                        @error('newName') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Description</label>
                        <textarea wire:model="newDescription" rows="3" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 transition resize-none" placeholder="Brief team description..."></textarea>
                        @error('newDescription') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                            class="px-4 py-2 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-white/[0.05] transition">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 rounded-xl text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 transition-all">
                            Create Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══ Edit Team Modal ═══ --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showEditModal', false)"></div>
            <div class="relative w-full max-w-md mx-4 glass-card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-6">Edit Team</h3>
                <form wire:submit="updateTeam" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Team Name</label>
                        <input wire:model="editName" type="text" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 transition" required>
                        @error('editName') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Description</label>
                        <textarea wire:model="editDescription" rows="3" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-emerald-500/30 focus:ring-1 focus:ring-emerald-500/20 transition resize-none"></textarea>
                        @error('editDescription') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showEditModal', false)"
                            class="px-4 py-2 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-white/[0.05] transition">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 rounded-xl text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
