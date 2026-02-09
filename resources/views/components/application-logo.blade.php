
<h1 class="text-xl font-black text-slate-800 dark:text-white truncate max-w-xs">
                            {{ \App\Models\Setting::first()?->company_name ?? ' ' }}  </h1>
                            <p>{{ \App\Models\Setting::first()?->description ?? '' }}</p>