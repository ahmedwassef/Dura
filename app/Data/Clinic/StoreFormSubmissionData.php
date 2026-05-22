<?php

namespace App\Data\Clinic;

readonly class StoreFormSubmissionData
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, array<string, mixed>>  $services
     * @param  array<string, mixed>  $toothStates
     * @param  array<int, int|string>  $planTeeth
     * @param  array<string, mixed>|null  $specialtyBreakdown
     * @param  array<int, array{role: string, signer_name?: string, image?: string}>  $signatures
     */
    public function __construct(
        public ?string $uuid,
        public string $formCode,
        public string $branchCode,
        public ?int $patientId,
        public ?int $doctorId,
        public string $patientName,
        public ?string $idNumber,
        public ?string $fileNumber,
        public ?string $doctorName,
        public string $language,
        public array $payload,
        public float $grandTotal,
        public bool $isSigned,
        public ?string $pdfBase64,
        public array $services = [],
        public array $toothStates = [],
        public array $planTeeth = [],
        public ?string $ageMode = null,
        public float $extraDiscount = 0,
        public string $extraDiscountType = 'amount',
        public ?array $specialtyBreakdown = null,
        public array $signatures = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            uuid: $data['uuid'] ?? null,
            formCode: $data['form_code'],
            branchCode: $data['branch_code'],
            patientId: isset($data['patient_id']) ? (int) $data['patient_id'] : null,
            doctorId: isset($data['doctor_id']) ? (int) $data['doctor_id'] : null,
            patientName: $data['patient_name'],
            idNumber: $data['id_number'] ?? null,
            fileNumber: $data['file_number'] ?? null,
            doctorName: $data['doctor_name'] ?? null,
            language: $data['language'] ?? 'ar',
            payload: $data['payload'] ?? [],
            grandTotal: (float) ($data['grand_total'] ?? 0),
            isSigned: (bool) ($data['is_signed'] ?? false),
            pdfBase64: $data['pdf_base64'] ?? null,
            services: $data['services'] ?? [],
            toothStates: $data['tooth_states'] ?? [],
            planTeeth: $data['plan_teeth'] ?? [],
            ageMode: $data['age_mode'] ?? null,
            extraDiscount: (float) ($data['extra_discount'] ?? 0),
            extraDiscountType: $data['extra_discount_type'] ?? 'amount',
            specialtyBreakdown: $data['specialty_breakdown'] ?? null,
            signatures: $data['signatures'] ?? [],
        );
    }
}
