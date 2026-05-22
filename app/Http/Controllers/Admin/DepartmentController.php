<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Contracts\Repositories\DepartmentRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->authorizeResource(Department::class, 'department');
    }

    public function index()
    {
        return Inertia::render('admin/departments/Index', [
            'departments' => $this->departmentRepository->getActiveDepartments(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:departments,code',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $this->departmentRepository->create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', __('Department created successfully.'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:departments,code,' . $department->id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $this->departmentRepository->update($department, $validated);

        return redirect()->route('admin.departments.index')
            ->with('success', __('Department updated successfully.'));
    }

    public function destroy(Department $department)
    {
        $this->departmentRepository->delete($department);

        return redirect()->route('admin.departments.index')
            ->with('success', __('Department deleted successfully.'));
    }
}
