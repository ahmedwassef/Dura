<?php

namespace App\Http\Requests\Clinic;

use App\Data\Clinic\StoreFormSubmissionData;
use Illuminate\Foundation\Http\FormRequest;

class StoreFormSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\FormSubmission::class) ?? true;
    }

    public function rules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'uuid'],
            'form_code' => ['required', 'string', 'max:64'],
            'branch_code' => ['required', 'string', 'max:32'],
            'patient_name' => ['required', 'string', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:64'],
            'file_number' => ['nullable', 'string', 'max:64'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'in:ar,en'],
            'payload' => ['nullable', 'array'],
            'grand_total' => ['nullable', 'numeric', 'min:0'],
            'is_signed' => ['nullable', 'boolean'],
            'pdf_base64' => ['nullable', 'string'],
            'services' => ['nullable', 'array'],
            'tooth_states' => ['nullable', 'array'],
            'plan_teeth' => ['nullable', 'array'],
            'age_mode' => ['nullable', 'string', 'in:adult,child'],
            'extra_discount' => ['nullable', 'numeric', 'min:0'],
            'extra_discount_type' => ['nullable', 'string', 'in:amount,percent'],
            'specialty_breakdown' => ['nullable', 'array'],
            'signatures' => ['nullable', 'array'],
            'signatures.*.role' => ['required_with:signatures', 'string', 'max:64'],
            'signatures.*.image' => ['nullable', 'string'],
            'signatures.*.signer_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toData(): StoreFormSubmissionData
    {
        return StoreFormSubmissionData::fromArray($this->validated());
    }
}
