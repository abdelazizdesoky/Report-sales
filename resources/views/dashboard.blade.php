<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
               <h2 class="font-black text-2xl text-white leading-tight">
                {{ __('لوحة التحكم') }}
            </h2>
            <div></div> <!-- Spacer -->
          
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Welcome Card -->
        <div class="glass-card p-8 vibrant-gradient text-white border-none shadow-xl shadow-indigo-500/20">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold mb-2">أهلاً بك، {{ Auth::user()->name }}</h3>
                    <p class="text-indigo-100 opacity-90">نظرة عامة شاملة على عمليات النظام والتقارير المتاحة.</p>
                </div>
                <div class="hidden lg:block">
                    <div class="p-4 bg-white/20 rounded-2xl backdrop-blur-xl">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="glass-card p-6">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-emerald-500/10 rounded-2xl">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">إجمالي المستخدمين</p>
                        <h4 class="text-3xl font-black mt-1">{{ $stats['users_count'] }}</h4>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-amber-500/10 rounded-2xl">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">التقارير المتاحة</p>
                        <h4 class="text-3xl font-black  mt-1">{{ $stats['reports_count'] }}</h4>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-indigo-500/10 rounded-2xl">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">آخر تحديث</p>
                        <h4 class="text-lg font-black mt-1">{{ $stats['last_report_date'] }}</h4>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Reports Quick Access Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-800 dark:text-white">الوصول السريع للتقارير</h3>
                <a href="{{ route('reports.index') }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition-colors flex items-center gap-1 group">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    عرض كافة التقارير
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($reports as $report)
                    <a href="{{ route('reports.show', $report) }}" class="glass-card p-6 flex flex-col items-center justify-center gap-4 hover:border-indigo-500/50 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 transition-all duration-300 group shadow-lg hover:shadow-indigo-500/10">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm border border-slate-200 dark:border-slate-700">
                            @if($report->code === 'aging_report')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            @endif
                        </div>
                        <div class="text-center">
                            <p class="font-black text-slate-800 dark:text-white mb-1 leading-snug">{{ $report->name }}</p>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[9px] text-slate-500 font-bold uppercase tracking-widest rounded-full border border-slate-200 dark:border-slate-700 group-hover:border-indigo-500/30 transition-colors">
                                {{ $report->code }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 glass-card text-center border-dashed border-2 border-slate-200 dark:border-slate-800">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <p class="text-slate-500 font-bold">لا تتوفر تقارير بصلاحياتك الحالية</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity Placeholder -->
        <div class="glass-card shadow-lg shadow-slate-200/50 dark:shadow-none">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">أحدث العمليات</h3>
                <button class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 transition-colors">عرض الكل</button>
            </div>
            <div class="p-8 text-center text-right">
                <div class="inline-block p-4 bg-slate-50 dark:bg-slate-800/50 rounded-full mb-4">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-slate-500 font-medium">لا توجد عمليات حديثة لمراجعتها.</p>
            </div>
        </div>
    </div>
</x-app-layout>
