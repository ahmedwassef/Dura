<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Services\Clinic\ClinicService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClinicController extends Controller
{
    /**
     * @var ClinicService
     */
    protected $clinicService;

    /**
     * ClinicController constructor.
     *
     * @param ClinicService $clinicService
     */
    public function __construct(ClinicService $clinicService)
    {
        $this->clinicService = $clinicService;
    }

    /**
     * Display branch selection page.
     *
     * @return Response
     */
    public function branchSelect(): Response
    {
        return Inertia::render('clinic/BranchSelect', $this->clinicService->getBranchSelectionData());
    }

    /**
     * Display role selection page.
     *
     * @param Request $request
     * @return Response
     */
    public function roleSelect(Request $request): Response
    {
        return Inertia::render('clinic/RoleSelect', [
            'branch' => $request->query('branch', 'zulfi'),
        ]);
    }

    /**
     * Display clinic home page.
     *
     * @return Response
     */
    public function home(): Response
    {
        return Inertia::render('clinic/Home', $this->clinicService->getHomeData());
    }

    /**
     * Display dental index page.
     *
     * @return Response
     */
    public function dental(): Response
    {
        return Inertia::render('clinic/dental/Index');
    }

    /**
     * Display dermatology index page.
     *
     * @return Response
     */
    public function dermatology(): Response
    {
        return Inertia::render('clinic/dermatology/Index');
    }

    /**
     * Display category show page.
     *
     * @param string $category
     * @return Response
     */
    public function category(string $category): Response
    {
        return Inertia::render('clinic/category/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Display archive index page.
     *
     * @return Response
     */
    public function archive(Request $request): Response
    {
        return Inertia::render('clinic/archive/Index', $this->clinicService->getArchiveData(
            $request->string('search')->toString() ?: null,
            $request->input('branch_id'),
            $request->input('form_id')
        ));
    }

    /**
     * Display form records (clinic admin panel).
     */
    public function records(Request $request): Response
    {
        return Inertia::render('clinic/admin/Index', $this->clinicService->getAdminData(
            $request->string('search')->toString() ?: null,
        ));
    }

    /**
     * Display specific form page.
     *
     * @param string $form
     * @return Response
     */
    public function form(string $form): Response
    {
        $data = $this->clinicService->getFormTemplate($form);

        return Inertia::render('clinic/forms/Show', [
            'form' => $form,
            'template' => $data,
        ]);
    }

    public function editSubmission(string $uuid): Response
    {
        $submission = $this->clinicService->getSubmissionForEdit($uuid);

        return Inertia::render('clinic/forms/Show', [
            'form' => $submission['template']['code'],
            'template' => $submission['template'],
            'initialSubmission' => $submission['data'],
        ]);
    }
}
