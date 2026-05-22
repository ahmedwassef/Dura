<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Nationality;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    public function index(Request $request): Response
    {
        $patients = Patient::query()
            ->with(['branch'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%")
                    ->orWhere('file_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('clinic/patients/Index', [
            'patients' => $patients,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(Patient $patient): Response
    {
        $patient->load(['branch', 'formSubmissions.user', 'formSubmissions.template']);

        return Inertia::render('clinic/patients/Show', [
            'patient' => $patient,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');
        
        $patients = Patient::query()
            ->when($query, function($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('file_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json([
            'patients' => $patients
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:20',
            'file_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'date_of_birth' => 'nullable|date',
            'sex' => 'nullable|string|in:male,female',
            'address' => 'nullable|string',
        ]);

        // Get branch from session or default to first
        $branchId = session('clinic_branch_id') ?? Branch::first()?->id;
        
        $patient = Patient::create(array_merge($validated, [
            'branch_id' => $branchId
        ]));

        return response()->json([
            'message' => 'Patient created successfully',
            'patient' => $patient
        ], 201);
    }

    public function nationalities(): JsonResponse
    {
        return response()->json([
            'nationalities' => Nationality::orderBy('name_en')->get()
        ]);
    }
}
