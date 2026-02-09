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
        <div class="form-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">المستخدم</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">البريد الإلكتروني</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">الصلاحيات</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose">تاريخ الانضمام</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest leading-loose text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach($users as $user)
                        <tr class="table-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">{{ $user->email }}</td>
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
        </div>
    </div>
</x-app-layout>
