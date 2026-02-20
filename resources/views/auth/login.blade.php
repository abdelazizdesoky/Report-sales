<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="rtl">
        @csrf

        <!-- Email Address or Username -->
        <div>
            <x-input-label for="login" :value="__('البريد الإلكتروني أو اسم المستخدم')" />
            <x-text-input id="login" class="block mt-1 w-full text-right" type="text" name="login" :value="old('login')" required autofocus />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('كلمة المرور')" />

            <x-text-input id="password" class="block mt-1 w-full text-right"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4 text-right">
            <label for="remember_me" class="inline-flex items-center">
                <span class="mr-2 text-sm text-slate-600 dark:text-slate-400">{{ __('تذكرني') }}</span>
                <input id="remember_me" type="checkbox" class="rounded dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-slate-800" name="remember">
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <!-- @if (Route::has('password.request'))
                <a class="underline text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800" href="{{ route('password.request') }}">
                    {{ __('نسيت كلمة المرور؟') }}
                </a>
            @endif -->

            <x-primary-button class="ml-3">
                {{ __('تسجيل الدخول') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
