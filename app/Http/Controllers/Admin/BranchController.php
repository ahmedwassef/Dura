<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Contracts\Repositories\BranchRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function __construct(
        protected BranchRepositoryInterface $branchRepository
    ) {
        $this->authorizeResource(Branch::class, 'branch');
    }

    public function index()
    {
        return Inertia::render('admin/branches/Index', [
            'branches' => $this->branchRepository->getActiveBranches(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:branches,code',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $this->branchRepository->create($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', __('Branch created successfully.'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:branches,code,' . $branch->id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $this->branchRepository->update($branch, $validated);

        return redirect()->route('admin.branches.index')
            ->with('success', __('Branch updated successfully.'));
    }

    public function destroy(Branch $branch)
    {
        $this->branchRepository->delete($branch);

        return redirect()->route('admin.branches.index')
            ->with('success', __('Branch deleted successfully.'));
    }
}
