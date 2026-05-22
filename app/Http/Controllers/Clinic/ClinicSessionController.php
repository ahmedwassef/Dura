<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Services\Clinic\ClinicSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClinicSessionController extends Controller
{
    public function __construct(
        protected ClinicSessionService $clinicSession,
    ) {}

    public function storeBranch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch' => ['required', 'string', 'in:zulfi,dawadmi'],
        ]);

        $this->clinicSession->setBranch($validated['branch']);

        return redirect()->route('admin.clinic.role', ['branch' => $validated['branch']]);
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:reception,doctor,admin'],
            'branch' => ['nullable', 'string', 'in:zulfi,dawadmi'],
        ]);

        if (! empty($validated['branch'])) {
            $this->clinicSession->setBranch($validated['branch']);
        }

        $this->clinicSession->setRole($validated['role']);

        return match ($validated['role']) {
            'reception' => redirect()->route('admin.clinic.archive'),
            'admin' => redirect()->route('admin.clinic.records'),
            default => redirect()->route('admin.clinic.home'),
        };
    }
}
