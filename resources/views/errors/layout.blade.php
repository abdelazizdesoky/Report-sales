<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') - {{ \App\Models\Setting::first()?->company_name ?? 'Alarabia group' }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Cairo:400,500,600,700,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { 
                font-family: 'Cairo', sans-serif; 
                background: radial-gradient(circle at top right, #f8fafc, #f1f5f9);
            }
            .dark body {
                background: radial-gradient(circle at top right, #0f172a, #020617);
            }
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            .dark .glass {
                background: rgba(15, 23, 42, 0.6);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
        </style>
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="antialiased text-slate-900 dark:text-slate-200 min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden transition-colors duration-300">
        <!-- Abstract Shapes for Premium Feel -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>

        <div class="max-w-xl w-full relative z-10">
            <div class="glass dark:glass rounded-[2rem] shadow-2xl shadow-indigo-500/10 p-8 sm:p-12 text-center transform transition-all duration-500 hover:scale-[1.01]">
                
                <!-- Icon Area -->
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl @yield('icon-bg', 'bg-gradient-to-br from-indigo-500 to-indigo-700') text-white mb-8 shadow-xl shadow-indigo-500/30 ring-8 ring-indigo-500/10">
                    @yield('icon')
                </div>
                
                <h1 class="text-4xl sm:text-5xl font-black text-slate-800 dark:text-white mb-6 tracking-tight">
                    @yield('code', '404')
                </h1>

                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-4">
                    @yield('title')
                </h2>
                
                <p class="text-slate-600 dark:text-slate-400 mb-10 text-lg leading-relaxed font-medium">
                    @yield('message')
                </p>

                <!-- Navigation Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ url('/') }}" class="w-full sm:w-auto px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black transition-all shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-3 group">
                        <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        العودة للرئيسية
                    </a>
                    
                    <button onclick="window.history.back()" class="w-full sm:w-auto px-10 py-4 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-2xl font-black transition-all border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 flex items-center justify-center gap-3 group shadow-lg shadow-slate-200/50 dark:shadow-none">
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        رجوع للخلف
                    </button>
                </div>
            </div>

            <!-- Footer Logo/Text -->
            <div class="mt-12 text-center opacity-50 flex items-center justify-center gap-3">
                @php $settings = \App\Models\Setting::first(); @endphp
                @if($settings?->logo_path)
                    <img src="{{ asset('storage/'.$settings->logo_path) }}" alt="Logo" class="h-6 w-auto grayscale">
                @endif
                <span class="text-sm font-bold tracking-widest uppercase">
                    {{ $settings?->company_name ?? 'Report Sales' }}
                </span>
            </div>
        </div>
    </body>
</html>

