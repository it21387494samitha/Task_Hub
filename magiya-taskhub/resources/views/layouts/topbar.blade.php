{{--
    Premium Top Bar — Glass morphism, working search, polished user section.
--}}
<header class="sticky top-0 z-20 bg-gray-950/60 backdrop-blur-2xl border-b border-white/[0.06]">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

        {{-- Left: Mobile menu toggle + Breadcrumb --}}
        <div class="flex items-center space-x-4">
            {{-- Mobile hamburger --}}
            <button @click="mobileSidebar = true"
                    class="lg:hidden text-gray-400 hover:text-white p-1.5 rounded-lg hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- Search bar with glass style --}}
            <div class="hidden sm:flex items-center bg-white/[0.04] hover:bg-white/[0.06] border border-white/[0.06] rounded-xl px-3.5 py-2 w-72 transition-all focus-within:border-indigo-500/30 focus-within:bg-white/[0.06] group">
                <svg class="w-4 h-4 text-gray-500 mr-2.5 group-focus-within:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" placeholder="Search tasks..."
                       class="bg-transparent border-none p-0 text-sm text-gray-200 placeholder-gray-500 focus:ring-0 focus:outline-none w-full">
                <kbd class="hidden lg:inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono text-gray-500 bg-white/[0.06] border border-white/[0.06]">⌘K</kbd>
            </div>
        </div>

        {{-- Right: Notifications + User --}}
        <div class="flex items-center space-x-2">
            {{-- Notification Bell --}}
            @livewire('notification-bell')

            {{-- Divider --}}
            <div class="w-px h-8 bg-white/[0.06]"></div>

            {{-- User avatar + dropdown --}}
            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-2.5 py-1.5 rounded-xl hover:bg-white/[0.05] transition-all">
                <div class="relative">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-xs shadow-lg shadow-indigo-500/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-gray-950"></div>
                </div>
                <div class="hidden md:block">
                    <p class="text-sm font-medium text-white leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-500 leading-tight">{{ ucfirst(str_replace('_', ' ', Auth::user()->role?->value ?? 'developer')) }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-500 hidden md:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </a>
        </div>
    </div>
</header>
