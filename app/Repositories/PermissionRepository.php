<?php

namespace App\Repositories;

use App\Contracts\Repositories\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return Permission::all();
    }
}
