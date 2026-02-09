<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl font-bold text-sm text-slate-700 dark:text-slate-200 uppercase tracking-wider hover:bg-slate-200 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:opacity-25 transition-all duration-200 shadow-sm']) }}>
    {{ $slot }}
</button>
