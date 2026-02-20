<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'TaskHub') }} — Enterprise Task Management</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

        <!-- Vanta.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.birds.min.js"></script>

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            html { height: 100%; }

            body {
                font-family: 'Inter', system-ui, sans-serif;
                background: #030712;
                color: #f9fafb;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 1.5rem 2rem;
                overflow-x: hidden;
            }

            #vanta-bg { position: fixed; inset: 0; z-index: 0; }

            .dot-grid {
                position: fixed; inset: 0; z-index: 1;
                pointer-events: none; opacity: 0.025;
                background-image: radial-gradient(circle at 1px 1px, rgb(255 255 255) 1px, transparent 0);
                background-size: 32px 32px;
            }

            .page-content {
                position: relative; z-index: 2;
                width: 100%; max-width: 960px;
                display: flex; flex-direction: column; align-items: center;
                gap: 2rem;
            }

            /* ── Nav ── */
            .top-nav {
                width: 100%; display: flex; justify-content: flex-end;
                align-items: center; gap: 0.75rem;
            }
            .nav-link {
                display: inline-block; padding: 0.5rem 1.2rem; border-radius: 10px;
                font-size: 0.8125rem; font-weight: 500; color: #d1d5db;
                text-decoration: none; border: 1px solid transparent;
                transition: all 0.2s ease; letter-spacing: 0.01em;
            }
            .nav-link:hover {
                color: #f9fafb; border-color: rgba(255,255,255,0.15);
                background: rgba(255,255,255,0.06);
            }
            .nav-link-primary {
                background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.15));
                border-color: rgba(99,102,241,0.4); color: #a5b4fc;
            }
            .nav-link-primary:hover {
                background: linear-gradient(135deg, rgba(99,102,241,0.3), rgba(139,92,246,0.25));
                border-color: rgba(99,102,241,0.7); color: #c7d2fe;
            }

            /* ── Hero card ── */
            .hero-card {
                width: 100%; border-radius: 20px; overflow: hidden;
                border: 1px solid rgba(255,255,255,0.07);
                box-shadow: 0 0 0 1px rgba(255,255,255,0.03), 0 25px 60px rgba(0,0,0,0.6), 0 0 120px rgba(99,102,241,0.06);
                backdrop-filter: blur(16px);
                background: rgba(255,255,255,0.02);
                animation: cardIn 0.75s cubic-bezier(0.16, 1, 0.3, 1) both;
                display: flex; flex-direction: column;
            }
            @media (min-width: 64rem) { .hero-card { flex-direction: row; } }

            @keyframes cardIn {
                from { opacity: 0; transform: translateY(28px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            /* ── Left pane ── */
            .hero-body {
                flex: 1; padding: 3rem 2.5rem;
                background: rgba(3, 7, 18, 0.75);
                display: flex; flex-direction: column; justify-content: center;
            }
            @media (min-width: 64rem) { .hero-body { padding: 3.5rem 3rem; } }

            .eyebrow {
                display: inline-flex; align-items: center; gap: 0.45rem;
                font-size: 0.7rem; font-weight: 700; letter-spacing: 0.14em;
                text-transform: uppercase; color: #818cf8; margin-bottom: 1.2rem;
            }
            .eyebrow-dot {
                width: 7px; height: 7px; border-radius: 50%; background: #818cf8;
                box-shadow: 0 0 8px #818cf8; animation: pulse-dot 2s ease-in-out infinite;
            }
            @keyframes pulse-dot { 0%,100% { opacity: 1; } 50% { opacity: 0.35; } }

            .hero-title {
                font-size: 2rem; font-weight: 800; line-height: 1.15;
                letter-spacing: -0.03em; margin-bottom: 0.6rem;
            }
            .hero-title .grad {
                background: linear-gradient(135deg, #818cf8 0%, #a78bfa 50%, #c084fc 100%);
                -webkit-background-clip: text; -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            @media (min-width: 64rem) { .hero-title { font-size: 2.5rem; } }

            .hero-subtitle {
                font-size: 0.9rem; color: #6b7280; line-height: 1.7; margin-bottom: 2.2rem; max-width: 420px;
            }

            /* ── Feature pills ── */
            .features {
                display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 2.2rem;
            }
            .pill {
                display: inline-flex; align-items: center; gap: 0.4rem;
                padding: 0.4rem 0.9rem; border-radius: 9999px;
                font-size: 0.72rem; font-weight: 600;
                background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
                color: #9ca3af; transition: all 0.2s;
            }
            .pill:hover { background: rgba(255,255,255,0.07); color: #d1d5db; }
            .pill svg { width: 14px; height: 14px; opacity: 0.7; }
            .pill-indigo  { color: #a5b4fc; border-color: rgba(99,102,241,0.25); background: rgba(99,102,241,0.08); }
            .pill-emerald { color: #6ee7b7; border-color: rgba(16,185,129,0.25); background: rgba(16,185,129,0.08); }
            .pill-amber   { color: #fcd34d; border-color: rgba(245,158,11,0.25); background: rgba(245,158,11,0.08); }
            .pill-rose    { color: #fda4af; border-color: rgba(244,63,94,0.25);  background: rgba(244,63,94,0.08);  }
            .pill-blue    { color: #93c5fd; border-color: rgba(59,130,246,0.25); background: rgba(59,130,246,0.08); }
            .pill-purple  { color: #c4b5fd; border-color: rgba(139,92,246,0.25); background: rgba(139,92,246,0.08); }

            /* ── CTA buttons ── */
            .cta-row { display: flex; gap: 0.75rem; flex-wrap: wrap; }
            .btn-primary {
                display: inline-flex; align-items: center; gap: 0.5rem;
                padding: 0.75rem 1.8rem; border-radius: 12px;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                color: #fff; font-size: 0.875rem; font-weight: 600;
                text-decoration: none; border: none; cursor: pointer;
                transition: all 0.25s ease;
                box-shadow: 0 4px 24px rgba(99,102,241,0.35), 0 0 0 1px rgba(99,102,241,0.3);
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 32px rgba(99,102,241,0.5), 0 0 0 1px rgba(99,102,241,0.5);
            }
            .btn-secondary {
                display: inline-flex; align-items: center; gap: 0.5rem;
                padding: 0.75rem 1.8rem; border-radius: 12px;
                background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
                color: #d1d5db; font-size: 0.875rem; font-weight: 600;
                text-decoration: none; cursor: pointer; transition: all 0.25s ease;
            }
            .btn-secondary:hover {
                background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2);
                transform: translateY(-1px); color: #f9fafb;
            }

            /* ── Right visual pane ── */
            .hero-visual {
                position: relative; overflow: hidden;
                background: rgba(3, 7, 18, 0.5); flex-shrink: 0;
                display: flex; align-items: center; justify-content: center;
                padding: 3rem 2rem;
            }
            @media (min-width: 64rem) { .hero-visual { width: 380px; padding: 2.5rem; } }

            .hero-visual::before {
                content: ''; position: absolute; inset: 0;
                background: radial-gradient(ellipse at 50% 50%, rgba(99,102,241,0.08) 0%, transparent 70%);
                pointer-events: none;
            }

            .logo-wrap {
                display: flex; flex-direction: column; align-items: center; gap: 1.5rem;
                position: relative; z-index: 1;
            }
            .logo-wrap svg { width: 120px; height: 120px; filter: drop-shadow(0 0 40px rgba(99,102,241,0.25)); }

            .brand-text { text-align: center; }
            .brand-text h2 {
                font-size: 2.4rem; font-weight: 800; letter-spacing: -0.03em;
            }
            .brand-word-task {
                background: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%);
                -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            }
            .brand-word-hub {
                background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
                -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            }
            .brand-sub {
                font-size: 0.7rem; font-weight: 600; letter-spacing: 0.2em;
                text-transform: uppercase; color: #4b5563; margin-top: 0.25rem;
            }

            /* ── Stats strip ── */
            .stats {
                display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px;
                border-radius: 14px; overflow: hidden;
                border: 1px solid rgba(255,255,255,0.06);
                background: rgba(255,255,255,0.04);
                animation: cardIn 0.75s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
                width: 100%;
            }
            .stat {
                padding: 1.2rem 1.5rem; text-align: center;
                background: rgba(3,7,18,0.6);
                transition: background 0.2s;
            }
            .stat:hover { background: rgba(255,255,255,0.03); }
            .stat-value {
                font-family: 'JetBrains Mono', monospace;
                font-size: 1.5rem; font-weight: 700;
                background: linear-gradient(135deg, #818cf8, #c084fc);
                -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            }
            .stat-label { font-size: 0.7rem; color: #6b7280; margin-top: 0.2rem; letter-spacing: 0.05em; }

            /* ── Footer ── */
            .footer {
                font-size: 0.7rem; color: #374151; letter-spacing: 0.02em;
                animation: cardIn 0.75s 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
            }
            .footer span { color: #6366f1; font-weight: 600; }

            /* ── Orbit animation (for logo) ── */
            @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
            .logo-orbit { animation: spin 20s linear infinite; transform-origin: 256px 256px; }
        </style>
    </head>
    <body>
        <div id="vanta-bg"></div>
        <div class="dot-grid"></div>

        <div class="page-content">

            {{-- Top nav --}}
            @if (Route::has('login'))
                <nav class="top-nav">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-link nav-link-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-2px;margin-right:4px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link nav-link-primary">Get Started</a>
                        @endif
                    @endauth
                </nav>
            @endif

            {{-- Hero card --}}
            <div class="hero-card">

                {{-- Left content --}}
                <div class="hero-body">
                    <span class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        Magiya Dev TaskHub
                    </span>

                    <h1 class="hero-title">
                        Ship faster with<br><span class="grad">enterprise-grade</span><br>task management.
                    </h1>

                    <p class="hero-subtitle">
                        Kanban boards, real-time analytics, team workload tracking, and SLA compliance — everything your dev team needs in one glassmorphic dashboard.
                    </p>

                    <div class="features">
                        <span class="pill pill-indigo">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" /></svg>
                            Kanban Board
                        </span>
                        <span class="pill pill-emerald">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            Analytics
                        </span>
                        <span class="pill pill-amber">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Teams
                        </span>
                        <span class="pill pill-rose">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            SLA Tracking
                        </span>
                        <span class="pill pill-blue">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            Notifications
                        </span>
                        <span class="pill pill-purple">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
                            Comments
                        </span>
                    </div>

                    <div class="cta-row">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary">
                                Open Dashboard
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-primary">
                                Get Started Free
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </a>
                            <a href="{{ route('login') }}" class="btn-secondary">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Right: Logo visual --}}
                <div class="hero-visual">
                    <div class="logo-wrap">
                        <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <filter id="glowB" x="-50%" y="-50%" width="200%" height="200%">
                                    <feGaussianBlur stdDeviation="4" result="blur"/>
                                    <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                                </filter>
                                <linearGradient id="gearGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#818cf8"/>
                                    <stop offset="100%" stop-color="#6366f1"/>
                                </linearGradient>
                                <linearGradient id="checkGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#FFB347"/>
                                    <stop offset="100%" stop-color="#FF7A18"/>
                                </linearGradient>
                                <linearGradient id="orbitGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#818cf8" stop-opacity="0.9"/>
                                    <stop offset="50%" stop-color="#6366f1" stop-opacity="0.4"/>
                                    <stop offset="100%" stop-color="#818cf8" stop-opacity="0.9"/>
                                </linearGradient>
                            </defs>
                            <circle cx="256" cy="256" r="140" fill="#818cf8" opacity="0.04"/>
                            <circle cx="256" cy="256" r="100" fill="#6366f1" opacity="0.03"/>
                            <g fill="none" stroke-linecap="round" stroke-linejoin="round" class="logo-orbit" style="transform-origin:256px 256px;">
                                <circle cx="256" cy="256" r="205" stroke="url(#orbitGrad)" stroke-width="6" opacity="0.5"/>
                                <circle cx="256" cy="256" r="205" stroke="url(#orbitGrad)" stroke-width="6" stroke-dasharray="40 80" opacity="0.8"/>
                                <circle cx="256" cy="51" r="8" fill="#818cf8" filter="url(#glowB)" opacity="0.9"/>
                            </g>
                            <!-- Gear -->
                            <path d="M281 166l6-26a4 4 0 00-3-5l-20-5a4 4 0 01-3-3c-2-6-5-12-9-17a4 4 0 010-4l12-18a4 4 0 00-1-5l-18-13a4 4 0 00-5 0l-16 14a4 4 0 01-4 1c-6-2-12-3-18-3a4 4 0 01-3-3l-5-20a4 4 0 00-5-3l-22 2a4 4 0 00-3 4l2 21a4 4 0 01-2 4c-5 4-10 8-14 13a4 4 0 01-4 1l-19-8a4 4 0 00-5 2l-11 20a4 4 0 001 5l17 12a4 4 0 011 4c-1 6-2 13-1 19a4 4 0 01-2 4l-19 10a4 4 0 00-1 5l10 20a4 4 0 005 2l20-6a4 4 0 014 1c4 5 8 9 13 12a4 4 0 012 4l-3 21a4 4 0 003 4l21 6a4 4 0 005-2l8-19a4 4 0 014-2c6 0 13 0 19-2a4 4 0 014 2l11 18a4 4 0 005 1l19-12a4 4 0 001-5l-9-19a4 4 0 011-4c4-5 8-10 11-16a4 4 0 013-2l21 1a4 4 0 004-4l2-22a4 4 0 00-3-4z"
                                  fill="url(#gearGrad)" opacity="0.3" transform="translate(72,80) scale(1.3)" filter="url(#glowB)"/>
                            <!-- Checkmark -->
                            <path d="M200 265l35 35 77-85" stroke="url(#checkGrad)" stroke-width="28" stroke-linecap="round" stroke-linejoin="round" fill="none" filter="url(#glowB)"/>
                        </svg>

                        <div class="brand-text">
                            <h2><span class="brand-word-task">Task</span><span class="brand-word-hub">Hub</span></h2>
                            <p class="brand-sub">Magiya Dev Studio</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats strip --}}
            <div class="stats">
                <div class="stat">
                    <div class="stat-value">4</div>
                    <div class="stat-label">Priority Levels</div>
                </div>
                <div class="stat">
                    <div class="stat-value">&infin;</div>
                    <div class="stat-label">Tasks & Comments</div>
                </div>
                <div class="stat">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Role Types</div>
                </div>
            </div>

            <p class="footer">Built with <span>Laravel 12</span> &middot; Livewire &middot; Alpine.js &middot; Tailwind CSS</p>

        </div>

        <script>
            VANTA.BIRDS({
                el: '#vanta-bg',
                mouseControls: true,
                touchControls: true,
                gyroControls: false,
                minHeight: 200.00,
                minWidth: 200.00,
                scale: 1.00,
                scaleMobile: 1.00,
                backgroundColor: 0x030712,
                color1: 0x6366f1,
                color2: 0x8b5cf6,
                colorMode: 'variance',
                birdSize: 1.2,
                wingSpan: 25.00,
                speedLimit: 4.00,
                separation: 30.00,
                alignment: 30.00,
                cohesion: 30.00,
                quantity: 3
            });
        </script>
    </body>
</html>
