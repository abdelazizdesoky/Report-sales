<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-500 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all duration-200 shadow-lg shadow-red-500/30']) }}>
    {{ $slot }}
</button>
