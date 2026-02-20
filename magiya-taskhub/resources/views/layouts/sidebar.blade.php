{{--
    Premium Sidebar — Glassmorphism, gradient active states, animated nav.
--}}

{{-- Desktop Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-40 transition-all duration-300 hidden lg:flex lg:flex-col
              bg-gray-950/80 backdrop-blur-2xl border-r border-white/[0.06]"
       :class="sidebarOpen ? 'w-64' : 'w-20'">

    {{-- Sidebar gradient glow (top-left) --}}
    <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Logo & Toggle --}}
    <div class="relative flex items-center h-16 px-4 border-b border-white/[0.06]">
        <a href="{{ route('dashboard') }}" class="logo-group flex items-center space-x-3" :class="!sidebarOpen && 'justify-center w-full'">
            <x-logo-icon class="flex-shrink-0 w-10 h-10" />
            <span class="text-xl tracking-tight" x-show="sidebarOpen" x-transition>
                <span class="logo-wordmark logo-wordmark-task">Task</span><span class="logo-wordmark logo-wordmark-hub">Hub</span>
            </span>
        </a>
    </div>

    {{-- User Profile Card --}}
    <div class="px-3 py-4 border-b border-white/[0.06]" x-show="sidebarOpen" x-transition>
        <div class="flex items-center space-x-3 p-3 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition-colors">
            <div class="relative flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-indigo-500/20">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-gray-950 pulse-dot"></div>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                @php
                    $roleLabels = ['admin' => 'Admin', 'team_leader' => 'Team Leader', 'developer' => 'Developer'];
                    $roleColors = ['admin' => 'text-purple-400', 'team_leader' => 'text-blue-400', 'developer' => 'text-emerald-400'];
                    $roleBadgeBg = ['admin' => 'bg-purple-500/10', 'team_leader' => 'bg-blue-500/10', 'developer' => 'bg-emerald-500/10'];
                    $roleVal = Auth::user()->role?->value ?? 'developer';
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider {{ $roleColors[$roleVal] ?? 'text-gray-400' }} {{ $roleBadgeBg[$roleVal] ?? 'bg-gray-500/10' }}">
                    {{ $roleLabels[$roleVal] ?? $roleVal }}
                </span>
            </div>
        </div>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @php
            $navItems = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'active' => request()->routeIs('dashboard')],
                ['route' => 'tasks.index', 'label' => 'Tasks', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'active' => request()->routeIs('tasks.*')],
                ['route' => 'notifications.settings', 'label' => 'Notifications', 'icon' => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0', 'active' => request()->routeIs('notifications.*')],
                ['route' => 'profile.edit', 'label' => 'Profile', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'active' => request()->routeIs('profile.*')],
            ];
        @endphp

        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="sidebar-nav-link flex items-center px-3 py-2.5 rounded-xl text-sm font-medium
                      {{ $item['active']
                          ? 'active bg-gradient-to-r from-indigo-600/80 to-indigo-500/40 text-white shadow-lg shadow-indigo-500/10 border border-indigo-500/20'
                          : 'text-gray-400 hover:text-white hover:bg-white/[0.05]' }}"
               :class="!sidebarOpen && 'justify-center'"
               title="{{ $item['label'] }}">
                <svg class="w-5 h-5 flex-shrink-0 {{ $item['active'] ? 'text-indigo-200' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="ml-3" x-show="sidebarOpen" x-transition>{{ $item['label'] }}</span>

                {{-- Badge for Tasks (show count) --}}
                @if ($item['route'] === 'tasks.index')
                    @php
                        $taskCount = Auth::user()->isDeveloper()
                            ? Auth::user()->assignedTasks()->count()
                            : \App\Models\Task::count();
                    @endphp
                    <span class="ml-auto inline-flex items-center justify-center min-w-[1.5rem] px-1.5 py-0.5 text-[10px] font-bold rounded-full
                                 {{ $item['active'] ? 'bg-white/20 text-white' : 'bg-indigo-500/10 text-indigo-400' }}"
                          x-show="sidebarOpen" x-transition>
                        {{ $taskCount }}
                    </span>
                @endif
            </a>
        @endforeach

        {{-- ═══ Admin Section ═══ --}}
        @if (!Auth::user()->isDeveloper())
            <div class="pt-4 mt-4 border-t border-white/[0.06]">
                <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-600"
                   x-show="sidebarOpen" x-transition>Admin</p>

                @php
                    $adminItems = [
                        ['route' => 'admin.dashboard', 'label' => 'Analytics', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'active' => request()->routeIs('admin.dashboard')],
                        ['route' => 'admin.users', 'label' => 'Users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'active' => request()->routeIs('admin.users')],
                        ['route' => 'admin.teams', 'label' => 'Teams', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z', 'active' => request()->routeIs('admin.teams')],
                    ];
                @endphp

                @foreach ($adminItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-nav-link flex items-center px-3 py-2.5 rounded-xl text-sm font-medium
                              {{ $item['active']
                                  ? 'active bg-gradient-to-r from-purple-600/80 to-purple-500/40 text-white shadow-lg shadow-purple-500/10 border border-purple-500/20'
                                  : 'text-gray-400 hover:text-white hover:bg-white/[0.05]' }}"
                       :class="!sidebarOpen && 'justify-center'"
                       title="{{ $item['label'] }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ $item['active'] ? 'text-purple-200' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                        <span class="ml-3" x-show="sidebarOpen" x-transition>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </nav>

    {{-- Sidebar Footer --}}
    <div class="px-3 py-4 border-t border-white/[0.06]">
        <button @click="sidebarOpen = !sidebarOpen"
                class="flex items-center w-full px-3 py-2 rounded-xl text-sm text-gray-500 hover:text-white hover:bg-white/[0.05] transition-all"
                :class="!sidebarOpen && 'justify-center'">
            <svg class="w-5 h-5 transition-transform duration-300" :class="!sidebarOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <span class="ml-3" x-show="sidebarOpen" x-transition>Collapse</span>
        </button>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit"
                    class="flex items-center w-full px-3 py-2 rounded-xl text-sm text-gray-500 hover:text-red-400 hover:bg-red-500/5 transition-all"
                    :class="!sidebarOpen && 'justify-center'"
                    title="Log Out">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="ml-3" x-show="sidebarOpen" x-transition>Log Out</span>
            </button>
        </form>
    </div>
</aside>

{{-- Mobile Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-950/95 backdrop-blur-2xl border-r border-white/[0.06] transform transition-transform duration-300 lg:hidden"
       :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full'">

    {{-- Close button --}}
    <div class="flex items-center justify-between h-16 px-4 border-b border-white/[0.06]">
        <a href="{{ route('dashboard') }}" class="logo-group flex items-center space-x-3">
            <x-logo-icon class="w-10 h-10" />
            <span class="text-xl tracking-tight">
                <span class="logo-wordmark logo-wordmark-task">Task</span><span class="logo-wordmark logo-wordmark-hub">Hub</span>
            </span>
        </a>
        <button @click="mobileSidebar = false" class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-white/[0.05] transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Mobile Nav Links --}}
    <nav class="px-3 py-4 space-y-1">
        @foreach ($navItems as $item)
            <a href="{{ route($item['route']) }}" @click="mobileSidebar = false"
               class="sidebar-nav-link flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition
                      {{ $item['active']
                          ? 'active bg-gradient-to-r from-indigo-600/80 to-indigo-500/40 text-white'
                          : 'text-gray-400 hover:text-white hover:bg-white/[0.05]' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="ml-3">{{ $item['label'] }}</span>
            </a>
        @endforeach

        {{-- Mobile Admin Section --}}
        @if (!Auth::user()->isDeveloper())
            <div class="pt-4 mt-4 border-t border-white/[0.06]">
                <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-600">Admin</p>
                @foreach ($adminItems as $item)
                    <a href="{{ route($item['route']) }}" @click="mobileSidebar = false"
                       class="sidebar-nav-link flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition
                              {{ $item['active']
                                  ? 'active bg-gradient-to-r from-purple-600/80 to-purple-500/40 text-white'
                                  : 'text-gray-400 hover:text-white hover:bg-white/[0.05]' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                        <span class="ml-3">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </nav>
</aside>
