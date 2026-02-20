@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/[0.04] border border-white/[0.06] text-gray-200 placeholder-gray-500 focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30 focus:bg-white/[0.06] rounded-xl shadow-sm transition-all duration-200']) }}>
