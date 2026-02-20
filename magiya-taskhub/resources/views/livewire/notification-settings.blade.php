{{--
    Notification Settings — Premium Glassmorphism design.
    Toggle email & in-app notifications per event type.
--}}
<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Notification Settings</h2>
                <p class="mt-0.5 text-sm text-gray-400">Choose which notifications you receive</p>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="enableAll"
                        class="inline-flex items-center px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl text-sm font-semibold transition-all border border-emerald-500/20">
                    Enable All
                </button>
                <button wire:click="disableAll"
                        class="inline-flex items-center px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-xl text-sm font-semibold transition-all border border-red-500/20">
                    Disable All
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Main card --}}
        <div class="glass-card rounded-2xl border border-white/[0.06] overflow-hidden">
            {{-- Card header --}}
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h3 class="text-lg font-semibold text-white">Event Preferences</h3>
                <p class="text-sm text-gray-400">Toggle individual channels for each event type</p>
            </div>

            {{-- Table header --}}
            <div class="grid grid-cols-12 gap-4 px-6 py-3 bg-white/[0.02] border-b border-white/[0.06] text-xs font-semibold uppercase tracking-wider text-gray-500">
                <div class="col-span-6">Event</div>
                <div class="col-span-3 text-center">In-App</div>
                <div class="col-span-3 text-center">Email</div>
            </div>

            {{-- Rows --}}
            @foreach (\App\Models\NotificationSetting::EVENT_TYPES as $type => $label)
                <div class="grid grid-cols-12 gap-4 items-center px-6 py-4 border-b border-white/[0.04] last:border-b-0 hover:bg-white/[0.02] transition-colors" wire:key="setting-{{ $type }}">
                    {{-- Event label --}}
                    <div class="col-span-6">
                        <p class="text-sm font-medium text-white">{{ $label }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $type }}</p>
                    </div>

                    {{-- In-App toggle --}}
                    <div class="col-span-3 flex justify-center">
                        <button wire:click="toggle('{{ $type }}', 'database')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:ring-offset-2 focus:ring-offset-gray-950
                                       {{ $settings[$type]['database'] ? 'bg-indigo-600' : 'bg-gray-700' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-lg transition-transform duration-200
                                         {{ $settings[$type]['database'] ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    {{-- Email toggle --}}
                    <div class="col-span-3 flex justify-center">
                        <button wire:click="toggle('{{ $type }}', 'email')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:ring-offset-2 focus:ring-offset-gray-950
                                       {{ $settings[$type]['email'] ? 'bg-indigo-600' : 'bg-gray-700' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-lg transition-transform duration-200
                                         {{ $settings[$type]['email'] ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Info card --}}
        <div class="glass-card rounded-2xl border border-white/[0.06] p-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white">About Notifications</h4>
                    <p class="text-sm text-gray-400 mt-1">
                        <strong class="text-gray-300">In-App</strong> notifications appear in your notification bell and dashboard.
                        <strong class="text-gray-300">Email</strong> notifications are sent to your registered email address.
                        Changes are saved automatically when you toggle a switch.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
