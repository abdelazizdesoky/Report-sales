<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HierarchyController extends Controller
{
    public function index()
    {
        if (auth()->user()->cannot('view hierarchy')) {
            abort(403);
        }

        $users = User::where('is_enabled', true)
            ->with(['roles', 'managedSalesmen', 'subordinates' => function($q) {
                $q->where('is_enabled', true);
            }])
            ->get();

        $enabledUserIds = $users->pluck('id')->toArray();

        // 1. Admins Bucket (Vertical column)
        $admins = $users->filter(fn($user) => $user->hasRole('Admin'));

        // 2. Staff/Coordinators Bucket (Vertical column)
        $staff = $users->filter(fn($user) => $user->hasRole('Coordinator') || $user->hasRole('Specialist'));

        // 3. Operational Tree Roots (General Managers or anyone without a supervisor)
        // We exclude anyone already in Admins or Staff from being a "Root" in the main tree
        $tree = $users->filter(function($user) use ($enabledUserIds) {
            $isStaffOrAdmin = $user->hasRole('Admin') || $user->hasRole('Coordinator') || $user->hasRole('Specialist');
            $noSupervisor = !$user->supervisor_id || !in_array($user->supervisor_id, $enabledUserIds);
            return $noSupervisor && !$isStaffOrAdmin;
        });

        // We still need GMs for titles or specific logic if needed, but the view will use $tree
        $gms = $users->filter(fn($user) => $user->hasRole('General Manager'));

        return view('reports.hierarchy.index', compact('tree', 'admins', 'staff', 'gms'));
    }
}
