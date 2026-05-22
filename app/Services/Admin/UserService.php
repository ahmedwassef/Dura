<?php

namespace App\Services\Admin;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated($filters, $perPage);
    }

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function updateUser(User $user, array $data): User
    {
        return $this->userRepository->update($user, $data);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}
