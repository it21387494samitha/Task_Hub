<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl font-semibold text-sm text-gray-300 tracking-wide shadow-sm hover:bg-white/[0.08] hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-white/20 disabled:opacity-25 transition-all duration-200']) }}>
    {{ $slot }}
</button>
