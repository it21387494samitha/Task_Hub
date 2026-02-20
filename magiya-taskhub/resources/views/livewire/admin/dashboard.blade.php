{{--
    Admin Analytics Dashboard — Premium glassmorphism, animated stat cards,
    workload heatmap, aging bars, bottleneck insights, SLA gauges, team stats.
--}}
<div>
    {{-- Page Header --}}
    <div class="mb-8 relative overflow-hidden rounded-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-700"></div>
        <div class="absolute inset-0 opacity-20"
             style="background-image: radial-gradient(at 20% 30%, rgba(139,92,246,0.5) 0px, transparent 50%), radial-gradient(at 80% 70%, rgba(59,130,246,0.4) 0px, transparent 50%);">
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-white/5 rounded-full blur-xl"></div>

        <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-purple-200 text-sm font-medium mb-1">Admin Panel</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Analytics Dashboard</h1>
                <p class="mt-2 text-indigo-200/80 text-sm max-w-lg">
                    Real-time insights into task health, team performance, and bottlenecks.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="exportCsv"
                    class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ Top Summary Cards ═══ --}}
    @php
        $cards = [
            ['label' => 'Health Score', 'value' => $summary['health_score'] . '%', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => $summary['health_score'] >= 80 ? 'emerald' : ($summary['health_score'] >= 50 ? 'yellow' : 'red'), 'gradient' => $summary['health_score'] >= 80 ? 'from-emerald-500/20 to-emerald-600/5' : ($summary['health_score'] >= 50 ? 'from-yellow-500/20 to-yellow-600/5' : 'from-red-500/20 to-red-600/5')],
            ['label' => 'Total Tasks', 'value' => $summary['total_tasks'], 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'indigo', 'gradient' => 'from-indigo-500/20 to-indigo-600/5'],
            ['label' => 'Overdue', 'value' => $summary['overdue'], 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => $summary['overdue'] > 0 ? 'red' : 'emerald', 'gradient' => $summary['overdue'] > 0 ? 'from-red-500/20 to-red-600/5' : 'from-emerald-500/20 to-emerald-600/5'],
            ['label' => 'Blocked', 'value' => $summary['blocked'], 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636', 'color' => $summary['blocked'] > 0 ? 'orange' : 'emerald', 'gradient' => $summary['blocked'] > 0 ? 'from-orange-500/20 to-orange-600/5' : 'from-emerald-500/20 to-emerald-600/5'],
            ['label' => 'Stuck', 'value' => $summary['stuck'], 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => $summary['stuck'] > 0 ? 'amber' : 'emerald', 'gradient' => $summary['stuck'] > 0 ? 'from-amber-500/20 to-amber-600/5' : 'from-emerald-500/20 to-emerald-600/5'],
            ['label' => 'Completion Rate', 'value' => $summary['completion_rate'] . '%', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'blue', 'gradient' => 'from-blue-500/20 to-blue-600/5'],
            ['label' => 'Avg Cycle Time', 'value' => $summary['avg_cycle_hours'] . 'h', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'purple', 'gradient' => 'from-purple-500/20 to-purple-600/5'],
            ['label' => 'Active Users', 'value' => $summary['active_users'], 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'cyan', 'gradient' => 'from-cyan-500/20 to-cyan-600/5'],
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8 animate-stagger">
        @foreach ($cards as $card)
            <div class="glass-card rounded-2xl p-4 sm:p-5 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br {{ $card['gradient'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs sm:text-sm font-medium text-gray-400">{{ $card['label'] }}</span>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-{{ $card['color'] }}-500/10 flex items-center justify-center ring-1 ring-{{ $card['color'] }}-500/20">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-{{ $card['color'] }}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-2xl sm:text-3xl font-bold text-white animate-count" style="font-family: 'JetBrains Mono', monospace;">
                        {{ $card['value'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ Tasks by Status & Priority ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- By Status --}}
        <div class="glass-card rounded-2xl p-6 hover:transform-none">
            <h3 class="text-base font-semibold text-white mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center ring-1 ring-blue-500/20 mr-3">
                    <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                Tasks by Status
            </h3>
            @php
                $statusGradients = [
                    'todo'        => 'from-gray-500 to-gray-400',
                    'in_progress' => 'from-blue-500 to-cyan-400',
                    'done'        => 'from-emerald-500 to-green-400',
                    'blocked'     => 'from-red-500 to-rose-400',
                ];
                $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done', 'blocked' => 'Blocked'];
                $statusDots = ['todo' => 'bg-gray-400', 'in_progress' => 'bg-blue-400', 'done' => 'bg-emerald-400', 'blocked' => 'bg-red-400'];
                $total = max($health['total'], 1);
            @endphp
            <div class="space-y-5">
                @foreach ($health['by_status'] as $status => $count)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-2">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full {{ $statusDots[$status] ?? 'bg-gray-500' }} mr-2.5"></span>
                                <span class="text-gray-300 font-medium">{{ $statusLabels[$status] ?? $status }}</span>
                            </div>
                            <span class="font-bold text-white tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-white/[0.04] rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r {{ $statusGradients[$status] ?? 'from-gray-500 to-gray-400' }} h-2 rounded-full animate-bar"
                                 style="width: {{ round(($count / $total) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- By Priority --}}
        <div class="glass-card rounded-2xl p-6 hover:transform-none">
            <h3 class="text-base font-semibold text-white mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center ring-1 ring-orange-500/20 mr-3">
                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                </div>
                Tasks by Priority
            </h3>
            @php
                $priorityGradients = [
                    'low'      => 'from-gray-500 to-gray-400',
                    'medium'   => 'from-yellow-500 to-amber-400',
                    'high'     => 'from-orange-500 to-amber-500',
                    'critical' => 'from-red-500 to-rose-400',
                ];
                $priorityLabels = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'];
                $priorityDots = ['low' => 'bg-gray-400', 'medium' => 'bg-yellow-400', 'high' => 'bg-orange-400', 'critical' => 'bg-red-400'];
            @endphp
            <div class="space-y-5">
                @foreach ($health['by_priority'] as $priority => $count)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-2">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full {{ $priorityDots[$priority] ?? 'bg-gray-500' }} mr-2.5"></span>
                                <span class="text-gray-300 font-medium">{{ $priorityLabels[$priority] ?? $priority }}</span>
                            </div>
                            <span class="font-bold text-white tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-white/[0.04] rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r {{ $priorityGradients[$priority] ?? 'from-gray-500 to-gray-400' }} h-2 rounded-full animate-bar"
                                 style="width: {{ $total > 0 ? round(($count / $total) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ Workload Distribution ═══ --}}
    <div class="glass-card rounded-2xl p-6 hover:transform-none mb-8">
        <h3 class="text-base font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center ring-1 ring-purple-500/20 mr-3">
                <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            Workload Distribution
            <span class="ml-auto text-xs text-gray-500 font-medium px-2.5 py-1 rounded-lg bg-white/[0.03]">{{ $workload->count() }} active users</span>
        </h3>

        @if ($workload->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($workload as $w)
                    <div class="p-4 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition-colors border {{ $w['is_overloaded'] ? 'border-red-500/30' : 'border-transparent' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold shadow-md">
                                    {{ strtoupper(substr($w['name'], 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $w['name'] }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $w['team'] }} · {{ ucfirst(str_replace('_', ' ', $w['role'])) }}</p>
                                </div>
                            </div>
                            @if ($w['is_overloaded'])
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-500/10 text-red-400 ring-1 ring-red-500/20">OVERLOADED</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="px-2 py-1 rounded-md bg-white/[0.04] text-gray-300">
                                <span class="font-bold text-white" style="font-family: 'JetBrains Mono', monospace;">{{ $w['open_tasks'] }}</span> open
                            </span>
                            <span class="px-2 py-1 rounded-md bg-blue-500/10 text-blue-400">{{ $w['by_status']['in_progress'] }} in progress</span>
                            @if ($w['by_status']['blocked'] > 0)
                                <span class="px-2 py-1 rounded-md bg-red-500/10 text-red-400">{{ $w['by_status']['blocked'] }} blocked</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 text-center py-8">No active users with tasks.</p>
        @endif
    </div>

    {{-- ═══ Task Aging & SLA Compliance ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Aging Buckets --}}
        <div class="glass-card rounded-2xl p-6 hover:transform-none">
            <h3 class="text-base font-semibold text-white mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center ring-1 ring-amber-500/20 mr-3">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                Task Aging
            </h3>
            @php
                $agingColors = ['emerald' => 'from-emerald-500 to-green-400', 'yellow' => 'from-yellow-500 to-amber-400', 'orange' => 'from-orange-500 to-amber-500', 'red' => 'from-red-500 to-rose-400'];
                $agingDots = ['emerald' => 'bg-emerald-400', 'yellow' => 'bg-yellow-400', 'orange' => 'bg-orange-400', 'red' => 'bg-red-400'];
                $agingCounts = array_column($aging, 'count');
                $agingMax = !empty($agingCounts) ? max($agingCounts) : 1;
                $agingMax = max($agingMax, 1);
            @endphp
            <div class="space-y-5">
                @foreach ($aging as $bucket)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-2">
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full {{ $agingDots[$bucket['color']] ?? 'bg-gray-500' }} mr-2.5"></span>
                                <span class="text-gray-300 font-medium">{{ $bucket['label'] }}</span>
                            </div>
                            <span class="font-bold text-white tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $bucket['count'] }}</span>
                        </div>
                        <div class="w-full bg-white/[0.04] rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r {{ $agingColors[$bucket['color']] ?? 'from-gray-500 to-gray-400' }} h-2 rounded-full animate-bar"
                                 style="width: {{ round(($bucket['count'] / $agingMax) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SLA Compliance --}}
        <div class="glass-card rounded-2xl p-6 hover:transform-none">
            <h3 class="text-base font-semibold text-white mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center ring-1 ring-cyan-500/20 mr-3">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                SLA Compliance
            </h3>
            @php
                $slaColors = ['critical' => 'red', 'high' => 'orange', 'medium' => 'yellow', 'low' => 'gray'];
                $slaDots = ['critical' => 'bg-red-400', 'high' => 'bg-orange-400', 'medium' => 'bg-yellow-400', 'low' => 'bg-gray-400'];
            @endphp
            <div class="space-y-4">
                @foreach ($sla as $priority => $data)
                    @php $rateColor = $data['rate'] >= 90 ? 'text-emerald-400' : ($data['rate'] >= 70 ? 'text-yellow-400' : 'text-red-400'); @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition-colors">
                        <div class="flex items-center">
                            <span class="w-2 h-2 rounded-full {{ $slaDots[$priority] ?? 'bg-gray-500' }} mr-3"></span>
                            <div>
                                <span class="text-sm text-gray-300 font-medium capitalize">{{ $priority }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $data['sla_hours'] }}h SLA</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="text-gray-500">{{ $data['breached'] }} breached</span>
                            <span class="font-bold {{ $rateColor }} tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $data['rate'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ Team Stats ═══ --}}
    @if ($teamStats->count() > 0)
        <div class="glass-card rounded-2xl p-6 hover:transform-none mb-8">
            <h3 class="text-base font-semibold text-white mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center ring-1 ring-indigo-500/20 mr-3">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                Team Performance
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($teamStats as $team)
                    <div class="p-5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition-colors border border-white/[0.04]">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-white">{{ $team['name'] }}</h4>
                                <p class="text-[10px] text-gray-500">{{ $team['member_count'] }} members · Lead: {{ $team['leader'] }}</p>
                            </div>
                            <span class="text-2xl font-bold {{ $team['completion_rate'] >= 50 ? 'text-emerald-400' : 'text-yellow-400' }} tabular-nums" style="font-family: 'JetBrains Mono', monospace;">
                                {{ $team['completion_rate'] }}%
                            </span>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center text-xs">
                            <div class="p-2 rounded-lg bg-white/[0.03]">
                                <span class="block font-bold text-white tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $team['total_tasks'] }}</span>
                                <span class="text-gray-500">Total</span>
                            </div>
                            <div class="p-2 rounded-lg bg-emerald-500/5">
                                <span class="block font-bold text-emerald-400 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $team['completed'] }}</span>
                                <span class="text-gray-500">Done</span>
                            </div>
                            <div class="p-2 rounded-lg bg-blue-500/5">
                                <span class="block font-bold text-blue-400 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $team['in_progress'] }}</span>
                                <span class="text-gray-500">Active</span>
                            </div>
                            <div class="p-2 rounded-lg bg-{{ $team['overdue'] > 0 ? 'red' : 'gray' }}-500/5">
                                <span class="block font-bold text-{{ $team['overdue'] > 0 ? 'red' : 'gray' }}-400 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $team['overdue'] }}</span>
                                <span class="text-gray-500">Overdue</span>
                            </div>
                        </div>
                        @if ($team['avg_cycle_hours'] > 0)
                            <p class="mt-3 text-xs text-gray-500">Avg cycle: <span class="text-gray-300 font-medium">{{ $team['avg_cycle_hours'] }}h</span></p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══ Bottleneck Insights ═══ --}}
    @if ($bottlenecks['total_issues'] > 0)
        <div class="glass-card rounded-2xl p-6 hover:transform-none mb-8 border border-amber-500/20">
            <h3 class="text-base font-semibold text-white mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center ring-1 ring-amber-500/20 mr-3">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                Bottleneck Insights
                <span class="ml-auto px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">
                    {{ $bottlenecks['total_issues'] }} issues
                </span>
            </h3>

            <div class="space-y-4">
                {{-- Stuck Tasks --}}
                @if ($bottlenecks['stuck_tasks']->count() > 0)
                    <div>
                        <h4 class="text-xs font-semibold text-amber-400 uppercase tracking-wider mb-2">Stuck Tasks ({{ config('taskhub.stuck_days_threshold') }}+ days in progress)</h4>
                        @foreach ($bottlenecks['stuck_tasks'] as $stuck)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-500/5 mb-2">
                                <div>
                                    <p class="text-sm text-gray-300">{{ $stuck['title'] }}</p>
                                    <p class="text-xs text-gray-500">Assigned to: {{ $stuck['assignee'] }}</p>
                                </div>
                                <span class="text-sm font-bold text-amber-400 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $stuck['days'] }}d</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Blocked Tasks --}}
                @if ($bottlenecks['blocked_tasks']->count() > 0)
                    <div>
                        <h4 class="text-xs font-semibold text-red-400 uppercase tracking-wider mb-2">Blocked Tasks</h4>
                        @foreach ($bottlenecks['blocked_tasks'] as $blocked)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-red-500/5 mb-2">
                                <div>
                                    <p class="text-sm text-gray-300">{{ $blocked['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $blocked['block_reason'] ?? 'No reason provided' }} · {{ $blocked['days_blocked'] }}d blocked</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ Overdue & Due Soon ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Overdue --}}
        <div class="glass-card rounded-2xl p-6 hover:transform-none">
            <h3 class="text-base font-semibold text-white mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center ring-1 ring-red-500/20 mr-3">
                    <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                Overdue Tasks
                @if ($overdue->count() > 0)
                    <span class="ml-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-500/10 text-red-400 ring-1 ring-red-500/20 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $overdue->count() }}</span>
                @endif
            </h3>
            @if ($overdue->count() > 0)
                <div class="space-y-2">
                    @foreach ($overdue->take(5) as $task)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-red-500/5 hover:bg-red-500/10 transition-colors">
                            <div class="min-w-0 mr-3">
                                <p class="text-sm text-gray-300 truncate">{{ $task['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $task['assignee'] }} · {{ ucfirst($task['priority']) }}</p>
                            </div>
                            <span class="text-xs font-bold text-red-400 whitespace-nowrap tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $task['days_overdue'] }}d late</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center py-8 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500">No overdue tasks — great job!</p>
                </div>
            @endif
        </div>

        {{-- Due Soon --}}
        <div class="glass-card rounded-2xl p-6 hover:transform-none">
            <h3 class="text-base font-semibold text-white mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center ring-1 ring-yellow-500/20 mr-3">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                Due Soon (72h)
                @if ($dueSoon->count() > 0)
                    <span class="ml-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-yellow-500/10 text-yellow-400 ring-1 ring-yellow-500/20 tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ $dueSoon->count() }}</span>
                @endif
            </h3>
            @if ($dueSoon->count() > 0)
                <div class="space-y-2">
                    @foreach ($dueSoon->take(5) as $task)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-yellow-500/5 hover:bg-yellow-500/10 transition-colors">
                            <div class="min-w-0 mr-3">
                                <p class="text-sm text-gray-300 truncate">{{ $task['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $task['assignee'] }} · {{ ucfirst($task['priority']) }}</p>
                            </div>
                            <span class="text-xs font-bold text-yellow-400 whitespace-nowrap tabular-nums" style="font-family: 'JetBrains Mono', monospace;">{{ round($task['hours_left']) }}h left</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No tasks due in the next 72 hours.</p>
            @endif
        </div>
    </div>

    {{-- ═══ Weekly Productivity ═══ --}}
    <div class="glass-card rounded-2xl p-6 hover:transform-none mb-8">
        <h3 class="text-base font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center ring-1 ring-emerald-500/20 mr-3">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            Weekly Productivity
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/[0.06]">
                        <th class="text-left py-3 px-3 text-gray-400 font-medium">Week</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Created</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Completed</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Net</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Avg Cycle</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData['weekly_productivity'] as $week)
                        <tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-3 text-gray-300 font-medium">{{ $week['week_label'] }}</td>
                            <td class="py-3 px-3 text-center tabular-nums text-gray-300" style="font-family: 'JetBrains Mono', monospace;">{{ $week['created'] }}</td>
                            <td class="py-3 px-3 text-center tabular-nums text-emerald-400" style="font-family: 'JetBrains Mono', monospace;">{{ $week['completed'] }}</td>
                            <td class="py-3 px-3 text-center tabular-nums {{ $week['net'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}" style="font-family: 'JetBrains Mono', monospace;">
                                {{ $week['net'] >= 0 ? '+' : '' }}{{ $week['net'] }}
                            </td>
                            <td class="py-3 px-3 text-center tabular-nums text-gray-400" style="font-family: 'JetBrains Mono', monospace;">{{ $week['avg_cycle_hours'] }}h</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Priority vs Completion ═══ --}}
    <div class="glass-card rounded-2xl p-6 hover:transform-none">
        <h3 class="text-base font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center ring-1 ring-pink-500/20 mr-3">
                <svg class="w-4 h-4 text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                </svg>
            </div>
            Priority vs Completion
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/[0.06]">
                        <th class="text-left py-3 px-3 text-gray-400 font-medium">Priority</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Total</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Done</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Overdue</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Rate</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">Avg Cycle</th>
                        <th class="text-center py-3 px-3 text-gray-400 font-medium">SLA Target</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData['priority_vs_completion'] as $pvc)
                        @php
                            $badgeClasses = [
                                'gray'   => 'bg-gray-500/10 text-gray-400',
                                'yellow' => 'bg-yellow-500/10 text-yellow-400',
                                'orange' => 'bg-orange-500/10 text-orange-400',
                                'red'    => 'bg-red-500/10 text-red-400',
                            ];
                        @endphp
                        <tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $badgeClasses[$pvc['color']] ?? 'bg-gray-500/10 text-gray-400' }}">
                                    {{ $pvc['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center tabular-nums text-gray-300" style="font-family: 'JetBrains Mono', monospace;">{{ $pvc['total'] }}</td>
                            <td class="py-3 px-3 text-center tabular-nums text-emerald-400" style="font-family: 'JetBrains Mono', monospace;">{{ $pvc['completed'] }}</td>
                            <td class="py-3 px-3 text-center tabular-nums {{ $pvc['overdue'] > 0 ? 'text-red-400' : 'text-gray-500' }}" style="font-family: 'JetBrains Mono', monospace;">{{ $pvc['overdue'] }}</td>
                            <td class="py-3 px-3 text-center tabular-nums font-bold {{ $pvc['completion_rate'] >= 50 ? 'text-emerald-400' : 'text-yellow-400' }}" style="font-family: 'JetBrains Mono', monospace;">{{ $pvc['completion_rate'] }}%</td>
                            <td class="py-3 px-3 text-center tabular-nums text-gray-400" style="font-family: 'JetBrains Mono', monospace;">{{ $pvc['avg_cycle_hours'] }}h</td>
                            <td class="py-3 px-3 text-center tabular-nums text-gray-500" style="font-family: 'JetBrains Mono', monospace;">{{ $pvc['sla_target'] }}h</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
