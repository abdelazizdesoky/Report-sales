<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
             <h2 class="font-black text-2xl text-slate-800 dark:text-white leading-tight">
                {{ $report->name }}
            </h2>
            <div class="flex gap-2 print:hidden items-center">
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors font-bold text-sm">
                    عودة &rarr;
                </a>
                <a href="{{ route('reports.top10', $report) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    أعلى 10
                </a>
                <a href="{{ route('reports.export.excel', $report) }}?{{ http_build_query(request()->all()) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصدير Excel
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    طباعة / PDF
                </button>
            </div>
           
        </div>
    </x-slot>

    <style media="print">
        @page { size: landscape; margin: 1cm; }
        body { background: white; color: black; }
        .glass-card { box-shadow: none !important; background: white !important; border: 1px solid #ddd !important; }
        .print\:hidden, header, nav, footer { display: none !important; }
        .overflow-x-auto { overflow: visible !important; }
        table { border: 1px solid #ccc; width: 100%; table-layout: fixed; font-size: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px !important; word-wrap: break-word; }
        th { background-color: #f0f0f0 !important; color: black !important; -webkit-print-color-adjust: exact; }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control { border-radius: 0.5rem !important; padding: 0.75rem !important; border-color: #e2e8f0 !important; }
        .dark .ts-control { background-color: #0f172a !important; border-color: #334155 !important; color: white !important; }
        .dark .ts-dropdown { background-color: #1e293b !important; color: white !important; border-color: #334155 !important; }
        .dark .ts-dropdown .option:hover, .dark .ts-dropdown .active { background-color: #334155 !important; color: white !important; }
        .ts-wrapper.multi .ts-control > div { background: #4f46e5; color: white; border-radius: 4px; }
        /* Tab styles */
        .tab-button { transition: all 0.2s; }
        .tab-button.active { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>

    <div class="space-y-4">
        <!-- Statistics Cards -->
        @php
            $overduePercent = $statistics['total_debt'] > 0 ? round(($statistics['total_overdue'] / $statistics['total_debt']) * 100, 1) : 0;
            $notDuePercent = $statistics['total_debt'] > 0 ? round(($statistics['total_not_due'] / $statistics['total_debt']) * 100, 1) : 0;
        @endphp
        <div class="flex gap-8 flex-nowrap overflow-x-auto print:hidden">
            <div class="glass-card px-12 py-8  flex items-center gap-3 whitespace-nowrap">
                <span class="text-sm text-slate-500 dark:text-slate-400">العملاء:</span>
                <span class="text-base font-bold text-slate-800 dark:text-white">{{ number_format($statistics['total_customers']) }}</span>
            </div>
            <div class="glass-card px-8  py-4 flex items-center gap-3 whitespace-nowrap">
                <span class="text-sm text-slate-500 dark:text-slate-400">المديونية:</span>
                <span class="text-base font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($statistics['total_debt'], 0) }}</span>
            </div>
            <div class="glass-card px-8 py-4 flex items-center gap-3 whitespace-nowrap border-r-2 border-red-500">
                <span class="text-sm text-slate-500 dark:text-slate-400">مستحق:</span>
                <span class="text-base font-bold text-red-600 dark:text-red-400">{{ number_format($statistics['total_overdue'], 0) }}</span>
                <span class="text-sm text-red-500">({{ $overduePercent }}%)</span>
            </div>
            <div class="glass-card px-8 py-4 flex items-center gap-3 whitespace-nowrap border-r-2 border-green-500">
                <span class="text-sm text-slate-500 dark:text-slate-400">غير مستحق:</span>
                <span class="text-base font-bold text-green-600 dark:text-green-400">{{ number_format($statistics['total_not_due'], 0) }}</span>
                <span class="text-sm text-green-500">({{ $notDuePercent }}%)</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card p-6 print:hidden">
            <form method="GET" action="{{ route('reports.show', $report) }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">بحث (الاسم / الكود)</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="بحث سريع...">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">التصنيف</label>
                    <select id="select-classification" name="classification" placeholder="اختر التصنيف...">
                        <option value="">الكل</option>
                        @foreach($filterOptions['classifications'] as $option)
                            <option value="{{ $option }}" {{ request('classification') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">المنطقة</label>
                    <select id="select-region" name="region" placeholder="اختر المنطقة...">
                        <option value="">الكل</option>
                        @foreach($filterOptions['regions'] as $option)
                            <option value="{{ $option }}" {{ request('region') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">المندوب</label>
                    <select id="select-salesman" name="salesman" placeholder="اختر المندوب...">
                        <option value="">الكل</option>
                        @foreach($filterOptions['salesmen'] as $option)
                            <option value="{{ $option }}" {{ request('salesman') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-1 md:col-span-2 lg:col-span-4 flex justify-end gap-2 mt-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('reports.show', $report) }}" class="bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-2 px-4 rounded-lg transition-colors">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>

        <script>
            document.querySelectorAll('select[id^="select-"]').forEach(function(el){
                new TomSelect(el,{
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            });
        </script>

        <!-- Tabs Navigation -->
        <div class="glass-card overflow-hidden">
            <div class="flex border-b border-slate-200 dark:border-slate-700 print:hidden">
                <button onclick="switchTab('customers')" id="tab-customers" class="tab-button active px-6 py-3 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    جدول العملاء
                </button>
                <button onclick="switchTab('regions')" id="tab-regions" class="tab-button px-6 py-3 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    ملخص المناطق
                </button>
                <button onclick="switchTab('salesmen')" id="tab-salesmen" class="tab-button px-6 py-3 font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    ملخص المندوبين
                </button>
            </div>

            <!-- Customers Table Tab -->
            <div id="content-customers" class="tab-content active">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">الكود</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">العميل</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">التصنيف</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">المنطقة</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">المندوب</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">إجمالي المديونية</th>
                                <th class="px-4 py-3 font-bold text-green-600 dark:text-green-400">غير مستحق</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">1-7 يوم</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">8-14 يوم</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">15-22 يوم</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">23-30 يوم</th>
                                <th class="px-4 py-3 font-bold text-yellow-600 dark:text-yellow-400">31-60 يوم</th>
                                <th class="px-4 py-3 font-bold text-orange-600 dark:text-orange-400">61-180 يوم</th>
                                <th class="px-4 py-3 font-bold text-red-600 dark:text-red-400">+180 يوم</th>
                                <th class="px-4 py-3 font-bold text-red-700 dark:text-red-500">Over Due</th>
                                <th class="px-4 py-3 font-bold text-purple-600 dark:text-purple-400">النسبة %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @forelse($data as $row)
                                @php
                                    $totalDebt = $row->{'اجمالي_مديونية_العميل'} ?? 0;
                                    $notDue = $row->{'Not Due'} ?? 0;
                                    $overdue = $row->{'Over Due'} ?? 0;
                                    // Calculate percentage excluding Not Due and Over Due
                                    $middleAging = $totalDebt - $notDue - $overdue;
                                    $percent = $statistics['total_debt'] > 0 ? round(($middleAging / $statistics['total_debt']) * 100, 2) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $row->{'كود_العميل'} }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $row->{'اسم_العميل'} }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $row->{'تصنيف'} }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $row->{'Region_name'} }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $row->{'SalesMan'} }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">{{ number_format($totalDebt, 0) }}</td>
                                    <td class="px-4 py-3 text-green-600 dark:text-green-400">{{ number_format($notDue, 0) }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ number_format($row->{'1-7 Days'} ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ number_format($row->{'8-14 Days'} ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ number_format($row->{'15-22 Days'} ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ number_format($row->{'23-30 Days'} ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 text-yellow-600 dark:text-yellow-400">{{ number_format($row->{'31-60 Days'} ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 text-orange-600 dark:text-orange-400">{{ number_format($row->{'61-180 Days'} ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 text-red-600 dark:text-red-400">{{ number_format($row->{'+180 Days'} ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 font-bold text-red-700 dark:text-red-500">{{ number_format($overdue, 0) }}</td>
                                    <td class="px-4 py-3 font-bold text-purple-600 dark:text-purple-400">{{ $percent }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        لا توجد بيانات مطابقة للبحث
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Regions Summary Tab -->
            <div id="content-regions" class="tab-content">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">#</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">المنطقة</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">عدد العملاء</th>
                                <th class="px-4 py-3 font-bold text-indigo-600 dark:text-indigo-400">إجمالي المديونية</th>
                                <th class="px-4 py-3 font-bold text-green-600 dark:text-green-400">غير مستحق</th>
                                <th class="px-4 py-3 font-bold text-red-600 dark:text-red-400">المستحق (Over Due)</th>
                                <th class="px-4 py-3 font-bold text-purple-600 dark:text-purple-400">النسبة %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @php $regionTotal = $debtSummaries['by_region']->sum('total_debt'); @endphp
                            @forelse($debtSummaries['by_region'] as $index => $region)
                                @php
                                    $regionPercent = $regionTotal > 0 ? round(($region->total_debt / $regionTotal) * 100, 2) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $region->Region_name }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ number_format($region->customers_count) }}</td>
                                    <td class="px-4 py-3 font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($region->total_debt, 0) }}</td>
                                    <td class="px-4 py-3 text-green-600 dark:text-green-400">{{ number_format($region->not_due ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 font-bold text-red-600 dark:text-red-400">{{ number_format($region->overdue ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 font-bold text-purple-600 dark:text-purple-400">{{ $regionPercent }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        لا توجد بيانات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($debtSummaries['by_region']->count() > 0)
                        <tfoot class="bg-slate-100 dark:bg-slate-800/50">
                            <tr class="font-bold">
                                <td colspan="2" class="px-4 py-3 text-slate-800 dark:text-white">الإجمالي</td>
                                <td class="px-4 py-3 text-slate-800 dark:text-white">{{ number_format($debtSummaries['by_region']->sum('customers_count')) }}</td>
                                <td class="px-4 py-3 text-indigo-600 dark:text-indigo-400">{{ number_format($regionTotal, 0) }}</td>
                                <td class="px-4 py-3 text-green-600 dark:text-green-400">{{ number_format($debtSummaries['by_region']->sum('not_due'), 0) }}</td>
                                <td class="px-4 py-3 text-red-600 dark:text-red-400">{{ number_format($debtSummaries['by_region']->sum('overdue'), 0) }}</td>
                                <td class="px-4 py-3 text-purple-600 dark:text-purple-400">100%</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Salesmen Summary Tab -->
            <div id="content-salesmen" class="tab-content">
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">#</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">المندوب</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">المنطقة</th>
                                <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">عدد العملاء</th>
                                <th class="px-4 py-3 font-bold text-indigo-600 dark:text-indigo-400">إجمالي المديونية</th>
                                <th class="px-4 py-3 font-bold text-green-600 dark:text-green-400">غير مستحق</th>
                                <th class="px-4 py-3 font-bold text-red-600 dark:text-red-400">المستحق (Over Due)</th>
                                <th class="px-4 py-3 font-bold text-purple-600 dark:text-purple-400">النسبة %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @php $salesmanTotal = $debtSummaries['by_salesman']->sum('total_debt'); @endphp
                            @forelse($debtSummaries['by_salesman'] as $index => $salesman)
                                @php
                                    $salesmanPercent = $salesmanTotal > 0 ? round(($salesman->total_debt / $salesmanTotal) * 100, 2) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $salesman->SalesMan }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $salesman->Region_name }}</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ number_format($salesman->customers_count) }}</td>
                                    <td class="px-4 py-3 font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($salesman->total_debt, 0) }}</td>
                                    <td class="px-4 py-3 text-green-600 dark:text-green-400">{{ number_format($salesman->not_due ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 font-bold text-red-600 dark:text-red-400">{{ number_format($salesman->overdue ?? 0, 0) }}</td>
                                    <td class="px-4 py-3 font-bold text-purple-600 dark:text-purple-400">{{ $salesmanPercent }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        لا توجد بيانات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($debtSummaries['by_salesman']->count() > 0)
                        <tfoot class="bg-slate-100 dark:bg-slate-800/50">
                            <tr class="font-bold">
                                <td colspan="3" class="px-4 py-3 text-slate-800 dark:text-white">الإجمالي</td>
                                <td class="px-4 py-3 text-slate-800 dark:text-white">{{ number_format($debtSummaries['by_salesman']->sum('customers_count')) }}</td>
                                <td class="px-4 py-3 text-indigo-600 dark:text-indigo-400">{{ number_format($salesmanTotal, 0) }}</td>
                                <td class="px-4 py-3 text-green-600 dark:text-green-400">{{ number_format($debtSummaries['by_salesman']->sum('not_due'), 0) }}</td>
                                <td class="px-4 py-3 text-red-600 dark:text-red-400">{{ number_format($debtSummaries['by_salesman']->sum('overdue'), 0) }}</td>
                                <td class="px-4 py-3 text-purple-600 dark:text-purple-400">100%</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        @if($data->hasPages())
            <div class="glass-card p-6">
                {{ $data->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <script>
        function switchTab(tabName) {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab
            document.getElementById('tab-' + tabName).classList.add('active');
            document.getElementById('content-' + tabName).classList.add('active');
        }
    </script>
</x-app-layout>
