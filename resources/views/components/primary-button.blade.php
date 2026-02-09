<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all duration-200 shadow-lg shadow-indigo-500/30']) }}>
    {{ $slot }}
</button>
