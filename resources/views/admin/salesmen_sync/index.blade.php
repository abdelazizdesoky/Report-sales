<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
            <form action="{{ route('salesmen-sync.sync') }}" method="POST" class="print:hidden">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2 font-bold shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    تحديث القائمة من SQL Server
                </button>
            </form>
            <h2 class="font-black text-2xl text-slate-800 dark:text-white leading-tight">
                إدارة المندوبين والمديرين
            </h2>
        </div>
    </x-slot>

    <div class="py-12 space-y-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Add Assignment Form -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">ربط مدير بمندوب</h3>
                <form action="{{ route('salesmen-sync.assign') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">المدير</label>
                        <select name="manager_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">اختر المدير...</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">المندوب (من SQL Server)</label>
                        <select name="salesman_name" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">اختر المندوب...</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman }}">{{ $salesman }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-black dark:text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            إضافة ربط جديد
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Assignments -->
            <div class="glass-card overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">قائمة الارتباطات الحالية</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-6 py-3 font-bold text-slate-500 dark:text-slate-400">المدير</th>
                                <th class="px-6 py-3 font-bold text-slate-500 dark:text-slate-400">المندوب المربوط</th>
                                <th class="px-6 py-3 font-bold text-slate-500 dark:text-slate-400 text-left">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @forelse($assignments as $assignment)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-800 dark:text-white">{{ $assignment->manager->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $assignment->manager->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-indigo-600 dark:text-indigo-400">
                                        {{ $assignment->salesman_name }}
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <form action="{{ route('salesmen-sync.unassign', [$assignment->manager_id, $assignment->salesman_name]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الارتباط؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        لا توجد ارتباطات مسجلة حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
