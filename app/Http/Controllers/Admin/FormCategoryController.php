<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\FormCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/form-categories/Index', [
            'categories'  => FormCategory::with('department')->orderBy('department_id')->orderBy('sort_order')->get(),
            'departments' => Department::orderBy('sort_order')->get(['id', 'name_ar', 'name_en', 'code']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code'          => 'required|string|max:64|unique:form_categories,code|alpha_dash',
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'required|string|max:255',
            'sort_order'    => 'nullable|integer|min:0',
            'is_active'     => 'boolean',
        ]);

        FormCategory::create($validated);

        return redirect()->route('admin.form-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function update(Request $request, FormCategory $formCategory): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code'          => 'required|string|max:64|alpha_dash|unique:form_categories,code,' . $formCategory->id,
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'required|string|max:255',
            'sort_order'    => 'nullable|integer|min:0',
            'is_active'     => 'boolean',
        ]);

        $formCategory->update($validated);

        return redirect()->route('admin.form-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(FormCategory $formCategory): RedirectResponse
    {
        // Guard: cannot delete if form templates reference this category
        if ($formCategory->formTemplates()->exists()) {
            return redirect()->route('admin.form-categories.index')
                ->with('error', 'Cannot delete: form templates are assigned to this category.');
        }

        $formCategory->delete();

        return redirect()->route('admin.form-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
