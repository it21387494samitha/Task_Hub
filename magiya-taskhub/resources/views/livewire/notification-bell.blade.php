{{-- Notification Bell — polls every 30s for new notifications --}}
<div class="relative" wire:poll.30s>

    {{-- Bell button --}}
    <button wire:click="toggleDropdown" class="relative inline-flex items-center p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition">
        {{-- Bell icon --}}
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        {{-- Unread badge --}}
        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown panel --}}
    @if ($showDropdown)
        <div class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">

            {{-- Header --}}
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        Mark all read
                    </button>
                @endif
            </div>

            {{-- Notification list --}}
            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($notifications as $notification)
                    <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <div class="flex items-start space-x-3">
                            {{-- Icon based on notification type --}}
                            <div class="flex-shrink-0 mt-0.5">
                                @php
                                    $type = $notification->data['type'] ?? 'default';
                                    $iconConfig = match($type) {
                                        'task_assigned' => ['bg-yellow-100 dark:bg-yellow-900', 'text-yellow-600 dark:text-yellow-400', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                        'task_updated'  => ['bg-blue-100 dark:bg-blue-900', 'text-blue-600 dark:text-blue-400', 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                        'task_deleted'  => ['bg-red-100 dark:bg-red-900', 'text-red-600 dark:text-red-400', 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                                        default         => ['bg-gray-100 dark:bg-gray-700', 'text-gray-600 dark:text-gray-400', 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    };
                                @endphp
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $iconConfig[0] }}">
                                    <svg class="h-4 w-4 {{ $iconConfig[1] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconConfig[2] }}" />
                                    </svg>
                                </span>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800 dark:text-gray-200">
                                    {{ $notification->data['message'] ?? 'New notification' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Mark as read --}}
                            <button wire:click="markAsRead('{{ $notification->id }}')" class="flex-shrink-0 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400" title="Mark as read">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You're all caught up!</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
