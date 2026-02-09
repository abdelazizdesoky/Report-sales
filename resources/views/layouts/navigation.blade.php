<div class="flex flex-col h-full bg-slate-800 dark:bg-slate-900">
    <!-- Brand Header -->
    <div class="px-6 py-6 border-b border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-black text-white leading-tight">{{ \App\Models\Setting::first()?->company_name ?? 'شركة' }}</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">PREMIUM DASHBOARD</p>
            </div>
        </div>
    </div>

    <!-- Navigation Area -->
    <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto custom-scrollbar overflow-x-hidden">
        <!-- Main Section -->
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>لوحة التحكم</span>
            </a>
        </div>

        <!-- System Management Section -->
        <div class="space-y-1">
            <p class="sidebar-section-title">إدارة النظام</p>
            @can('view users')
            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>إدارة المستخدمين</span>
            </a>
            @endcan
            
            @can('manage report visibility')
            <a href="{{ route('role-permissions.index') }}" class="sidebar-link {{ request()->routeIs('role-permissions.*') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                <span>مصفوفة الصلاحيات</span>
            </a>
            @endcan

            @can('view settings')
            <a href="{{ route('settings.edit') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                <span>الإعدادات</span>
            </a>
            @endcan

            @can('view users')
            <a href="{{ route('salesmen-sync.index') }}" class="sidebar-link {{ request()->routeIs('salesmen-sync.*') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>إدارة المندوبين</span>
            </a>
            @endcan
        </div>

        <!-- Reports Section -->
        <div class="space-y-1">
            <p class="sidebar-section-title">التقارير</p>
            @can('view reports')
            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>التقارير </span>
            </a>
            @endcan
        </div>
    </nav>

    <!-- User Profile Section -->
    <div class="px-4 py-4 border-t border-slate-700/50">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-700/30 hover:bg-slate-700/50 transition-colors cursor-pointer">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                {{ mb_substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ Auth::user()->roles->first()?->name ?? 'مستخدم' }}</p>
            </div>
        </div>
    </div>
</div>
