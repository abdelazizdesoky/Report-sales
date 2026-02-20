<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
               <h2 class="font-black text-2xl text-white leading-tight">
                {{ __('تعديل بيانات المستخدم') }}
            </h2>
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors text-sm font-bold shadow-sm">
                عودة للقائمة &larr;
            </a>
         
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="form-card overflow-hidden">
                <form method="POST" action="{{ route('users.update', $user) }}" class="p-8 space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" value="الاسم الكامل" />
                        <x-text-input id="name" name="name" type="text" class="mt-1" :value="old('name', $user->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->  
                    <div>
                        <x-input-label for="email" value="البريد الإلكتروني" />
                        <x-text-input id="email" name="email" type="email" class="mt-1" :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Username -->
                    <div>
                        <x-input-label for="username" value="اسم المستخدم (Username)" />
                        <x-text-input id="username" name="username" type="text" class="mt-1" :value="old('username', $user->username)" placeholder="مثال: ahmed_123" />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <x-input-label for="role" value="المستوى (الصلاحية)" />
                        <select id="role" name="role" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/30 transition-all duration-200 shadow-sm">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- Salesman Name (SQL Server Mapping) -->
                    <div>
                        <x-input-label for="salesman_name" value="اسم المندوب في النظام (لربط التقارير)" />
                        <x-text-input id="salesman_name" name="salesman_name" type="text" class="mt-1" :value="old('salesman_name', $user->salesman_name)" placeholder="مثال: احمد محمد علي" />
                        <p class="text-xs text-slate-500 mt-1">اتركه فارغاً إذا كان المستخدم مشرفاً أو يرى كل البيانات.</p>
                        <x-input-error :messages="$errors->get('salesman_name')" class="mt-2" />
                    </div>

                    <!-- Supervisor -->
                    <div>
                        <x-input-label for="supervisor_id" value="المشرف (مدير المنطقة أو المدير المباشر)" />
                        <select id="supervisor_id" name="supervisor_id" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/30 transition-all duration-200 shadow-sm">
                            <option value="">-- بدون مشرف --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('supervisor_id', $user->supervisor_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->roles->pluck('name')->first() }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('supervisor_id')" class="mt-2" />
                    </div>

                    <!-- Account Status -->
                    <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                        <x-input-label for="is_enabled" value="حالة الحساب" class="!mb-0" />
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_enabled" value="0">
                            <input type="checkbox" name="is_enabled" id="is_enabled" value="1" class="sr-only peer" {{ $user->is_enabled ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                            <span class="ms-3 text-sm font-medium text-slate-800 dark:text-slate-300">نشط</span>
                        </label>
                    </div>

                    <!-- Password (Optional) -->
                    <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-bold mb-4">اترك حقول كلمة المرور فارغة إذا كنت لا تريد تغييرها</p>
                        
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="password" value="كلمة المرور الجديدة" />
                                <x-text-input id="password" name="password" type="password" class="mt-1" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" value="تأكيد كلمة المرور الجديدة" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button>
                            {{ __('تحديث البيانات') }}
                        </x-primary-button>
                        <x-secondary-button onclick="window.history.back()">
                            {{ __('إلغاء') }}
                        </x-secondary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
