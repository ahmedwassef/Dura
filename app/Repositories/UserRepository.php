<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['branch_id'] ?? null, function ($query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($filters['department_id'] ?? null, function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->with(['branch', 'department', 'roles'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'branch_id' => $data['branch_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'display_name_ar' => $data['display_name_ar'] ?? $data['name'],
            'status' => $data['status'] ?? 'active',
        ]);

        if (isset($data['roles'])) {
            $user->assignRole($data['roles']);
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function update(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->phone,
            'branch_id' => $data['branch_id'] ?? $user->branch_id,
            'department_id' => $data['department_id'] ?? $user->department_id,
            'display_name_ar' => $data['display_name_ar'] ?? $user->display_name_ar,
            'status' => $data['status'] ?? $user->status,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function delete(User $user): ?bool
    {
        return $user->delete();
    }
}
