<?php

namespace App\Contracts\Repositories;

use App\Models\Branch;
use Illuminate\Support\Collection;

interface BranchRepositoryInterface
{
    /**
     * Get all active branches.
     *
     * @return Collection
     */
    public function getActiveBranches(): Collection;

    public function findByCode(string $code): ?\App\Models\Branch;

    /**
     * Create a new branch.
     *
     * @param array $data
     * @return Branch
     */
    public function create(array $data): Branch;

    /**
     * Update an existing branch.
     *
     * @param Branch $branch
     * @param array $data
     * @return Branch
     */
    public function update(Branch $branch, array $data): Branch;

    /**
     * Delete a branch.
     *
     * @param Branch $branch
     * @return bool|null
     */
    public function delete(Branch $branch): ?bool;
}
