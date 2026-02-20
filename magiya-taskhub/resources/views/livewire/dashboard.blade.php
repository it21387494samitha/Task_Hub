{{--
    Premium Dashboard — Glass cards, animated counters, staggered grids,
    gradient progress bars, polished activity timeline.
--}}
<div>
    {{-- Greeting Banner --}}
    <div class="mb-8 relative overflow-hidden rounded-2xl">
        {{-- Gradient Background --}}
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800"></div>
        {{-- Mesh overlay --}}
        <div class="absolute inset-0 opacity-20"
             style="background-image: radial-gradient(at 20% 30%, rgba(139,92,246,0.5) 0px, transparent 50%), radial-gradient(at 80% 70%, rgba(59,130,246,0.4) 0px, transparent 50%), radial-gradient(at 50% 10%, rgba(236,72,153,0.3) 0px, transparent 50%);">
        </div>
        {{-- Decorative elements --}}
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white/5 rounded-full blur-xl"></div>
        <div class="absolute bottom-0 left-1/3 -mb-12 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 right-16 -mb-6 w-20 h-20 bg-white/5 rounded-full"></div>

        <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-indigo-200 text-sm font-medium mb-1">
                    {{ now()->format('l, F j, Y') }}
                </p>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    Welcome back, {{ $user->name }}!
                </h1>
                <p class="mt-2 text-indigo-200/80 text-sm sm:text-base max-w-md">
                    Here's what's happening with your tasks today.
                </p>
            </div>
            @php
                $roleLabels = ['admin' => 'Admin', 'team_leader' => 'Team Leader', 'developer' => 'Developer'];
                $roleIcons = [
                    'admin'       => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    'team_leader' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    'developer'   => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                ];
            @endphp
            <div class="self-start sm:self-auto">
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-white/10 text-white border border-white/20 backdrop-blur-sm shadow-lg">
                    <svg class="w-4 h-4 mr-2 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $roleIcons[$stats['user_role']] ?? $roleIcons['developer'] }}" />
                    </svg>
                    {{ $roleLabels[$stats['user_role']] ?? $stats['user_role'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    @php
        $statCards = [
            [
                'label'    => $stats['user_role'] === 'developer' ? 'My Tasks' : 'Total Tasks',
                'value'    => $stats['total_tasks'],
                'icon'     => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'color'    => 'indigo',
                'gradient' => 'from-indigo-500/20 to-indigo-600/5',
            ],
            [
                'label'    => $stats['user_role'] === 'developer' ? 'My Overdue' : 'Overdue',
                'value'    => $stats['overdue_tasks'],
                'icon'     => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'color'    => $stats['overdue_tasks'] > 0 ? 'red' : 'emerald',
                'gradient' => $stats['overdue_tasks'] > 0 ? 'from-red-500/20 to-red-600/5' : 'from-emerald-500/20 to-emerald-600/5',
            ],
            [
                'label'    => 'In Progress',
                'value'    => $stats['tasks_by_status']['in_progress'] ?? 0,
                'icon'     => 'M13 10V3L4 14h7v7l9-11h-7z',
                'color'    => 'blue',
                'gradient' => 'from-blue-500/20 to-blue-600/5',
            ],
            [
                'label'    => 'Completed',
                'value'    => $stats['tasks_by_status']['done'] ?? 0,
                'icon'     => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'color'    => 'emerald',
                'gradient' => 'from-emerald-500/20 to-emerald-600/5',
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8 animate-stagger">
        @foreach ($statCards as $index => $card)
            <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                {{-- Background gradient glow --}}
                <div class="absolute inset-0 bg-gradient-to-br {{ $card['gradient'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>

                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-400">{{ $card['label'] }}</span>
                        <div class="w-10 h-10 rounded-xl bg-{{ $card['color'] }}-500/10 flex items-center justify-center ring-1 ring-{{ $card['color'] }}-500/20">
                            <svg class="w-5 h-5 text-{{ $card['color'] }}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-end justify-between">
                        <span class="text-3xl font-bold text-white animate-count" style="font-family: 'JetBrains Mono', monospace;">
                            {{ $card['value'] }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Middle Row: Tasks by Status + Team/My Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Tasks by Status (visual bars) --}}
        <div class="glass-card rounded-2xl p-6 hover:transform-none">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-white">
                    {{ $stats['user_role'] === 'developer' ? 'My Tasks by Status' : 'Tasks by Status' }}
                </h3>
                <span class="text-xs text-gray-500 font-medium">{{ $stats['total_tasks'] }} total</span>
            </div>
            @php
                $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done', 'blocked' => 'Blocked'];
                $statusBarGradients = [
                    'todo'        => 'from-gray-500 to-gray-400',
                    'in_progress' => 'from-blue-500 to-cyan-400',
                    'done'        => 'from-emerald-500 to-green-400',
                    'blocked'     => 'from-red-500 to-rose-400',
                ];
                $statusDotColors = ['todo' => 'bg-gray-400', 'in_progress' => 'bg-blue-400', 'done' => 'bg-emerald-400', 'blocked' => 'bg-red-400'];
                $total = max($stats['total_tasks'], 1);
            @endphp
            <div class="space-y-5">
                @foreach ($stats['tasks_by_status'] as $status => $count)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-2">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full {{ $statusDotColors[$status] ?? 'bg-gray-500' }} mr-2.5"></span>
                                <span class="text-gray-300 font-medium">{{ $statusLabels[$status] ?? $status }}</span>
                            </div>
                            <span class="font-bold text-white tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-white/[0.04] rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r {{ $statusBarGradients[$status] ?? 'from-gray-500 to-gray-400' }} h-2 rounded-full animate-bar"
                                 style="width: {{ round(($count / $total) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Team / Personal Summary --}}
        <div class="glass-card rounded-2xl p-6 hover:transform-none">
            @if ($stats['user_role'] === 'developer')
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-semibold text-white">My Task Summary</h3>
                </div>
                <div class="space-y-2.5">
                    @foreach ($stats['tasks_by_status'] as $status => $count)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition-colors">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full {{ $statusDotColors[$status] ?? 'bg-gray-500' }} mr-3"></span>
                                <span class="text-sm text-gray-300 font-medium capitalize">{{ str_replace('_', ' ', $status) }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-400 bg-indigo-500/10 ring-1 ring-inset ring-indigo-500/20 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">
                                {{ $count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-semibold text-white">Tasks per Developer</h3>
                    <span class="text-xs text-gray-500 font-medium">{{ count($stats['tasks_per_user']) }} members</span>
                </div>
                @if (count($stats['tasks_per_user']) > 0)
                    <div class="space-y-2.5">
                        @foreach ($stats['tasks_per_user'] as $userStat)
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition-colors group/user">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold shadow-md shadow-indigo-500/20 group-hover/user:shadow-indigo-500/30 transition-shadow">
                                        {{ strtoupper(substr($userStat['name'], 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-gray-300 font-medium">{{ $userStat['name'] }}</span>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-400 bg-indigo-500/10 ring-1 ring-inset ring-indigo-500/20 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">
                                    {{ $userStat['task_count'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center py-10 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-white/[0.04] flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">No tasks assigned yet.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Recent Activity Feed --}}
    <div class="glass-card rounded-2xl p-6 hover:transform-none">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center ring-1 ring-indigo-500/20">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-white">
                    {{ $stats['user_role'] === 'developer' ? 'My Recent Activity' : 'Recent Activity' }}
                </h3>
            </div>
            <span class="text-xs text-gray-500 font-medium px-2.5 py-1 rounded-lg bg-white/[0.03]">Last 10</span>
        </div>

        @if ($recentActivity->count() > 0)
            <div class="flow-root">
                <ul class="-mb-8">
                    @foreach ($recentActivity as $index => $log)
                        <li>
                            <div class="relative pb-8">
                                @if (! $loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gradient-to-b from-white/[0.08] to-transparent" aria-hidden="true"></span>
                                @endif

                                <div class="relative flex space-x-3">
                                    {{-- Action icon --}}
                                    <div>
                                        @php
                                            $iconColors = [
                                                'created'  => 'bg-emerald-500/15 text-emerald-400 ring-emerald-500/20',
                                                'updated'  => 'bg-blue-500/15 text-blue-400 ring-blue-500/20',
                                                'deleted'  => 'bg-red-500/15 text-red-400 ring-red-500/20',
                                                'assigned' => 'bg-amber-500/15 text-amber-400 ring-amber-500/20',
                                            ];
                                            $iconColor = $iconColors[$log->action] ?? 'bg-gray-500/15 text-gray-400 ring-gray-500/20';
                                        @endphp
                                        <span class="h-8 w-8 rounded-lg {{ $iconColor }} flex items-center justify-center ring-1 ring-inset">
                                            @switch($log->action)
                                                @case('created')
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    @break
                                                @case('updated')
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    @break
                                                @case('deleted')
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    @break
                                                @case('assigned')
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    @break
                                                @default
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endswitch
                                        </span>
                                    </div>

                                    {{-- Description + timestamp --}}
                                    <div class="min-w-0 flex-1 pt-1 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-300">{{ $log->description }}</p>
                                            @if ($log->changes)
                                                <div class="mt-2 flex flex-wrap gap-1.5">
                                                    @foreach ($log->changes as $field => $change)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-white/[0.04] text-gray-400 ring-1 ring-inset ring-white/[0.06]">
                                                            {{ $field }}: <span class="text-gray-500 mx-1">{{ $change['old'] ?? '—' }}</span>→ <span class="text-white ml-1">{{ $change['new'] ?? '—' }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-gray-500 pt-0.5">
                                            {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="flex flex-col items-center py-10 text-center">
                <div class="w-14 h-14 rounded-2xl bg-white/[0.04] flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500">No activity yet. Create or update a task to see the audit trail here.</p>
            </div>
        @endif
    </div>
</div>
