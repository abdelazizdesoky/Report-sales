<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        if (auth()->user()->cannot('manage report visibility')) {
            abort(403);
        }
        $roles = Role::where('name', '!=', 'Admin')->get();
        $permissions = Permission::all();
        $reports = Report::all();
        
        return view('role_permissions.index', compact('roles', 'permissions', 'reports'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'permissions' => 'array',
            'reports' => 'array',
        ]);

        $roles = Role::where('name', '!=', 'Admin')->get();
        foreach ($roles as $role) {
            // Sync System Permissions
            $assignedPermissions = $request->input("permissions.{$role->id}", []);
            $role->syncPermissions($assignedPermissions);

            // Sync Report Visibility (Custom Pivot)
            $assignedReports = $request->input("reports.{$role->id}", []);
            $role->belongsToMany(Report::class, 'role_reports')->sync($assignedReports);
        }

        return back()->with('status', 'permissions-updated');
    }
}
