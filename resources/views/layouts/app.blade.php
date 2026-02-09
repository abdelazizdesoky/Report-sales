<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{\App\Models\Setting::first()?->company_name ?? '' }}</title>
        <link rel="icon" href="{{ asset('storage/'.\App\Models\Setting::first()?->logo_path) }}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Cairo:400,500,600,700,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script>
            // Set theme before content loads to avoid flicker
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Cairo', sans-serif; }
            
            /* Sidebar transitions */
            #sidebar { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease; }
            .sidebar-collapsed .sidebar-text,
            .sidebar-collapsed .sidebar-section-title,
            .sidebar-collapsed .sidebar-description {
                display: none;
            }
            .sidebar-collapsed .sidebar-link {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            .sidebar-collapsed .sidebar-link svg {
                margin: 0;
                width: 1.5rem;
                height: 1.5rem;
            }
            .sidebar-collapsed #brand-text {
                display: none;
            }
            .sidebar-collapsed #user-info {
                display: none;
            }
            .sidebar-collapsed #brand-header {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-900 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 overflow-hidden transition-colors duration-300 rtl">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar (Right - RTL) -->
            <aside id="sidebar" class="w-72 bg-slate-800 dark:bg-slate-900 border-l border-slate-700/50 flex flex-col flex-shrink-0 transition-all duration-300 z-30">
                @include('layouts.navigation')
            </aside>

            <!-- Main Workspace (Left) -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
                <!-- Top Navbar -->
                <nav class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800/50 flex items-center justify-between px-8 z-20 shadow-sm transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <button id="sidebar-toggle" class="p-2 text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                        </button>
                        
                        @php
                            $settings = \App\Models\Setting::first();
                        @endphp
                        
                        <div class="flex items-center gap-3">
                            @if($settings?->logo_path)
                                <img src="{{ asset('storage/'.$settings->logo_path) }}" alt="Logo" class="h-8 w-auto object-contain">
                            @else
                                <div class="p-2 bg-indigo-600 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                            @endif
                            <h1 class="text-xl font-black text-slate-800 dark:text-white truncate max-w-xs">
                                {{ $settings?->company_name ?? 'Alarabia group ' }}
                            </h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-4 pr-6 border-r border-slate-200 dark:border-slate-800/50">
                            <div class="text-right">
                                <p class="text-sm font-black text-slate-800 dark:text-white leading-none">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">
                                    {{ Auth::user()->roles->first()?->name ?? 'مستخدم' }}
                                </p>
                            </div>
                            <!-- Theme Toggle -->
                            <button id="theme-toggle" class="p-2 text-slate-500 hover:text-amber-500 transition-colors">
                                <svg class="w-5 h-5 dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>
                                <svg class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading (Premium Banner) -->
                @isset($header)
                    <div class="bg-gradient-to-l from-indigo-600 to-indigo-800 dark:from-indigo-900 dark:to-slate-900 px-8 py-8 border-b border-indigo-500/20 relative overflow-hidden">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                        <div class="relative max-w-7xl mx-auto flex items-center justify-between">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-8 custom-scrollbar bg-slate-50 dark:bg-slate-950/50">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <script>
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            
            // Check storage for sidebar state
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('sidebar-collapsed');
                sidebar.classList.replace('w-72', 'w-20');
            }

            toggle.addEventListener('click', () => {
                const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                if (isCollapsed) {
                    sidebar.classList.remove('sidebar-collapsed');
                    sidebar.classList.replace('w-20', 'w-72');
                    localStorage.setItem('sidebar-collapsed', 'false');
                } else {
                    sidebar.classList.add('sidebar-collapsed');
                    sidebar.classList.replace('w-72', 'w-20');
                    localStorage.setItem('sidebar-collapsed', 'true');
                }
            });

            // Theme Toggle Logic
            const themeToggle = document.getElementById('theme-toggle');
            themeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        </script>
    </body>
</html>
