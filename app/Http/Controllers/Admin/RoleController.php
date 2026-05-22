<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Services\Admin\RoleService;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {
        // Spatie Role model doesn't have a default policy, we handle it via middleware or manual check
    }

    public function index()
    {
        $this->authorize('roles.view');

        return Inertia::render('admin/roles/Index', [
            'roles' => $this->roleService->getAllRoles(),
            'permissions' => $this->roleService->getGroupedPermissions(),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $this->roleService->createRole($request->validated());

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role created successfully.'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->roleService->updateRole($role, $request->validated());

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role updated successfully.'));
    }

    public function destroy(Role $role)
    {
        $this->authorize('roles.delete');

        if ($role->name === 'super-admin') {
            return redirect()->back()->with('error', __('Cannot delete super-admin role.'));
        }

        $this->roleService->deleteRole($role);

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role deleted successfully.'));
    }
}
