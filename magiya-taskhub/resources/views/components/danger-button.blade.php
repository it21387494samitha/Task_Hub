<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-500 border border-red-500/30 rounded-xl font-semibold text-sm text-white tracking-wide hover:from-red-500 hover:to-red-400 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-red-500/25 focus:outline-none focus:ring-2 focus:ring-red-500/50 active:translate-y-0 transition-all duration-200']) }}>
    {{ $slot }}
</button>
