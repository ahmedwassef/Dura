<?php

namespace App\Contracts\Repositories;

use App\Models\Patient;

interface PatientRepositoryInterface
{
    public function findOrCreate(int $branchId, array $attributes): Patient;
}
