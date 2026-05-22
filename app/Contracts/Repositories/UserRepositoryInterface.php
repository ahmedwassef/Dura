<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /**
     * Get paginated users with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User;

    /**
     * Delete a user.
     *
     * @param User $user
     * @return bool|null
     */
    public function delete(User $user): ?bool;
}
