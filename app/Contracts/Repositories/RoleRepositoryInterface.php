<?php

namespace App\Contracts\Repositories;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;

interface RoleRepositoryInterface
{
    /**
     * Get all roles.
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Create a new role.
     *
     * @param array $data
     * @return Role
     */
    public function create(array $data): Role;

    /**
     * Update an existing role.
     *
     * @param Role $role
     * @param array $data
     * @return Role
     */
    public function update(Role $role, array $data): Role;

    /**
     * Delete a role.
     *
     * @param Role $role
     * @return bool|null
     */
    public function delete(Role $role): ?bool;
}
