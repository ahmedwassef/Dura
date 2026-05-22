<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\PermissionRepositoryInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;

class RoleService
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
        protected PermissionRepositoryInterface $permissionRepository
    ) {}

    public function getAllRoles(): Collection
    {
        return $this->roleRepository->getAll();
    }

    public function getAllPermissions(): Collection
    {
        return $this->permissionRepository->getAll();
    }

    public function createRole(array $data): Role
    {
        return $this->roleRepository->create($data);
    }

    public function updateRole(Role $role, array $data): Role
    {
        return $this->roleRepository->update($role, $data);
    }

    public function deleteRole(Role $role): bool
    {
        return $this->roleRepository->delete($role);
    }

    public function getGroupedPermissions(): Collection
    {
        return $this->permissionRepository->getAll()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
    }
}
