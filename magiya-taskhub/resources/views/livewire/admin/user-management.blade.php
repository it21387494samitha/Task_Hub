{{--
    Admin User Management — Premium glassmorphism table, role/team editing,
    toggle active/inactive, create/edit modals.
--}}
<div>
    {{-- Page Header --}}
    <div class="mb-8 relative overflow-hidden rounded-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700"></div>
        <div class="absolute inset-0 opacity-20"
             style="background-image: radial-gradient(at 30% 40%, rgba(59,130,246,0.5) 0px, transparent 50%), radial-gradient(at 70% 60%, rgba(139,92,246,0.4) 0px, transparent 50%);">
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white/5 rounded-full blur-xl"></div>

        <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-blue-200 text-sm font-medium mb-1">Admin Panel</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">User Management</h1>
                <p class="mt-2 text-indigo-200/80 text-sm">Manage roles, teams, and access for all users.</p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center px-5 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 group">
                <svg class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90 duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New User
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 glass-card rounded-2xl p-4 hover:transform-none">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative group">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 group-focus-within:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 placeholder-gray-500 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                </div>
            </div>
            <select wire:model.live="roleFilter"
                class="sm:w-40 py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                <option value="">All Roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="teamFilter"
                class="sm:w-40 py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                <option value="">All Teams</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="statusFilter"
                class="sm:w-36 py-2.5 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="glass-card rounded-2xl overflow-hidden hover:transform-none">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/[0.06]">
                        <th class="text-left py-4 px-5 text-gray-400 font-medium">User</th>
                        <th class="text-left py-4 px-5 text-gray-400 font-medium">Role</th>
                        <th class="text-left py-4 px-5 text-gray-400 font-medium">Team</th>
                        <th class="text-center py-4 px-5 text-gray-400 font-medium">Status</th>
                        <th class="text-right py-4 px-5 text-gray-400 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $roleBadge = [
                                'admin'       => 'bg-purple-500/10 text-purple-400 ring-purple-500/20',
                                'team_leader' => 'bg-blue-500/10 text-blue-400 ring-blue-500/20',
                                'developer'   => 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
                            ];
                        @endphp
                        <tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors {{ !$user->is_active ? 'opacity-50' : '' }}">
                            {{-- User Info --}}
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold shadow-md">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-gray-950 {{ $user->is_active ? 'bg-emerald-500' : 'bg-gray-600' }}"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="py-4 px-5">
                                <select wire:change="changeRole({{ $user->id }}, $event.target.value)"
                                        class="py-1 px-2 bg-transparent border border-white/[0.06] rounded-lg text-xs text-gray-300 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->value }}" {{ $user->role === $role ? 'selected' : '' }}>{{ $role->label() }}</option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Team --}}
                            <td class="py-4 px-5">
                                <select wire:change="assignTeam({{ $user->id }}, $event.target.value)"
                                        class="py-1 px-2 bg-transparent border border-white/[0.06] rounded-lg text-xs text-gray-300 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                                    <option value="" {{ !$user->team_id ? 'selected' : '' }}>No Team</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" {{ $user->team_id === $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-5 text-center">
                                <button wire:click="toggleActive({{ $user->id }})"
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors
                                               {{ $user->is_active
                                                   ? 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 hover:bg-emerald-500/20'
                                                   : 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20 hover:bg-red-500/20' }}"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-5 text-right">
                                <button wire:click="openEditModal({{ $user->id }})"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-gray-400 hover:text-white bg-white/[0.04] hover:bg-white/[0.08] transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Create User Modal ═══ --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$el.querySelector('input')?.focus()">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative w-full max-w-md mx-4 glass-card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-6">Create New User</h3>
                <form wire:submit="createUser" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Name</label>
                        <input wire:model="newName" type="text" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition" required>
                        @error('newName') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
                        <input wire:model="newEmail" type="email" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition" required>
                        @error('newEmail') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Password</label>
                        <input wire:model="newPassword" type="text" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Role</label>
                            <select wire:model="newRole" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Team</label>
                            <select wire:model="newTeamId" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                                <option value="">No Team</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                            class="px-4 py-2 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-white/[0.05] transition">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 rounded-xl text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══ Edit User Modal ═══ --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showEditModal', false)"></div>
            <div class="relative w-full max-w-md mx-4 glass-card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-6">Edit User</h3>
                <form wire:submit="updateUser" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Name</label>
                        <input wire:model="editName" type="text" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition" required>
                        @error('editName') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
                        <input wire:model="editEmail" type="email" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition" required>
                        @error('editEmail') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Role</label>
                            <select wire:model="editRole" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Team</label>
                            <select wire:model="editTeamId" class="w-full py-2.5 px-3 bg-white/[0.04] border border-white/[0.06] rounded-xl text-sm text-gray-200 focus:border-indigo-500/30 focus:ring-1 focus:ring-indigo-500/20 transition">
                                <option value="">No Team</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showEditModal', false)"
                            class="px-4 py-2 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-white/[0.05] transition">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 rounded-xl text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
