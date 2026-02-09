<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
                <h2 class="font-black text-2xl text-white leading-tight">
                {{ $report->name }}
            </h2>
            <a href="{{ route('reports.index') }}" class="text-sm font-bold text-indigo-100 hover:text-white transition-colors flex items-center gap-2 group">
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                العودة للتقارير
            </a>
        
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="glass-card overflow-hidden">
            @if($data->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-block p-4 bg-slate-50 dark:bg-slate-850 rounded-full mb-4">
                        <svg class="w-12 h-12 text-slate-400 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-slate-500 font-medium font-bold uppercase tracking-widest leading-loose">لا توجد بيانات متاحة لهذا التقرير حالياً.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                                @foreach(array_keys((array)$data->first()) as $column)
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">
                                        {{ str_replace('_', ' ', $column) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @foreach($data as $row)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    @foreach((array)$row as $value)
                                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                            {{ $value }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($data->hasPages())
            <div class="glass-card p-6">
                {{ $data->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
