<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DoctorController extends Controller
{
    public function index(Request $request): Response
    {
        $doctors = Doctor::query()
            ->with(['branch'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('specialty', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('clinic/doctors/Index', [
            'doctors' => $doctors,
            'filters' => $request->only(['search']),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');
        
        $doctors = Doctor::query()
            ->where('is_active', true)
            ->when($query, function($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json(['doctors' => $doctors]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $branchId = session('clinic_branch_id') ?? Branch::first()?->id;
        
        $doctor = Doctor::create(array_merge($validated, [
            'branch_id' => $branchId,
            'is_active' => $request->boolean('is_active', true)
        ]));

        if ($request->wantsJson()) {
            return response()->json(['doctor' => $doctor], 201);
        }

        return back()->with('success', 'Doctor created successfully');
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $doctor->update($validated);

        return back()->with('success', 'Doctor updated successfully');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return back()->with('success', 'Doctor deleted successfully');
    }
}
