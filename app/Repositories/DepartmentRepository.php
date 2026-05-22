<?php

namespace App\Repositories;

use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Models\Department;
use Illuminate\Support\Collection;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getActiveDepartments(): Collection
    {
        return Department::where('status', 'active')->get();
    }

    /**
     * @inheritDoc
     */
    public function getAllWithTemplateCount(): Collection
    {
        return Department::query()
            ->withCount('formTemplates')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Department
    {
        return Department::create($data);
    }

    /**
     * @inheritDoc
     */
    public function update(Department $department, array $data): Department
    {
        $department->update($data);
        return $department;
    }

    /**
     * @inheritDoc
     */
    public function delete(Department $department): ?bool
    {
        return $department->delete();
    }
}
