<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
              <h2 class="font-black text-2xl text-white leading-tight">
                {{ __('إدارة المستخدمين') }}
            </h2>
            <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl px-6 py-2 transition-all shadow-lg shadow-indigo-500/20">
                إضافة مستخدم جديد +
            </a>
          
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Search Bar -->
        <div class="form-card px-6 py-4">
            <form action="{{ route('users.index') }}" method="GET" class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن مستخدم بالاسم، البريد، أو المندوب..." class="w-full px-4 py-2 pr-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-xl transition-all shadow-lg shadow-indigo-500/20">
                    بحث
                </button>
                @if(request('search'))
                    <a href="{{ route('users.index') }}" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 text-sm font-bold">
                        إعادة تعيين
                    </a>
                @endif
            </form>
        </div>

        <div class="form-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">المستخدم</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">المنطقة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">البريد الإلكتروني</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose text-center">الحالة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">الصلاحيات</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">تاريخ الانضمام</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach($users as $user)
                        <tr class="table-row {{ !$user->is_enabled ? 'opacity-60 bg-slate-50/50 dark:bg-slate-900/50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</span>
                                        @if($user->salesman_name)
                                            <span class="text-[10px] text-slate-500 font-medium">المندوب: {{ $user->salesman_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">
                                <div class="text-sm text-slate-500">{{ $user->region ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($user->is_enabled)
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-600/10 text-green-600 dark:text-green-400 text-[10px] font-bold rounded-lg border border-green-200 dark:border-green-500/20">
                                        نشط
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-600/10 text-red-600 dark:text-red-400 text-[10px] font-bold rounded-lg border border-red-200 dark:border-red-500/20">
                                        معطل
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold rounded-lg border border-indigo-200 dark:border-indigo-500/20">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-sm">{{ $user->created_at->format('Y/m/d') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 {{ $user->is_enabled ? 'text-green-400 hover:text-red-500' : 'text-red-400 hover:text-green-500' }} transition-colors" title="{{ $user->is_enabled ? 'تعطيل الحساب' : 'تفعيل الحساب' }}">
                                            @if($user->is_enabled)
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    <a href="{{ route('users.edit', $user) }}" class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 dark:divide-slate-800/50">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
