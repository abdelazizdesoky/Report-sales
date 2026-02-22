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
        .tree li > .branch-container::before {
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
        
        .branch-container.expanded {
            display: flex !important;
        }
        
        .branch-container.vertical-stack.expanded {
            flex-direction: column !important;
        }

        .chevron-icon.rotated {
            transform: rotate(180deg);
        }

        /* Condensed hint for collapsed parents - "Stacked" cards look */
        .node-card.has-children:not(.expanded-card)::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            margin: 0 auto;
            width: 200px; /* Fixed width for standardized stack */
            height: 100%;
            background: var(--h-card-bg);
            border: 1px solid var(--h-card-border);
            border-radius: 20px;
            z-index: -1;
            box-shadow: var(--h-shadow);
            opacity: 0.7;
            transform: scale(0.96);
        }
        
        .node-card.has-children:not(.expanded-card)::before {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            right: 0;
            margin: 0 auto;
            width: 180px; /* Fixed width for standardized stack */
            height: 100%;
            background: var(--h-card-bg);
            border: 1px solid var(--h-card-border);
            border-radius: 20px;
            z-index: -2;
            box-shadow: var(--h-shadow);
            opacity: 0.4;
            transform: scale(0.92);
        }

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
            padding: 20px 16px;
            border-radius: 20px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 220px; /* Fixed width */
            min-height: 120px; /* Fixed minimum height */
            box-shadow: var(--h-shadow);
            position: relative;
            z-index: 10;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .node-card:hover {
            border-color: var(--h-line);
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .role-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .role-gm { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
        .role-manager { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
        .role-area { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; }
        .role-coordinator { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff; }
        .role-specialist { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; }
        .role-admin { background: linear-gradient(135deg, #64748b, #475569); color: #fff; }
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
            color: #fff;
            margin-top: 8px;
            font-weight: 800;
            background: var(--h-line);
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .branch.expanded {
            display: flex !important;
            opacity: 1 !important;
            scale: 1 !important;
            padding-top: 40px;
        }

        .branch.vertical-stack.expanded {
            flex-direction: column !important;
            padding-top: 20px;
        }

        .chevron-icon.rotated {
            transform: rotate(180deg);
        }

        /* Adjust lines for collapsed children */
        .node-card.has-children-collapsed::after {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: var(--h-bg); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--h-card-border); border-radius: 4px; }
    </style>

    <script>
        function toggleBranch(card) {
            const li = card.closest('li');
            const branch = li.querySelector(':scope > .branch-container');
            const chevron = card.querySelector('.chevron-icon');

            if (branch) {
                const isHidden = branch.style.display === 'none';
                
                if (isHidden) {
                    branch.style.display = 'flex';
                    card.classList.add('expanded-card');
                    if (chevron) chevron.classList.add('rotated');
                } else {
                    branch.style.display = 'none';
                    card.classList.remove('expanded-card');
                    if (chevron) chevron.classList.remove('rotated');
                }
            }
        }
    </script>
</x-app-layout>
