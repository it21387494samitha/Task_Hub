<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts — Inter for body, JetBrains Mono for code/numbers -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: true, mobileSidebar: false }"
          style="font-family: 'Inter', system-ui, sans-serif;">
        <div class="min-h-screen bg-gray-950 flex relative">

            {{-- Subtle grid / dot pattern overlay --}}
            <div class="fixed inset-0 pointer-events-none opacity-[0.02]"
                 style="background-image: radial-gradient(circle at 1px 1px, rgb(255 255 255) 1px, transparent 0); background-size: 32px 32px;"></div>

            {{-- ── Sidebar ─────────────────────────────────────── --}}
            @include('layouts.sidebar')

            {{-- ── Main Content ────────────────────────────────── --}}
            <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
                 :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

                {{-- Top Bar --}}
                @include('layouts.topbar')

                {{-- Page Heading --}}
                @isset($header)
                    <header class="glass border-b border-white/5">
                        <div class="px-4 sm:px-6 lg:px-8 py-5">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                {{-- Page Content --}}
                <main class="flex-1 p-4 sm:p-6 lg:p-8 page-enter">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Mobile sidebar overlay --}}
        <div x-show="mobileSidebar" x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="mobileSidebar = false"
             class="fixed inset-0 bg-black/70 backdrop-blur-sm z-30 lg:hidden" style="display: none;"></div>

        {{-- Toast Container --}}
        <div x-data="{ toasts: [] }"
             @toast.window="
                let t = { id: Date.now(), message: $event.detail.message, type: $event.detail.type || 'success' };
                toasts.push(t);
                setTimeout(() => toasts = toasts.filter(x => x.id !== t.id), 4000);
             "
             class="fixed top-4 right-4 z-50 flex flex-col gap-3 pointer-events-none">
            <template x-for="toast in toasts" :key="toast.id">
                <div class="toast-enter pointer-events-auto glass rounded-xl px-5 py-3 flex items-center space-x-3 shadow-2xl"
                     :class="{
                        'border-l-4 border-emerald-500': toast.type === 'success',
                        'border-l-4 border-red-500': toast.type === 'error',
                        'border-l-4 border-blue-500': toast.type === 'info',
                     }">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <span class="text-sm font-medium text-gray-200" x-text="toast.message"></span>
                </div>
            </template>
        </div>

        {{-- ── DEBUG: Livewire & Fetch Error Logger ────────────── --}}
        <script>
            // 1. Intercept ALL fetch requests — log every failed HTTP response
            const _origFetch = window.fetch;
            window.fetch = async function(...args) {
                const response = await _origFetch.apply(this, args);
                if (!response.ok) {
                    const clone = response.clone();
                    clone.text().then(body => {
                        console.error(
                            '%c[TASKHUB FETCH ERROR]', 'color:red;font-weight:bold',
                            '\nURL    :', args[0],
                            '\nStatus :', response.status, response.statusText,
                            '\nBody   :', body
                        );
                    });
                }
                return response;
            };

            // 2. Livewire v4 commit hook — catches server-side failures (500, 422, 403 etc.)
            document.addEventListener('livewire:init', () => {
                Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    fail(({ status, preventDefault }) => {
                        console.error(
                            '%c[LIVEWIRE COMMIT FAILED]', 'color:red;font-weight:bold',
                            '\nComponent:', component.name,
                            '\nHTTP Status:', status
                        );
                        // Show a visible toast so you can see it in the UI too
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: 'Server error (HTTP ' + status + ') — check console for details.', type: 'error' }
                        }));
                    });
                });

                // 3. Log every Livewire request+response for full visibility
                Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    succeed(({ snapshot, effect }) => {
                        if (effect && effect.dispatches && effect.dispatches.length) {
                            console.log(
                                '%c[LIVEWIRE OK]', 'color:green;font-weight:bold',
                                component.name, '→ dispatched:', effect.dispatches
                            );
                        }
                    });
                });
            });

            // 4. Catch any unhandled JS errors or promise rejections
            window.addEventListener('unhandledrejection', e => {
                console.error('%c[UNHANDLED PROMISE]', 'color:orange;font-weight:bold', e.reason);
            });
            window.onerror = (msg, src, line, col, err) => {
                console.error('%c[JS ERROR]', 'color:red;font-weight:bold', msg, '\nat', src + ':' + line);
            };
        </script>
    </body>
</html>
