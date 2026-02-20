<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    @php
        $user = Auth::user();
        $initials = strtoupper(substr($user->name, 0, 2));
        $roleName = ucfirst(str_replace('_', ' ', $user->role?->value ?? 'developer'));
        $memberSince = $user->created_at->format('M Y');
        $assignedCount = $user->assignedTasks()->count();
        $completedCount = $user->assignedTasks()->where('status', \App\Enums\TaskStatus::DONE)->count();
        $teamName = $user->team?->name ?? 'No team';
    @endphp

    <div class="py-6 animate-stagger">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ══════════ HERO PROFILE CARD ══════════ --}}
            <div class="glass-card rounded-2xl overflow-hidden gradient-border">
                {{-- Banner gradient --}}
                <div class="relative h-36 sm:h-44 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/30 via-purple-600/20 to-pink-600/10"></div>
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 80%, rgba(99,102,241,0.25) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(168,85,247,0.2) 0%, transparent 50%), radial-gradient(circle at 50% 50%, rgba(236,72,153,0.1) 0%, transparent 50%);"></div>
                    {{-- Decorative floating orbs --}}
                    <div class="absolute top-6 right-12 w-20 h-20 rounded-full bg-indigo-500/10 blur-2xl animate-pulse"></div>
                    <div class="absolute bottom-4 left-16 w-16 h-16 rounded-full bg-purple-500/10 blur-xl animate-pulse" style="animation-delay: 1s;"></div>
                    <div class="absolute top-10 left-1/3 w-12 h-12 rounded-full bg-pink-500/10 blur-xl animate-pulse" style="animation-delay: 2s;"></div>
                </div>

                {{-- Profile info overlay --}}
                <div class="relative px-6 sm:px-8 pb-6">
                    {{-- Avatar --}}
                    <div class="-mt-16 mb-4 flex items-end justify-between">
                        <div class="flex items-end gap-5">
                            <div class="relative group">
                                <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-black text-3xl shadow-2xl shadow-indigo-500/25 ring-4 ring-gray-950 transition-transform duration-300 group-hover:scale-105">
                                    {{ $initials }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-[3px] border-gray-950 shadow-lg shadow-emerald-500/30"></div>
                            </div>
                            <div class="pb-1">
                                <h1 class="text-2xl font-bold text-white tracking-tight">{{ $user->name }}</h1>
                                <p class="text-sm text-gray-400 mt-0.5">{{ $user->email }}</p>
                            </div>
                        </div>

                        {{-- Role badge --}}
                        <div class="hidden sm:flex items-center gap-2 pb-1">
                            @php
                                $roleColors = [
                                    'admin' => 'from-amber-500/20 to-orange-500/20 border-amber-500/30 text-amber-400',
                                    'team_leader' => 'from-indigo-500/20 to-purple-500/20 border-indigo-500/30 text-indigo-400',
                                    'developer' => 'from-emerald-500/20 to-teal-500/20 border-emerald-500/30 text-emerald-400',
                                ];
                                $roleColor = $roleColors[$user->role?->value ?? 'developer'] ?? $roleColors['developer'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-gradient-to-r {{ $roleColor }} border text-xs font-bold uppercase tracking-wider">
                                @if($user->role?->value === 'admin')
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @elseif($user->role?->value === 'team_leader')
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                @endif
                                {{ $roleName }}
                            </span>
                        </div>
                    </div>

                    {{-- Stats strip --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                        <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl px-4 py-3 text-center hover:bg-white/[0.05] transition-all duration-200 hover:-translate-y-0.5">
                            <p class="text-xl font-bold text-white font-mono tabular-nums">{{ $assignedCount }}</p>
                            <p class="text-[11px] text-gray-500 font-medium mt-0.5 uppercase tracking-wider">Assigned</p>
                        </div>
                        <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl px-4 py-3 text-center hover:bg-white/[0.05] transition-all duration-200 hover:-translate-y-0.5">
                            <p class="text-xl font-bold text-emerald-400 font-mono tabular-nums">{{ $completedCount }}</p>
                            <p class="text-[11px] text-gray-500 font-medium mt-0.5 uppercase tracking-wider">Completed</p>
                        </div>
                        <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl px-4 py-3 text-center hover:bg-white/[0.05] transition-all duration-200 hover:-translate-y-0.5">
                            <p class="text-xl font-bold text-indigo-400 font-mono tabular-nums">{{ $memberSince }}</p>
                            <p class="text-[11px] text-gray-500 font-medium mt-0.5 uppercase tracking-wider">Member Since</p>
                        </div>
                        <div class="bg-white/[0.03] border border-white/[0.06] rounded-xl px-4 py-3 text-center hover:bg-white/[0.05] transition-all duration-200 hover:-translate-y-0.5">
                            <p class="text-sm font-bold text-purple-400 truncate">{{ $teamName }}</p>
                            <p class="text-[11px] text-gray-500 font-medium mt-0.5 uppercase tracking-wider">Team</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════ TWO-COLUMN LAYOUT (lg) ══════════ --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ── LEFT: SIDEBAR NAV ── --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Quick navigation --}}
                    <div class="glass-card rounded-2xl p-5 gradient-border" x-data="{ activeTab: 'profile' }">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 px-1">Settings</h3>
                        <nav class="space-y-1">
                            <a href="#profile-section" @click="activeTab = 'profile'"
                               :class="activeTab === 'profile' ? 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-200 hover:bg-white/[0.03]'"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl border transition-all duration-200 group">
                                <div :class="activeTab === 'profile' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-white/[0.05] text-gray-500 group-hover:text-gray-400'"
                                     class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Profile Information</p>
                                    <p class="text-[11px] text-gray-600">Name, email & verification</p>
                                </div>
                            </a>
                            <a href="#password-section" @click="activeTab = 'password'"
                               :class="activeTab === 'password' ? 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-200 hover:bg-white/[0.03]'"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl border transition-all duration-200 group">
                                <div :class="activeTab === 'password' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-white/[0.05] text-gray-500 group-hover:text-gray-400'"
                                     class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Security</p>
                                    <p class="text-[11px] text-gray-600">Password & authentication</p>
                                </div>
                            </a>
                            <a href="#danger-section" @click="activeTab = 'danger'"
                               :class="activeTab === 'danger' ? 'bg-red-500/10 border-red-500/30 text-red-400' : 'border-transparent text-gray-400 hover:text-gray-200 hover:bg-white/[0.03]'"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl border transition-all duration-200 group">
                                <div :class="activeTab === 'danger' ? 'bg-red-500/20 text-red-400' : 'bg-white/[0.05] text-gray-500 group-hover:text-gray-400'"
                                     class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Danger Zone</p>
                                    <p class="text-[11px] text-gray-600">Delete account permanently</p>
                                </div>
                            </a>
                        </nav>
                    </div>

                    {{-- Activity quick-glance --}}
                    <div class="glass-card rounded-2xl p-5 gradient-border">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 px-1">Completion Rate</h3>
                        @php
                            $rate = $assignedCount > 0 ? round(($completedCount / $assignedCount) * 100) : 0;
                        @endphp
                        <div class="relative mt-2">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-300">Tasks Completed</span>
                                <span class="text-sm font-bold font-mono tabular-nums {{ $rate >= 75 ? 'text-emerald-400' : ($rate >= 50 ? 'text-amber-400' : 'text-red-400') }}">{{ $rate }}%</span>
                            </div>
                            <div class="w-full h-2 bg-white/[0.05] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $rate >= 75 ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : ($rate >= 50 ? 'bg-gradient-to-r from-amber-500 to-amber-400' : 'bg-gradient-to-r from-red-500 to-red-400') }}"
                                     style="width: {{ $rate }}%"></div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $completedCount }} of {{ $assignedCount }} tasks completed
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT: FORM SECTIONS ── --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Profile Information --}}
                    <div id="profile-section" class="glass-card rounded-2xl overflow-hidden gradient-border">
                        <div class="px-6 sm:px-8 pt-6 pb-0 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                                <svg class="w-4.5 h-4.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-white">Profile Information</h2>
                                <p class="text-xs text-gray-500">Manage your name, email and public identity</p>
                            </div>
                        </div>
                        <div class="p-6 sm:p-8">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- Password --}}
                    <div id="password-section" class="glass-card rounded-2xl overflow-hidden gradient-border">
                        <div class="px-6 sm:px-8 pt-6 pb-0 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                                <svg class="w-4.5 h-4.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-white">Security</h2>
                                <p class="text-xs text-gray-500">Update your password to keep your account secure</p>
                            </div>
                        </div>
                        <div class="p-6 sm:p-8">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- Danger Zone --}}
                    <div id="danger-section" class="glass-card rounded-2xl overflow-hidden border-red-500/10">
                        <div class="px-6 sm:px-8 pt-6 pb-0 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                                <svg class="w-4.5 h-4.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-red-400">Danger Zone</h2>
                                <p class="text-xs text-gray-500">Permanently delete your account and all data</p>
                            </div>
                        </div>
                        <div class="p-6 sm:p-8">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
