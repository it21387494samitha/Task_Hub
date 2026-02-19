<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                {{-- Total Tasks --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tasks</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_tasks'] }}</div>
                </div>

                {{-- Overdue --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue</div>
                    <div class="mt-2 text-3xl font-bold {{ $stats['overdue_tasks'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $stats['overdue_tasks'] }}
                    </div>
                </div>

                {{-- In Progress --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">In Progress</div>
                    <div class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['tasks_by_status']['in_progress'] ?? 0 }}</div>
                </div>

                {{-- Done --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</div>
                    <div class="mt-2 text-3xl font-bold text-green-600">{{ $stats['tasks_by_status']['done'] ?? 0 }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Tasks by Status --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tasks by Status</h3>
                    <div class="space-y-3">
                        @php
                            $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done', 'blocked' => 'Blocked'];
                            $statusBarColors = ['todo' => 'bg-gray-500', 'in_progress' => 'bg-blue-500', 'done' => 'bg-green-500', 'blocked' => 'bg-red-500'];
                            $total = max($stats['total_tasks'], 1);
                        @endphp
                        @foreach ($stats['tasks_by_status'] as $status => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $statusLabels[$status] ?? $status }}</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="{{ $statusBarColors[$status] ?? 'bg-gray-500' }} h-2 rounded-full transition-all duration-500"
                                         style="width: {{ ($count / $total) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tasks per User --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tasks per Developer</h3>
                    @if (count($stats['tasks_per_user']) > 0)
                        <div class="space-y-3">
                            @foreach ($stats['tasks_per_user'] as $userStat)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $userStat['name'] }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $userStat['task_count'] }} tasks
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No tasks assigned yet.</p>
                    @endif
                </div>
            </div>

            {{-- Recent Activity Feed --}}
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Activity</h3>

                @if ($recentActivity->count() > 0)
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach ($recentActivity as $index => $log)
                                <li>
                                    <div class="relative pb-8">
                                        {{-- Connector line (not on last item) --}}
                                        @if (! $loop->last)
                                            <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                        @endif

                                        <div class="relative flex space-x-3">
                                            {{-- Action icon --}}
                                            <div>
                                                @php
                                                    $iconColors = [
                                                        'created'  => 'bg-green-500',
                                                        'updated'  => 'bg-blue-500',
                                                        'deleted'  => 'bg-red-500',
                                                        'assigned' => 'bg-yellow-500',
                                                    ];
                                                    $iconColor = $iconColors[$log->action] ?? 'bg-gray-500';
                                                @endphp
                                                <span class="h-8 w-8 rounded-full {{ $iconColor }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                    @switch($log->action)
                                                        @case('created')
                                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            @break
                                                        @case('updated')
                                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                            @break
                                                        @case('deleted')
                                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            @break
                                                        @case('assigned')
                                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                            @break
                                                        @default
                                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @endswitch
                                                </span>
                                            </div>

                                            {{-- Description + timestamp --}}
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $log->description }}</p>
                                                    @if ($log->changes)
                                                        <div class="mt-1 flex flex-wrap gap-1">
                                                            @foreach ($log->changes as $field => $change)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                                    {{ $field }}: {{ $change['old'] ?? '—' }} → {{ $change['new'] ?? '—' }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">No activity yet. Create or update a task to see the audit trail here.</p>
                @endif
            </div>
        </div>
    </div>
</div>
