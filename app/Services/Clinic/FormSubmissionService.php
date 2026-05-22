<?php

namespace App\Services\Clinic;

use App\Contracts\Repositories\BranchRepositoryInterface;
use App\Contracts\Repositories\FormSubmissionRepositoryInterface;
use App\Contracts\Repositories\FormTemplateRepositoryInterface;
use App\Contracts\Repositories\ServiceCatalogRepositoryInterface;
use App\Data\Clinic\StoreFormSubmissionData;
use App\Enums\FormSubmissionStatus;
use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormSubmissionService
{
    public function __construct(
        protected FormSubmissionRepositoryInterface $submissionRepository,
        protected FormTemplateRepositoryInterface $templateRepository,
        protected BranchRepositoryInterface $branchRepository,
        protected ServiceCatalogRepositoryInterface $serviceCatalogRepository,
        protected PatientService $patientService,
    ) {}

    public function findByUuid(string $uuid): ?FormSubmission
    {
        return $this->submissionRepository->findByUuid($uuid);
    }

    public function searchServices(?string $query): Collection
    {
        return $this->serviceCatalogRepository->search($query);
    }

    public function store(StoreFormSubmissionData $data, ?User $creator = null): FormSubmission
    {
        $branch = $this->branchRepository->findByCode($data->branchCode);

        if (! $branch) {
            throw new ModelNotFoundException("Branch {$data->branchCode} not found.");
        }

        $template = $this->templateRepository->findByCode($data->formCode);

        if (! $template) {
            throw new ModelNotFoundException("Form template {$data->formCode} not found.");
        }

        return DB::transaction(function () use ($data, $creator, $branch, $template) {
            $patient = $this->patientService->findOrCreateFromSubmission($branch->id, $data);

            $status = $this->resolveStatus($data);

            $attributes = [
                'form_template_id' => $template->id,
                'patient_id' => $patient->id,
                'doctor_id' => $data->doctorId,
                'branch_id' => $branch->id,
                'created_by' => $creator?->id,
                'patient_name' => $data->patientName,
                'id_number' => $data->idNumber,
                'file_number' => $data->fileNumber,
                'doctor_name' => $data->doctorName,
                'language' => $data->language,
                'status' => $status,
                'grand_total' => $data->grandTotal,
                'is_signed' => $data->isSigned,
                'signed_at' => $data->isSigned ? now() : null,
                'submitted_at' => now(),
                'payload' => $data->payload,
                'tooth_states' => $data->toothStates ?: null,
                'plan_teeth' => $data->planTeeth ?: null,
                'age_mode' => $data->ageMode,
                'extra_discount' => $data->extraDiscount,
                'extra_discount_type' => $data->extraDiscountType,
                'specialty_breakdown' => $data->specialtyBreakdown,
            ];

            if ($data->uuid && $existing = $this->submissionRepository->findByUuid($data->uuid)) {
                $attributes['updated_by'] = $creator?->id;
                unset($attributes['created_by']);
                $existing->update($attributes);
                $submission = $existing;
            } else {
                $submission = $this->submissionRepository->create($attributes);
            }

            if ($data->formCode === 'dental' && count($data->services) > 0) {
                $this->syncTreatmentPlanItems($submission, $data->services);
            }

            $this->syncSignatures($submission, $data->signatures);
            $this->storePdf($submission, $data->pdfBase64);

            return $submission->fresh(['template', 'branch', 'treatmentPlanItems', 'signatures', 'media']);
        });
    }

    protected function resolveStatus(StoreFormSubmissionData $data): FormSubmissionStatus
    {
        if ($data->formCode === 'medreport' && ($data->payload['needs_admin_stamp'] ?? false)) {
            return FormSubmissionStatus::PendingAdmin;
        }

        if ($data->formCode === 'dental' && $data->extraDiscount > 0) {
            return FormSubmissionStatus::PendingDiscountReview;
        }

        return FormSubmissionStatus::Completed;
    }

    /**
     * @param  array<int, array<string, mixed>>  $services
     */
    protected function syncTreatmentPlanItems(FormSubmission $submission, array $services): void
    {
        $submission->treatmentPlanItems()->delete();

        foreach ($services as $index => $service) {
            $qty = max(1, (int) ($service['qty'] ?? 1));
            $unitPrice = (float) ($service['price'] ?? $service['unit_price'] ?? 0);
            $discount = (float) ($service['discount'] ?? 0);
            $discountType = $service['discountType'] ?? $service['discount_type'] ?? 'amount';
            $lineTotal = $this->calculateLineTotal($unitPrice, $qty, $discount, $discountType);

            $submission->treatmentPlanItems()->create([
                'service_code' => $service['code'] ?? null,
                'name_ar' => $service['ar'] ?? $service['name_ar'] ?? '',
                'name_en' => $service['en'] ?? $service['name_en'] ?? null,
                'category' => $service['category'] ?? null,
                'tooth_number' => $service['tooth'] ?? null,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'discount_type' => $discountType,
                'line_total' => $lineTotal,
                'sort_order' => $index,
            ]);
        }
    }

    protected function calculateLineTotal(float $unitPrice, int $qty, float $discount, string $discountType): float
    {
        $base = $unitPrice * $qty;
        $deduction = $discountType === 'percent'
            ? $base * ($discount / 100)
            : $discount;

        return max(0, round($base - min($deduction, $base), 2));
    }

    /**
     * @param  array<int, array{role: string, signer_name?: string, image?: string}>  $signatures
     */
    protected function syncSignatures(FormSubmission $submission, array $signatures): void
    {
        foreach ($signatures as $signature) {
            $role = $signature['role'];
            $image = $signature['image'] ?? null;

            // If image is empty, user cleared the signature
            if (empty($image)) {
                $submission->signatures()->where('role', $role)->delete();
                continue;
            }

            // If it's a URL, it's an existing signature already in the database
            // Note: Now we also check if it starts with 'data:' for base64
            if (str_starts_with($image, 'http')) {
                continue;
            }

            // If it's a new Base64 signature, delete the old one and create new
            $submission->signatures()->where('role', $role)->delete();

            $submission->signatures()->create([
                'role' => $role,
                'signer_name' => $signature['signer_name'] ?? null,
                'image_data' => $image,
                'signed_at' => now(),
            ]);
        }
    }

    protected function storePdf(FormSubmission $submission, ?string $pdfBase64): void
    {
        if (! $pdfBase64) {
            return;
        }

        $binary = $this->decodeBase64($pdfBase64);

        if ($binary === null) {
            return;
        }

        $fileName = Str::slug($submission->patient_name).'-'.($submission->file_number ?: $submission->uuid).'.pdf';

        $submission
            ->addMediaFromString($binary)
            ->usingFileName($fileName)
            ->withCustomProperties(['source' => 'client_pdf'])
            ->toMediaCollection(FormSubmission::MEDIA_ARCHIVE_PDF);
    }

    protected function attachBase64Image(object $model, string $dataUri, string $collection): void
    {
        $binary = $this->decodeBase64($dataUri);

        if ($binary === null) {
            return;
        }

        $model
            ->addMediaFromString($binary)
            ->usingFileName('signature-'.Str::uuid().'.png')
            ->toMediaCollection($collection);
    }

    protected function decodeBase64(string $data): ?string
    {
        if (str_contains($data, ',')) {
            $data = substr($data, strpos($data, ',') + 1);
        }

        $decoded = base64_decode($data, true);

        return $decoded !== false ? $decoded : null;
    }
}
