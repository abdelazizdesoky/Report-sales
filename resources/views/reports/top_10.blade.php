<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
             <h2 class="font-black text-2xl text-white leading-tight">
                📊 إحصائيات أعلى 10 - {{ $report->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('reports.show', $report) }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors font-bold text-sm">
                    عودة للتقرير الرئيسي &rarr;
                </a>
            </div>
           
        </div>
    </x-slot>   

    <!-- External Libs -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control { border-radius: 0.5rem !important; padding: 0.75rem !important; border-color: #e2e8f0 !important; }
        .dark .ts-control { background-color: #0f172a !important; border-color: #334155 !important; color: white !important; }
        .tab-btn.active { border-bottom: 2px solid #4f46e5; color: #4f46e5; }
        .dark .tab-btn.active { border-bottom: 2px solid #818cf8; color: #818cf8; }
    </style>

    <div class="space-y-6" x-data="{ activeTab: 'customers' }">
        
        <!-- Filters -->
        <div class="glass-card p-6">
            <form method="GET" action="{{ route('reports.top10', $report) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
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

                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        تحديث النتائج
                    </button>
                </div>
            </form>
        </div>

        <!-- Check for Data -->
        @if($topDebtors->isEmpty() && $topSalesmen->isEmpty())
            <div class="glass-card p-12 text-center text-slate-500">
                لا توجد بيانات للعرض حالياً.
            </div>
        @else

        <!-- Tabs -->
        <div class="border-b border-slate-200 dark:border-slate-700 flex gap-6 px-2">
            <button @click="activeTab = 'customers'" 
                    :class="{ 'active': activeTab === 'customers' }"
                    class="tab-btn pb-3 px-2 font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                🏆 أعلى 10 عملاء
            </button>
            <button @click="activeTab = 'salesmen'" 
                    :class="{ 'active': activeTab === 'salesmen' }"
                    class="tab-btn pb-3 px-2 font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                💼 أعلى 10 مندوبين
            </button>
        </div>

        <!-- Content -->
        
        <!-- Customers Tab -->
        <div x-show="activeTab === 'customers'" class="glass-card overflow-hidden" x-transition>
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">قائمة العملاء الأكثر مديونية</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-4 py-3 w-10">#</th>
                            <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">العميل</th>
                            <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">المندوب</th>
                            <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">المنطقة</th>
                            <th class="px-4 py-3 font-bold text-indigo-600 dark:text-indigo-400">إجمالي المديونية</th>
                            <th class="px-4 py-3 font-bold text-red-600 dark:text-red-400">Over Due</th>
                            <th class="px-4 py-3 w-1/4">مؤشر</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @php $maxDebt = $topDebtors->first()->{'اجمالي_مديونية_العميل'} ?? 1; @endphp
                        @foreach($topDebtors as $index => $debtor)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $debtor->{'اسم_العميل'} }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $debtor->{'SalesMan'} }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $debtor->{'Region_name'} }}</td>
                                <td class="px-4 py-3 font-bold text-indigo-700 dark:text-indigo-300">{{ number_format($debtor->{'اجمالي_مديونية_العميل'}, 2) }}</td>
                                <td class="px-4 py-3 font-bold text-red-600 dark:text-red-400">{{ number_format($debtor->{'Over Due'}, 2) }}</td>
                                <td class="px-4 py-3">
                                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ ($debtor->{'اجمالي_مديونية_العميل'} / $maxDebt) * 100 }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Salesmen Tab -->
        <div x-show="activeTab === 'salesmen'" class="glass-card overflow-hidden" x-transition style="display: none;">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">قائمة المندوبين الأعلى مديونية (إجمالي العملاء)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-4 py-3 w-10">#</th>
                            <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">المندوب</th>
                            <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400">عدد العملاء</th>
                            <th class="px-4 py-3 font-bold text-indigo-600 dark:text-indigo-400">إجمالي مديونية العملاء</th>
                            <th class="px-4 py-3 w-1/3">مؤشر</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @php $maxSalesDebt = $topSalesmen->first()->total_debt ?? 1; @endphp
                        @foreach($topSalesmen as $index => $salesman)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800 dark:text-white">{{ $salesman->SalesMan }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $salesman->customers_count }}</td>
                                <td class="px-4 py-3 font-bold text-indigo-700 dark:text-indigo-300">{{ number_format($salesman->total_debt, 2) }}</td>
                                <td class="px-4 py-3">
                                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-4 relative">
                                        <div class="bg-blue-600 h-4 rounded-full flex items-center justify-end pr-2 text-[10px] text-white" style="width: {{ ($salesman->total_debt / $maxSalesDebt) * 100 }}%">
                                            {{ round(($salesman->total_debt / $maxSalesDebt) * 100) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @endif
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
</x-app-layout>
