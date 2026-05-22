<?php

namespace App\Services\Clinic;

use App\Contracts\Repositories\PatientRepositoryInterface;
use App\Data\Clinic\StoreFormSubmissionData;
use App\Models\Patient;

class PatientService
{
    public function __construct(
        protected PatientRepositoryInterface $patientRepository,
    ) {}

    public function findOrCreateFromSubmission(int $branchId, StoreFormSubmissionData $data): Patient
    {
        return $this->patientRepository->findOrCreate($branchId, [
            'id' => $data->patientId,
            'name' => $data->patientName,
            'id_number' => $data->idNumber,
            'file_number' => $data->fileNumber,
            'phone' => $data->payload['phone'] ?? $data->payload['tel'] ?? null,
            'nationality_id' => $data->payload['nationality_id'] ?? null,
            'date_of_birth' => $data->payload['date_of_birth'] ?? null,
            'sex' => $data->payload['sex'] ?? null,
            'address' => $data->payload['address'] ?? $data->payload['addr'] ?? null,
        ]);
    }
}
