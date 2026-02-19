<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tasks
            </h2>
            @can('create', App\Models\Task::class)
                <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                    + New Task
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if ($successMessage)
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg">
                    {{ $successMessage }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Search --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tasks..."
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select wire:model.live="statusFilter"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Priority Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                        <select wire:model.live="priorityFilter"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Priorities</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Task Table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assigned To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($tasks as $task)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</div>
                                        @if ($task->description)
                                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $task->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = ['gray' => 'bg-gray-100 text-gray-800', 'blue' => 'bg-blue-100 text-blue-800', 'green' => 'bg-green-100 text-green-800', 'red' => 'bg-red-100 text-red-800'];
                                            $color = $statusColors[$task->status->color()] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                            {{ $task->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $priorityColors = ['gray' => 'text-gray-500', 'yellow' => 'text-yellow-600', 'orange' => 'text-orange-600', 'red' => 'text-red-600'];
                                            $pColor = $priorityColors[$task->priority->color()] ?? 'text-gray-500';
                                        @endphp
                                        <span class="text-sm font-medium {{ $pColor }}">{{ $task->priority->label() }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $task->assignee?->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $task->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $task->due_date?->format('M d, Y') ?? '—' }}
                                        @if ($task->isOverdue())
                                            <span class="text-xs">(overdue)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Edit</a>
                                        @endcan
                                        @can('delete', $task)
                                            <button wire:click="deleteTask({{ $task->id }})"
                                                wire:confirm="Are you sure you want to delete '{{ $task->title }}'?"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400">
                                                Delete
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No tasks found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
