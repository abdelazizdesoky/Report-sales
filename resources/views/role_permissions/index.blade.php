<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
              <h2 class="font-black text-2xl text-white leading-tight">
                {{ __('الصلاحيات المتقدمة') }}
            </h2>
            <div></div> <!-- Spacer -->
          
        </div>
        <!-- <p class="text-right text-slate-500 dark:text-slate-200 text-sm mt-1">التحكم الكامل في الوصول للوحدات والتقارير المالية لكل مستوى وظيفي</p> -->
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <form action="{{ route('role-permissions.update') }}" method="POST">
                @csrf
                
                <!-- System Permissions Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        صلاحيات النظام (CRUD)
                    </h3>
                    <div class="glass-card overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                                        <th class="px-6 py-6 text-xs font-black text-slate-500 uppercase tracking-widest leading-loose">المستوى الوظيفي</th>
                                        @php
                                            $moduleGroups = [
                                                'المستخدمين' => ['view users', 'create users', 'edit users', 'delete users'],
                                                'الإعدادات' => ['view settings', 'edit settings'],
                                                'التقارير' => ['view reports', 'manage report visibility', 'export excel', 'view hierarchy'],
                                            ];
                                        @endphp
                                        @foreach($moduleGroups as $groupName => $perms)
                                            <th colspan="{{ count($perms) }}" class="px-6 py-6 text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-widest leading-loose text-center border-x border-slate-200/50 dark:border-slate-800/50">
                                                {{ $groupName }}
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr class="bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-200 dark:border-slate-800">
                                        <th class="px-6 py-2"></th>
                                        @foreach($moduleGroups as $perms)
                                            @foreach($perms as $perm)
                                                <th class="px-2 py-2 text-[10px] font-bold text-slate-500 text-center border-x border-slate-200/50 dark:border-slate-800/50">
                                                    {{ str_replace(['view ', 'create ', 'edit ', 'delete ', 'manage ', ' visibility', 'export excel', 'view hierarchy'], ['عرض ', 'إضافة ', 'تعديل ', 'حذف ', 'إدارة ', ' ', 'تصدير إكسل', 'عرض الهيكل'], $perm) }}
                                                </th>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @foreach($roles as $role)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td class="px-6 py-4">
                                                <span class="font-bold text-slate-900 dark:text-white">{{ $role->name }}</span>
                                            </td>
                                            @foreach($moduleGroups as $perms)
                                                @foreach($perms as $perm)
                                                    <td class="px-2 py-4 text-center border-x border-slate-200/50 dark:border-slate-800/50">
                                                        <input type="checkbox" name="permissions[{{ $role->id }}][]" value="{{ $perm }}" 
                                                               {{ $role->hasPermissionTo($perm) ? 'checked' : '' }}
                                                               class="w-4 h-4 text-indigo-600 bg-slate-100 border-slate-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-slate-800 dark:bg-slate-700 dark:border-slate-600">
                                                    </td>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Report Visibility Section -->
                <div class="space-y-4 mt-12">
                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        الوصول للتقارير المالية المخصصة
                    </h3>
                    <div class="glass-card overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                                        <th class="px-6 py-6 text-xs font-black text-slate-500 uppercase tracking-widest leading-loose">المستوى الوظيفي</th>
                                        @foreach($reports as $report)
                                            <th class="px-6 py-6 text-xs font-black text-slate-500 uppercase tracking-widest leading-loose text-center">
                                                {{ $report->name }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @foreach($roles as $role)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td class="px-6 py-4">
                                                <span class="font-bold text-slate-900 dark:text-white">{{ $role->name }}</span>
                                            </td>
                                            @foreach($reports as $report)
                                                <td class="px-6 py-4 text-center">
                                                    <label class="inline-flex items-center cursor-pointer relative group">
                                                        <input type="checkbox" 
                                                               name="reports[{{ $role->id }}][]" 
                                                               value="{{ $report->id }}"
                                                               class="sr-only peer"
                                                               {{ $role->belongsToMany(App\Models\Report::class, 'role_reports')->where('report_id', $report->id)->exists() ? 'checked' : '' }}>
                                                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                                    </label>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-12">
                    <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-12 rounded-2xl shadow-xl shadow-indigo-500/20 transition-all transform hover:scale-105">
                        {{ __('حفظ مصفوفة الصلاحيات') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
