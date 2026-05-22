<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use App\Contracts\Repositories\BranchRepositoryInterface;
use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Services\Admin\RoleService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected RoleService $roleService,
        protected BranchRepositoryInterface $branchRepository,
        protected DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'branch_id', 'department_id', 'status']);
        
        // Non-super-admins only see their branch
        if (!$request->user()->isSuperAdmin()) {
            $filters['branch_id'] = $request->user()->branch_id;
        }

        return Inertia::render('admin/users/Index', [
            'users' => $this->userService->getPaginatedUsers($filters),
            'filters' => $filters,
            'branches' => $request->user()->isSuperAdmin() ? $this->branchRepository->getActiveBranches() : [],
            'departments' => $this->departmentRepository->getActiveDepartments(),
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/users/Form', [
            'branches' => $this->branchRepository->getActiveBranches(),
            'departments' => $this->departmentRepository->getActiveDepartments(),
            'roles' => $this->roleService->getAllRoles(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->createUser($request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', __('User created successfully.'));
    }

    public function edit(User $user)
    {
        return Inertia::render('admin/users/Form', [
            'user' => $user->load('roles'),
            'branches' => $this->branchRepository->getActiveBranches(),
            'departments' => $this->departmentRepository->getActiveDepartments(),
            'roles' => $this->roleService->getAllRoles(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', __('User updated successfully.'));
    }

    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);

        return redirect()->route('admin.users.index')
            ->with('success', __('User deleted successfully.'));
    }
}
