<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return Role::with(['permissions'])->get();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    /**
     * @inheritDoc
     */
    public function update(Role $role, array $data): Role
    {
        $role->update([
            'name' => $data['name'] ?? $role->name,
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    /**
     * @inheritDoc
     */
    public function delete(Role $role): ?bool
    {
        return $role->delete();
    }
}
