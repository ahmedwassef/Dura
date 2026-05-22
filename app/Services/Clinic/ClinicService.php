<?php

namespace App\Services\Clinic;

use App\Contracts\Repositories\BranchRepositoryInterface;
use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Contracts\Repositories\FormSubmissionRepositoryInterface;
use App\Contracts\Repositories\FormTemplateRepositoryInterface;
use App\Support\Clinic\FormSubmissionPresenter;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClinicService
{
    public function __construct(
        protected BranchRepositoryInterface $branchRepository,
        protected FormTemplateRepositoryInterface $formTemplateRepository,
        protected DepartmentRepositoryInterface $departmentRepository,
        protected FormSubmissionRepositoryInterface $formSubmissionRepository,
        protected ClinicSessionService $clinicSession,
    ) {}

    /**
     * Get data for branch selection.
     *
     * @return array
     */
    public function getBranchSelectionData(): array
    {
        return [
            'branches' => $this->branchRepository->getActiveBranches(),
        ];
    }

    /**
     * Get data for clinic home.
     *
     * @return array
     */
    public function getHomeData(): array
    {
        return [
            'departments' => $this->departmentRepository->getAllWithTemplateCount()->map(function ($dept) {
                return [
                    'code' => $dept->code,
                    'name_ar' => $dept->name_ar,
                    'name_en' => $dept->name_en,
                    'templates_count' => $dept->form_templates_count,
                    'description_ar' => $this->getDepartmentDescription($dept->code, 'ar'),
                    'description_en' => $this->getDepartmentDescription($dept->code, 'en'),
                ];
            }),
        ];
    }

    /**
     * Get data for the archive page.
     *
     * @return array
     */
    public function getArchiveData(?string $search = null, ?int $filterBranchId = null, ?int $formTemplateId = null): array
    {
        // For standard clinic users, we might restrict by session branch
        // But for admin/archive view, we allow cross-branch if filter is provided
        $sessionBranchId = $this->clinicSession->resolveBranchId();
        
        $finalBranchId = $filterBranchId ?: $sessionBranchId;

        return [
            'archive' => FormSubmissionPresenter::collectionForArchive(
                $this->formSubmissionRepository->getArchiveSubmissions($finalBranchId, $search, $formTemplateId),
            ),
            'filters' => [
                'branches' => $this->branchRepository->getActiveBranches(),
                'forms' => $this->formTemplateRepository->getAll(),
            ]
        ];
    }

    public function getAdminData(?string $search = null): array
    {
        $branchId = $this->clinicSession->resolveBranchId();

        return [
            'submissions' => FormSubmissionPresenter::collectionForAdmin(
                $this->formSubmissionRepository->getAdminSubmissions($branchId, $search),
            ),
        ];
    }

    /**
     * Get a form template by code.
     *
     * @param string $code
     * @return array
     * @throws ModelNotFoundException
     */
    public function getFormTemplate(string $code): array
    {
        $template = $this->formTemplateRepository->findByCode($code);

        if (!$template) {
            throw new ModelNotFoundException("Form template with code {$code} not found.");
        }

        return [
            'code' => $template->code,
            'name_ar' => $template->name_ar,
            'name_en' => $template->name_en,
            'is_bilingual' => $template->is_bilingual,
            'is_ar_only' => $template->is_ar_only,
            'fields' => $template->fields()->get()->toArray(),
        ];
    }

    /**
     * Get hardcoded department descriptions (could be moved to DB later).
     *
     * @param string $code
     * @param string $lang
     * @return string
     */
    protected function getDepartmentDescription(string $code, string $lang): string
    {
        $descriptions = [
            'dental' => [
                'ar' => 'الخطة العلاجية، السجل الصحي، الخلع، العصب، التركيبات، التقويم',
                'en' => 'Treatment plans, medical history, extractions, RCT, prosthetics, orthodontics',
            ],
            'dermatology' => [
                'ar' => 'عيادة الدكتورة، الأجهزة، الليزر، الفيلر والمورفيس',
                'en' => 'Doctor\'s clinic, devices, laser, fillers, Morpheus',
            ],
        ];

        return $descriptions[$code][$lang] ?? '';
    }

    public function getSubmissionForEdit(string $uuid): array
    {
        $submission = $this->formSubmissionRepository->findByUuid($uuid);

        if (!$submission) {
            throw new ModelNotFoundException("Form submission with UUID {$uuid} not found.");
        }

        $template = $submission->template;

        return [
            'template' => [
                'code' => $template->code,
                'name_ar' => $template->name_ar,
                'name_en' => $template->name_en,
                'is_bilingual' => $template->is_bilingual,
                'is_ar_only' => $template->is_ar_only,
                'fields' => $template->fields()->get()->toArray(),
            ],
            'data' => FormSubmissionPresenter::forEdit($submission),
        ];
    }
}
