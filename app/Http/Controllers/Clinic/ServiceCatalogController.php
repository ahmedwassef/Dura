<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\ServiceCatalog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceCatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $services = ServiceCatalog::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('category_ar', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('clinic/services/Index', [
            'services' => $services,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:service_catalog,code',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_ar' => 'nullable|string|max:255',
            'category_en' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        ServiceCatalog::create($validated);

        return back()->with('success', 'Service created successfully');
    }

    public function update(Request $request, ServiceCatalog $service)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:service_catalog,code,' . $service->id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_ar' => 'nullable|string|max:255',
            'category_en' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $service->update($validated);

        return back()->with('success', 'Service updated successfully');
    }

    public function destroy(ServiceCatalog $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted successfully');
    }
}
