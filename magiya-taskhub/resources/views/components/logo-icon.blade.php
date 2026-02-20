{{--
    TaskHub Premium Logo Icon (gear + checkmark + animated orbit).
    Usage: <x-logo-icon class="w-10 h-10" />
    Add 'animate' attribute for the spinning orbit ring: <x-logo-icon class="w-10 h-10" animate />
--}}
@props(['class' => 'w-10 h-10', 'animate' => true])

<svg {{ $attributes->merge(['class' => $class . ' logo-icon']) }} viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="TaskHub icon">
    <defs>
        {{-- Glow filters --}}
        <filter id="glowB" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur stdDeviation="4" result="blur"/>
            <feComposite in="SourceGraphic" in2="blur" operator="over"/>
        </filter>
        <filter id="glowO" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur stdDeviation="3" result="blur"/>
            <feComposite in="SourceGraphic" in2="blur" operator="over"/>
        </filter>
        <filter id="softGlow" x="-60%" y="-60%" width="220%" height="220%">
            <feGaussianBlur stdDeviation="8" result="blur"/>
            <feMerge>
                <feMergeNode in="blur"/>
                <feMergeNode in="blur"/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>

        {{-- Gradient for gear --}}
        <linearGradient id="gearGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%"   stop-color="#2AA8FF"/>
            <stop offset="100%" stop-color="#0B5CFF"/>
        </linearGradient>

        {{-- Gradient for checkmark --}}
        <linearGradient id="checkGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%"   stop-color="#FFB347"/>
            <stop offset="100%" stop-color="#FF7A18"/>
        </linearGradient>

        {{-- Orbit gradient --}}
        <linearGradient id="orbitGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%"   stop-color="#2AA8FF" stop-opacity="0.9"/>
            <stop offset="50%"  stop-color="#0B5CFF" stop-opacity="0.4"/>
            <stop offset="100%" stop-color="#2AA8FF" stop-opacity="0.9"/>
        </linearGradient>
    </defs>

    {{-- Ambient background glow --}}
    <circle cx="256" cy="256" r="140" fill="#2AA8FF" opacity="0.04"/>
    <circle cx="256" cy="256" r="100" fill="#0B5CFF" opacity="0.03"/>

    {{-- Orbit ring (slow spin animation via CSS) --}}
    <g fill="none" stroke-linecap="round" stroke-linejoin="round"
       class="{{ $animate !== false ? 'logo-orbit' : '' }}"
       style="transform-origin: 256px 256px;">

        {{-- Main orbit circle --}}
        <circle cx="256" cy="256" r="205" stroke="url(#orbitGrad)" stroke-width="8" opacity="0.6"/>

        {{-- Orbit arc segments —  give a "dashed ring" look --}}
        <path d="M256 51a205 205 0 0 1 170 90"  stroke="#2AA8FF" stroke-width="10" opacity="0.8"/>
        <path d="M461 256a205 205 0 0 1-68 153" stroke="#0B5CFF" stroke-width="8"  opacity="0.5"/>
        <path d="M256 461a205 205 0 0 1-173-95"  stroke="#2AA8FF" stroke-width="10" opacity="0.8"/>
        <path d="M51 256A205 205 0 0 1 122 109"   stroke="#0B5CFF" stroke-width="8"  opacity="0.5"/>

        {{-- Cardinal nodes with pulse --}}
        <g stroke="#2AA8FF" stroke-width="5" opacity="0.9">
            <circle cx="256" cy="76"  r="7" fill="#2AA8FF" fill-opacity="0.25"/>
            <circle cx="256" cy="76"  r="3" fill="#2AA8FF"/>
            <circle cx="436" cy="256" r="7" fill="#2AA8FF" fill-opacity="0.25"/>
            <circle cx="436" cy="256" r="3" fill="#2AA8FF"/>
            <circle cx="256" cy="436" r="7" fill="#2AA8FF" fill-opacity="0.25"/>
            <circle cx="256" cy="436" r="3" fill="#2AA8FF"/>
            <circle cx="76"  cy="256" r="7" fill="#2AA8FF" fill-opacity="0.25"/>
            <circle cx="76"  cy="256" r="3" fill="#2AA8FF"/>
        </g>
    </g>

    {{-- Gear body --}}
    <g fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="url(#gearGrad)" stroke-width="11" filter="url(#glowB)"
              d="M256 156c12 0 24 2 35 6l14-18 26 15-8 22
                 c9 7 17 15 23 24l23-8 15 26-19 14
                 c3 11 5 22 5 34s-2 24-5 35l19 14-15 26-23-8
                 c-7 9-15 17-24 23l8 23-26 15-14-19
                 c-11 3-23 5-34 5s-24-2-35-5l-14 19-26-15 8-23
                 c-9-7-17-15-23-24l-23 8-15-26 19-14
                 c-3-11-5-23-5-34s2-24 5-35l-19-14 15-26 23 8
                 c7-9 15-17 24-23l-8-23 26-15 14 19c11-4 23-6 34-6z"/>

        {{-- Inner ring --}}
        <circle cx="256" cy="256" r="62" stroke="#0B5CFF" stroke-width="9" opacity="0.85"/>
        <circle cx="256" cy="256" r="62" stroke="#2AA8FF" stroke-width="3" opacity="0.3"/>
    </g>

    {{-- Checkmark + arrow (orange) --}}
    <g fill="none" stroke-linecap="round" stroke-linejoin="round" filter="url(#softGlow)">
        <path stroke="url(#checkGrad)" stroke-width="18" d="M206 270l36 36 86-106"/>
        <path stroke="url(#checkGrad)" stroke-width="16" d="M325 206l54-12-12 54"/>
    </g>

    {{-- Center dot with bright halo --}}
    <circle cx="256" cy="256" r="10" fill="#2AA8FF" opacity="0.15"/>
    <circle cx="256" cy="256" r="5"  fill="#EAF2FF" opacity="0.95"/>
</svg>
