<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users' => ['view', 'create', 'edit', 'delete', 'manage-status'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'departments' => ['view', 'create', 'edit', 'delete'],
            'branches' => ['view', 'create', 'edit', 'delete'],
            'reports' => ['view', 'export'],
            'settings' => ['view', 'update'],
            'dashboard' => ['view-all-branches'],
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create super-admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // Create branch-admin role
        $branchAdminRole = Role::firstOrCreate(['name' => 'branch-admin', 'guard_name' => 'web']);
        $branchAdminRole->syncPermissions([
            'users.view', 'users.create', 'users.edit',
            'departments.view',
            'reports.view',
        ]);
    }
}
