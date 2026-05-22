<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\FormCategory;
use App\Models\FormTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormTemplateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/form-templates/Index', [
            'templates'    => FormTemplate::with(['department', 'category'])->orderBy('department_id')->orderBy('sort_order')->get(),
            'departments'  => Department::orderBy('sort_order')->get(['id', 'name_ar', 'name_en', 'code']),
            'categories'   => FormCategory::orderBy('department_id')->orderBy('sort_order')->get(['id', 'department_id', 'code', 'name_ar', 'name_en']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code'          => 'required|string|max:64|unique:form_templates,code|alpha_dash',
            'category_code' => 'nullable|string|exists:form_categories,code',
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'required|string|max:255',
            'is_bilingual'  => 'boolean',
            'is_ar_only'    => 'boolean',
            'sort_order'    => 'nullable|integer|min:0',
            'is_active'     => 'boolean',
        ]);

        FormTemplate::create($validated);

        return redirect()->route('admin.form-templates.index')
            ->with('success', 'Form template created successfully.');
    }

    public function update(Request $request, FormTemplate $formTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code'          => 'required|string|max:64|alpha_dash|unique:form_templates,code,' . $formTemplate->id,
            'category_code' => 'nullable|string|exists:form_categories,code',
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'required|string|max:255',
            'is_bilingual'  => 'boolean',
            'is_ar_only'    => 'boolean',
            'sort_order'    => 'nullable|integer|min:0',
            'is_active'     => 'boolean',
        ]);

        $formTemplate->update($validated);

        return redirect()->route('admin.form-templates.index')
            ->with('success', 'Form template updated successfully.');
    }

    public function destroy(FormTemplate $formTemplate): RedirectResponse
    {
        // Guard: cannot delete if submissions reference this template
        if ($formTemplate->submissions()->exists()) {
            return redirect()->route('admin.form-templates.index')
                ->with('error', 'Cannot delete: form submissions exist for this template.');
        }

        $formTemplate->delete();

        return redirect()->route('admin.form-templates.index')
            ->with('success', 'Form template deleted successfully.');
    }
}
