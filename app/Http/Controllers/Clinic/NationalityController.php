<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Nationality;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NationalityController extends Controller
{
    public function index(Request $request): Response
    {
        $nationalities = Nationality::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name_ar')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('clinic/nationalities/Index', [
            'nationalities' => $nationalities,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
        ]);

        Nationality::create($validated);

        return back()->with('success', 'Nationality created successfully');
    }

    public function update(Request $request, Nationality $nationality)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
        ]);

        $nationality->update($validated);

        return back()->with('success', 'Nationality updated successfully');
    }

    public function destroy(Nationality $nationality)
    {
        $nationality->delete();
        return back()->with('success', 'Nationality deleted successfully');
    }
}
