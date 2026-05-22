<?php

namespace App\Contracts\Repositories;

use App\Models\FormSubmission;
use Illuminate\Support\Collection;

interface FormSubmissionRepositoryInterface
{
    public function create(array $attributes): FormSubmission;

    public function findByUuid(string $uuid): ?FormSubmission;

    public function getArchiveSubmissions(?int $branchId = null, ?string $search = null, ?int $formTemplateId = null, int $limit = 100): Collection;

    public function getAdminSubmissions(?int $branchId = null, ?string $search = null, ?int $formTemplateId = null, int $limit = 100): Collection;
}
