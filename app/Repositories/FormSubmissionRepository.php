<?php

namespace App\Repositories;

use App\Contracts\Repositories\FormSubmissionRepositoryInterface;
use App\Models\FormSubmission;
use Illuminate\Support\Collection;

class FormSubmissionRepository implements FormSubmissionRepositoryInterface
{
    public function create(array $attributes): FormSubmission
    {
        return FormSubmission::query()->create($attributes);
    }

    public function findByUuid(string $uuid): ?FormSubmission
    {
        return FormSubmission::query()
            ->with(['template', 'branch', 'creator', 'treatmentPlanItems', 'signatures', 'media'])
            ->where('uuid', $uuid)
            ->first();
    }

    public function getArchiveSubmissions(?int $branchId = null, ?string $search = null, ?int $formTemplateId = null, int $limit = 100): Collection
    {
        return $this->baseQuery($branchId, $search, $formTemplateId)
            ->limit($limit)
            ->get();
    }

    public function getAdminSubmissions(?int $branchId = null, ?string $search = null, ?int $formTemplateId = null, int $limit = 100): Collection
    {
        return $this->baseQuery($branchId, $search, $formTemplateId)
            ->with('creator')
            ->limit($limit)
            ->get();
    }

    protected function baseQuery(?int $branchId, ?string $search, ?int $formTemplateId = null)
    {
        $query = FormSubmission::query()
            ->with(['template', 'branch', 'media'])
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('file_number', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%")
                    ->orWhere('doctor_name', 'like', "%{$search}%");
            });
        }
        
        if ($formTemplateId) {
            $query->where('form_template_id', $formTemplateId);
        }

        return $query;
    }
}
