@php
    $role = $user->roles->first()?->name ?? 'مستخدم';
    
    // User requested to hide "User", "Supervisor" and "Salesman" roles from the main tree
    // Note: Salesmen are handled via managedSalesmen relationship under Area Managers
    if (in_array($role, ['مستخدم', 'Salesman', 'مندوب', 'Supervisor'])) {
        return;
    }

    $roleLabel = match($role) {
        'General Manager', 'مدير عام', 'GM' => 'مدير عام',
        'Manager', 'مدير قطاع', 'Sector Manager' => 'مدير قطاع',
        'Area Manager', 'مدير منطقة' => 'مدير منطقة',
        'Supervisor', 'سوبر فيزور' => 'سوبر فيزور',
        'Coordinator', 'منسق' => 'منسق',
        'Specialist', 'أخصائي' => 'أخصائي',
        'Admin', 'مدير مبيعات', 'مدير نظام' => 'مدير نظام',
        'Salesman', 'مندوب' => 'مندوب',
        default => $role // Fallback to raw role name
    };
    $roleClass = match($role) {
        'General Manager', 'مدير عام', 'GM' => 'role-gm',
        'Manager', 'مدير قطاع', 'Sector Manager' => 'role-manager',
        'Area Manager', 'مدير منطقة' => 'role-area',
        'Supervisor', 'سوبر فيزور' => 'role-supervisor',
        'Coordinator', 'منسق' => 'role-coordinator',
        'Specialist', 'أخصائي' => 'role-specialist',
        'Admin', 'مدير مبيعات', 'مدير نظام' => 'role-admin',
        default => 'role-salesman'
    };
    
    $subCount = $user->subordinates->count();
    $salesCount = $user->managedSalesmen->count();

    $subordinates = $user->subordinates->filter(function($u) {
        $r = $u->roles->first()?->name;
        // Keep checking for nested staff just in case, though header has most of them
        return !in_array($r, ['مستخدم', 'Supervisor']);
    });

    $isAreaManager = in_array($role, ['Area Manager', 'مدير منطقة']);
    $showSalesData = ($isAreaManager && $user->managedSalesmen->isNotEmpty());
    
    // Determine if we should stack subordinates vertically
    // User requested Salesmen (Mandib) vertically under Area Managers.
    $useVerticalStack = $isAreaManager;
@endphp

<li>
    <div class="node-card">
        <span class="role-badge {{ $roleClass }}">{{ $roleLabel }}</span>
        <span class="name-label">{{ $user->name }}</span>
        
        @if($user->region)
            <span class="text-[9px] block mt-1 font-bold" style="color: var(--h-text-muted);">{{ $user->region }}</span>
        @endif

        @if($subCount > 0 || $salesCount > 0)
            <span class="salesman-count">
                @if($subCount > 0) {{ $subCount }} م @endif
                @if($subCount > 0 && $salesCount > 0) | @endif
                @if($salesCount > 0) {{ $salesCount }} ن @endif
            </span>
        @endif
    </div>

    @if($subordinates->isNotEmpty() || $showSalesData)
        <ul class="{{ $useVerticalStack ? 'vertical-stack' : '' }}">
            {{-- Recursively render all subordinates --}}
            @foreach($subordinates as $subordinate)
                @include('reports.hierarchy.node', ['user' => $subordinate])
            @endforeach

            {{-- Render Salesmen if this is an Area Manager (Strictly Vertical) --}}
            @if($showSalesData)
                @foreach($user->managedSalesmen as $ms)
                    <li>
                        <div class="node-card role-salesman">
                            <span class="role-badge role-salesman">مندوب</span>
                            <span class="name-label">{{ $ms->salesman_name }}</span>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
    @endif
</li>
