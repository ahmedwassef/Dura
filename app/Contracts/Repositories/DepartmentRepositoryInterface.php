<?php

namespace App\Contracts\Repositories;

use App\Models\Department;
use Illuminate\Support\Collection;

interface DepartmentRepositoryInterface
{
    /**
     * Get all active departments.
     *
     * @return Collection
     */
    public function getActiveDepartments(): Collection;

    /**
     * Get all departments with their form templates count.
     *
     * @return Collection
     */
    public function getAllWithTemplateCount(): Collection;

    /**
     * Create a new department.
     *
     * @param array $data
     * @return Department
     */
    public function create(array $data): Department;

    /**
     * Update an existing department.
     *
     * @param Department $department
     * @param array $data
     * @return Department
     */
    public function update(Department $department, array $data): Department;

    /**
     * Delete a department.
     *
     * @param Department $department
     * @return bool|null
     */
    public function delete(Department $department): ?bool;
}
