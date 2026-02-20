<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased" style="font-family: 'Inter', system-ui, sans-serif;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-950 relative overflow-hidden">

            {{-- Background effects --}}
            <div class="fixed inset-0 pointer-events-none">
                {{-- Grid pattern --}}
                <div class="absolute inset-0 opacity-[0.02]"
                     style="background-image: radial-gradient(circle at 1px 1px, rgb(255 255 255) 1px, transparent 0); background-size: 32px 32px;"></div>
                {{-- Gradient blobs --}}
                <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-500/8 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
            </div>

            {{-- Logo --}}
            <div class="relative mb-8">
                <a href="/" class="logo-group flex items-center space-x-3">
                    <x-logo-icon class="w-14 h-14" />
                    <span class="text-3xl tracking-tight">
                        <span class="logo-wordmark logo-wordmark-task">Task</span><span class="logo-wordmark logo-wordmark-hub">Hub</span>
                    </span>
                </a>
            </div>

            {{-- Glass Card --}}
            <div class="relative w-full sm:max-w-md px-6 py-8 sm:px-8 glass-card rounded-2xl shadow-2xl shadow-black/30 hover:transform-none page-enter">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="relative mt-8 text-xs text-gray-600">&copy; {{ date('Y') }} TaskHub. All rights reserved.</p>
        </div>
    </body>
</html>
