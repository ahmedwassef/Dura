<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\StoreFormSubmissionRequest;
use App\Services\Clinic\FormSubmissionService;
use App\Support\Clinic\FormSubmissionPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormSubmissionController extends Controller
{
    public function __construct(
        protected FormSubmissionService $formSubmissionService,
    ) {}

    public function store(StoreFormSubmissionRequest $request): JsonResponse
    {
        $submission = $this->formSubmissionService->store(
            $request->toData(),
            $request->user(),
        );

        return response()->json([
            'message' => 'Form saved successfully.',
            'submission' => FormSubmissionPresenter::forArchive($submission),
        ], 201);
    }

    public function pdf(string $uuid): StreamedResponse
    {
        $submission = $this->formSubmissionService->findByUuid($uuid);

        abort_unless($submission, 404);

        $this->authorize('download', $submission);

        $media = $submission->archivePdf();

        abort_unless($media, 404);

        return response()->streamDownload(
            fn () => print ($media->stream()),
            $media->file_name,
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function signatureImage(\App\Models\Signature $signature): StreamedResponse
    {
        $media = $signature->signatureImage();

        abort_unless($media, 404);

        return response()->streamDownload(
            fn () => print ($media->stream()),
            $media->file_name,
            [
                'Content-Type' => $media->mime_type,
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            ],
        );
    }

    public function searchServices(Request $request): JsonResponse
    {
        $query = $request->string('q')->toString() ?: null;

        $services = $this->formSubmissionService->searchServices($query);

        return response()->json([
            'services' => $services->map(fn ($item) => [
                'code' => $item->code,
                'name_ar' => $item->name_ar,
                'name_en' => $item->name_en,
                'price' => (float) $item->price,
                'category' => $item->category,
            ]),
        ]);
    }
}
