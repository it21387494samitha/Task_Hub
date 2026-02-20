<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 border border-indigo-500/30 rounded-xl font-semibold text-sm text-white tracking-wide hover:from-indigo-500 hover:to-indigo-400 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/25 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 active:translate-y-0 transition-all duration-200']) }}>
    {{ $slot }}
</button>
