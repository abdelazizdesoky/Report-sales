<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view settings',
            'edit settings',
            'view reports',
            'manage report visibility'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions(['view users', 'edit users', 'view settings', 'view reports']);

        $generalManager = Role::firstOrCreate(['name' => 'General Manager', 'guard_name' => 'web']);
        $generalManager->syncPermissions(['view users', 'view reports']);

        $areaManager = Role::firstOrCreate(['name' => 'Area Manager', 'guard_name' => 'web']);
        $areaManager->syncPermissions(['view users', 'view reports']);

        $supervisor = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $supervisor->syncPermissions(['view users', 'view reports']);

        $coordinator = Role::firstOrCreate(['name' => 'Coordinator', 'guard_name' => 'web']);
        $coordinator->syncPermissions(['view reports']);

        $specialist = Role::firstOrCreate(['name' => 'Specialist', 'guard_name' => 'web']);
        $specialist->syncPermissions(['view reports']);

        $employee = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        $employee->syncPermissions(['view reports']);
    }
}
