<?php

namespace App\Repositories;

use App\Contracts\Repositories\PatientRepositoryInterface;
use App\Models\Patient;

class PatientRepository implements PatientRepositoryInterface
{
    public function findOrCreate(int $branchId, array $attributes): Patient
    {
        $query = Patient::query()->where('branch_id', $branchId);

        if (! empty($attributes['id'])) {
            $existing = (clone $query)->find($attributes['id']);
            if ($existing) {
                $existing->update(array_filter($attributes));
                return $existing->fresh();
            }
        }

        if (! empty($attributes['file_number'])) {
            $existing = (clone $query)
                ->where('file_number', $attributes['file_number'])
                ->first();

            if ($existing) {
                $existing->update(array_filter($attributes));

                return $existing->fresh();
            }
        }

        if (! empty($attributes['id_number'])) {
            $existing = (clone $query)
                ->where('id_number', $attributes['id_number'])
                ->first();

            if ($existing) {
                $existing->update(array_filter($attributes));

                return $existing->fresh();
            }
        }

        return Patient::query()->create([
            'branch_id' => $branchId,
            'name' => $attributes['name'],
            'id_number' => $attributes['id_number'] ?? null,
            'file_number' => $attributes['file_number'] ?? null,
            'phone' => $attributes['phone'] ?? null,
            'nationality_id' => $attributes['nationality_id'] ?? null,
            'date_of_birth' => $attributes['date_of_birth'] ?? null,
            'sex' => $attributes['sex'] ?? null,
            'address' => $attributes['address'] ?? null,
        ]);
    }
}
