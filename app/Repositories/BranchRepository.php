<?php

namespace App\Repositories;

use App\Contracts\Repositories\BranchRepositoryInterface;
use App\Models\Branch;
use Illuminate\Support\Collection;

class BranchRepository implements BranchRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getActiveBranches(): Collection
    {
        return Branch::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function findByCode(string $code): ?Branch
    {
        return Branch::query()->where('code', $code)->first();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    /**
     * @inheritDoc
     */
    public function update(Branch $branch, array $data): Branch
    {
        $branch->update($data);
        return $branch;
    }

    /**
     * @inheritDoc
     */
    public function delete(Branch $branch): ?bool
    {
        return $branch->delete();
    }
}
