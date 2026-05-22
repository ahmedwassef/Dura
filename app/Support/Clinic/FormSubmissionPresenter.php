<?php

namespace App\Support\Clinic;

use App\Models\FormSubmission;
use Illuminate\Support\Collection;

class FormSubmissionPresenter
{
    public static function forArchive(FormSubmission $submission): array
    {
        $pdf = $submission->archivePdf();

        return [
            'id' => $submission->uuid,
            'type' => $submission->template?->code,
            'typeName' => $submission->template?->name_ar,
            'typeNameEn' => $submission->template?->name_en,
            'patientName' => $submission->patient_name,
            'fileNum' => $submission->file_number,
            'idNumber' => $submission->id_number,
            'doctor' => $submission->doctor_name,
            'branch' => $submission->branch?->code,
            'branchName' => $submission->branch?->name_ar,
            'date' => ($submission->submitted_at ?? $submission->created_at)?->toIso8601String(),
            'grandTotal' => (float) $submission->grand_total,
            'signed' => $submission->is_signed,
            'status' => $submission->status?->value ?? $submission->status,
            'pdfUrl' => $pdf ? route('admin.clinic.submissions.pdf', $submission->uuid) : null,
            'fileName' => $pdf?->file_name,
        ];
    }

    public static function collectionForArchive(Collection $submissions): array
    {
        return $submissions
            ->map(fn (FormSubmission $s) => self::forArchive($s))
            ->values()
            ->all();
    }

    public static function forAdmin(FormSubmission $submission): array
    {
        return [
            ...self::forArchive($submission),
            'creator' => $submission->creator?->name,
        ];
    }

    public static function forEdit(FormSubmission $submission): array
    {
        return [
            'id' => $submission->id,
            'uuid' => $submission->uuid,
            'patient_id' => $submission->patient_id,
            'doctor_id' => $submission->doctor_id,
            'patient_name' => $submission->patient_name,
            'file_number' => $submission->file_number,
            'id_number' => $submission->id_number,
            'doctor_name' => $submission->doctor_name,
            'age_mode' => $submission->age_mode,
            'payload' => $submission->payload,
            'tooth_states' => $submission->tooth_states,
            'plan_teeth' => $submission->plan_teeth,
            'created_at' => $submission->created_at?->format('Y-m-d H:i'),
            'updated_at' => $submission->updated_at?->format('Y-m-d H:i'),
            'creator' => $submission->creator?->name,
            'editor' => $submission->updatedBy?->name,
            'signatures' => $submission->signatures->map(fn ($sig) => [
                'role' => $sig->role,
                'signer_name' => $sig->signer_name,
                'image' => $sig->image_data ?: route('admin.clinic.signatures.image', $sig->id),
            ]),
            'patient' => $submission->patient ? [
                'id' => $submission->patient->id,
                'name' => $submission->patient->name,
                'file_number' => $submission->patient->file_number,
                'phone' => $submission->patient->phone,
                'date_of_birth' => $submission->patient->date_of_birth,
                'nationality_id' => $submission->patient->nationality_id,
            ] : null,
            'doctor' => $submission->doctor ? [
                'id' => $submission->doctor->id,
                'name' => $submission->doctor->name,
                'specialty' => $submission->doctor->specialty,
            ] : null,
            'services' => $submission->treatmentPlanItems->map(fn ($item) => [
                'code' => $item->service_code,
                'name_ar' => $item->service_name_ar,
                'name_en' => $item->service_name_en,
                'price' => (float) $item->unit_price,
                'category' => $item->category,
                'tooth' => $item->tooth,
                'quantity' => $item->quantity,
                'discount' => (float) $item->discount_amount,
                'total' => (float) $item->total_price,
            ]),
        ];
    }

    public static function collectionForAdmin(Collection $submissions): array
    {
        return $submissions
            ->map(fn (FormSubmission $s) => self::forAdmin($s))
            ->values()
            ->all();
    }
}
