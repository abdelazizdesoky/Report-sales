<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-white dark:text-gray-100 leading-tight text-right">
            {{ __('الهيكل الإداري مبيعات') }}
        </h2>
    </x-slot>

    <div class="h-full">
        <div class="w-full">
            <div class="-m-8 bg-white dark:bg-slate-900 shadow-2xl overflow-auto custom-scrollbar" style="height: calc(100vh - 4rem);">
                <div class="hierarchy-container min-w-max min-h-full">
                    
                    <!-- Strategic Header Layer -->
                    <div class="strategic-header">
                        <div class="strategic-col">
                            <h3 class="col-title" style="color: #64748b;">المديرين</h3>
                            @foreach($admins as $user)
                                <div class="node-card">
                                    <span class="role-badge role-admin">مدير نظام</span>
                                    <span class="name-label">{{ $user->name }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="strategic-col main-tree-start">
                            <h3 class="col-title" style="color: #f59e0b;">الإدارة العامة</h3>
                            <ul class="tree root-level">
                                @foreach($tree as $root)
                                    @include('reports.hierarchy.node', ['user' => $root, 'level' => 0])
                                @endforeach
                            </ul>
                        </div>

                        <div class="strategic-col">
                            <h3 class="col-title" style="color: #8b5cf6;">المنسقين والأخصائيين</h3>
                            @foreach($staff as $user)
                                <div class="node-card">
                                    @php 
                                        $r = $user->roles->first()?->name;
                                        $label = ($r === 'Specialist') ? 'أخصائي' : 'منسق';
                                        $class = ($r === 'Specialist') ? 'role-specialist' : 'role-coordinator';
                                    @endphp
                                    <span class="role-badge {{ $class }}">{{ $label }}</span>
                                    <span class="name-label">{{ $user->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --h-bg: #f8fafc;
            --h-dot: #cbd5e1;
            --h-card-bg: #ffffff;
            --h-card-border: #cbd5e1;
            --h-text: #0f172a;
            --h-text-muted: #64748b;
            --h-line: #6366f1;
            --h-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.1);
        }

        .dark {
            --h-bg: #0f172a;
            --h-dot: #1e293b;
            --h-card-bg: #1e293b;
            --h-card-border: #334155;
            --h-text: #f8fafc;
            --h-text-muted: #94a3b8;
            --h-line: #818cf8;
            --h-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.6);
        }

        .hierarchy-container {
            padding: 40px 60px 800px 60px;
            background-color: var(--h-bg);
            background-image: radial-gradient(circle at 1px 1px, var(--h-dot) 1px, transparent 0);
            background-size: 30px 30px;
            min-height: 100%;
            direction: rtl;
            color: var(--h-text);
            transition: all 0.3s ease;
        }

        /* --- Strategic Header --- */
        .strategic-header {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 120px;
            margin-bottom: 60px;
        }

        .strategic-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            position: relative;
        }

        .col-title {
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* --- Tree Core (High Precision RTL) --- */
        .tree, .tree ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
            position: relative;
        }

        .tree li {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            padding: 40px 10px 0 10px;
            list-style: none;
        }

        /* Vertical Connector from Parent down to Branching Bar */
        .tree li > ul::before {
            content: '';
            position: absolute;
            top: -40px;
            right: 50%;
            width: 0;
            height: 42px; /* Overlap to fix gaps */
            border-right: 2px solid var(--h-line);
            margin-right: -1px;
            z-index: 1;
        }

        /* Horizontal Bridge Bars */
        .tree li::before, .tree li::after {
            content: '';
            position: absolute;
            top: 0;
            width: 50.5%;
            height: 40px;
            border-top: 2px solid var(--h-line);
            z-index: 0;
        }

        /* ::before = RIGHT HALF (0 to 50% from right) */
        .tree li::before {
            right: 0;
        }

        /* ::after = LEFT HALF (50% to 100% from right) + VERTICAL DROP */
        .tree li::after {
            right: 50%;
            border-right: 2px solid var(--h-line);
            margin-right: -1px;
        }

        /* Corner/Edge Logic for RTL */
        .tree li:first-child::before { display: none; }
        .tree li:first-child::after { border-radius: 0 15px 0 0; }
        
        .tree li:last-child::after { border-top: none; }
        .tree li:last-child::before { border-radius: 15px 0 0 0; }

        .tree li:only-child::before, .tree li:only-child::after { display: none; }
        .tree li:only-child { padding-top: 0; }

        /* --- Vertical Column (GM Header) --- */
        .tree.root-level {
            flex-direction: column;
            align-items: center;
        }
        .tree.root-level > li {
            padding: 0 0 40px 0;
        }
        .tree.root-level > li::before, .tree.root-level > li::after { display: none; }
        
        .tree.root-level > li:not(:last-child) > .node-card::after {
            content: '';
            position: absolute;
            bottom: -40px;
            right: 50%;
            height: 42px;
            border-right: 2px solid var(--h-line);
            margin-right: -1px;
            z-index: 1;
        }

        /* --- Vertical Salesman Stack --- */
        .tree ul.vertical-stack {
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }
        .tree ul.vertical-stack::before { height: 22px; top: -20px; }
        
        .tree ul.vertical-stack li {
            padding: 20px 0 0 0;
            width: auto;
        }
        .tree ul.vertical-stack li::before, .tree ul.vertical-stack li::after { display: none; }
        
        .tree ul.vertical-stack li:not(:first-child)::after {
            content: '';
            position: absolute;
            top: -20px;
            right: 50%;
            height: 22px;
            border-right: 2px solid var(--h-line);
            margin-right: -1px;
            display: block;
        }

        /* --- Node Card Styling --- */
        .node-card {
            background: var(--h-card-bg);
            border: 1px solid var(--h-card-border);
            padding: 12px 24px;
            border-radius: 14px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            min-width: 160px;
            max-width: 280px;
            box-shadow: var(--h-shadow);
            position: relative;
            z-index: 10;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .node-card:hover {
            border-color: var(--h-line);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .role-badge {
            font-size: 8px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 6px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .role-gm { background: #d97706; color: #fff; }
        .role-manager { background: #059669; color: #fff; }
        .role-area { background: #2563eb; color: #fff; }
        .role-coordinator { background: #7c3aed; color: #fff; }
        .role-specialist { background: #4f46e5; color: #fff; }
        .role-admin { background: #475569; color: #fff; }
        .role-salesman { 
            background: var(--h-bg); 
            color: var(--h-text-muted); 
            border: 1px dashed var(--h-card-border); 
        }

        .name-label {
            font-size: 14px;
            font-weight: 700;
            color: var(--h-text);
            text-align: center;
            line-height: 1.4;
        }

        .salesman-count {
            font-size: 11px;
            color: var(--h-line);
            margin-top: 6px;
            font-weight: 800;
            background: rgba(99, 102, 241, 0.08);
            padding: 2px 8px;
            border-radius: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: var(--h-bg); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--h-card-border); border-radius: 4px; }
    </style>
</x-app-layout>
