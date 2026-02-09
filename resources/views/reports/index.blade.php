<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
              <h2 class="font-black text-2xl text-white leading-tight">
                {{ __('تقارير المبيعات المتوفرة') }}
            </h2>
            <div></div> <!-- Spacer -->
          
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($reports as $report)
            <a href="{{ route('reports.show', $report) }}" class="glass-card group p-8 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-6 border border-slate-200 dark:border-slate-700 transition-all duration-300 group-hover:scale-110 group-hover:border-indigo-500/50 group-hover:bg-indigo-600/10">
                    <svg class="w-8 h-8 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">{{ $report->name }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">عرض تفصيلي لبيانات {{ $report->name }} مع إمكانية التصدير والطباعة.</p>
                
                <div class="mt-8 flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-sm uppercase tracking-widest leading-loose">
                    <span>فتح التقرير</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-[-4px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </div>
            </a>
        @endforeach
    </div>
</x-app-layout>
