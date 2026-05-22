<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionName::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $reception = Role::findOrCreate(RoleName::Reception->value, 'web');
        $reception->syncPermissions([
            PermissionName::ArchiveView->value,
            PermissionName::ArchiveDownload->value,
            PermissionName::PatientsSearch->value,
        ]);

        $doctor = Role::findOrCreate(RoleName::Doctor->value, 'web');
        $doctor->syncPermissions([
            PermissionName::ArchiveView->value,
            PermissionName::ArchiveDownload->value,
            PermissionName::FormsView->value,
            PermissionName::FormsCreate->value,
            PermissionName::FormsUpdate->value,
            PermissionName::PatientsSearch->value,
        ]);

        $admin = Role::findOrCreate(RoleName::Admin->value, 'web');
        $admin->syncPermissions(
            collect(PermissionName::cases())->map(fn (PermissionName $p) => $p->value)->all(),
        );

        $superAdmin = Role::findOrCreate(RoleName::SuperAdmin->value, 'web');
        $superAdmin->syncPermissions(Permission::all());
    }
}
