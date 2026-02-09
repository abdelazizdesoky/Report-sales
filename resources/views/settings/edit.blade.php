<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row-reverse items-center justify-between w-full">
             <h2 class="font-black text-2xl text-white leading-tight">
                {{ __('الإعدادات العامة') }}
            </h2>
            <div></div> <!-- Spacer to keep title right -->
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="form-card max-w-2xl mx-auto overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">بيانات الشركة الأساسية</h3>
            </div>
            
            <form method="post" action="{{ route('settings.update') }}" class="p-6 space-y-6">
                @csrf
                @method('patch')

                <div>
                    <x-input-label for="company_name" value="اسم الشركة" />
                    <x-text-input id="company_name" name="company_name" type="text" class="mt-1" :value="old('company_name', $setting->company_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                </div>

                <div>
                    <x-input-label for="activity" value="النشاط التجاري" />
                    <x-text-input id="activity" name="activity" type="text" class="mt-1" :value="old('activity', $setting->activity)" />
                    <x-input-error class="mt-2" :messages="$errors->get('activity')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="phone" value="رقم الهاتف" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1" :value="old('phone', $setting->phone)" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>
                    <div>
                        <x-input-label for="address" value="العنوان" />
                        <x-text-input id="address" name="address" type="text" class="mt-1" :value="old('address', $setting->address)" />
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="description" value="وصف الشركة" />
                    <textarea id="description" name="description" rows="3" class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/30 transition-all duration-200 shadow-sm">{{ old('description', $setting->description) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <!-- Logo Placeholder -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 text-center">
                    <div class="inline-block p-4 bg-slate-100 dark:bg-slate-800 rounded-full mb-2">
                        <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest leading-loose">شعار الشركة (قريباً)</p>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <x-primary-button>
                        {{ __('حفظ التغييرات') }}
                    </x-primary-button>

                    @if (session('status') === 'settings-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-emerald-600 dark:text-emerald-400 font-bold"
                        >{{ __('تم الحفظ بنجاح.') }}</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
