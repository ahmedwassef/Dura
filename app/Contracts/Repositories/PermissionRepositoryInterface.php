<?php

namespace App\Contracts\Repositories;

use Illuminate\Support\Collection;

interface PermissionRepositoryInterface
{
    /**
     * Get all permissions.
     *
     * @return Collection
     */
    public function getAll(): Collection;
}
