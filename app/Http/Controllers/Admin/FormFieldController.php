<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormField;
use App\Models\FormTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormFieldController extends Controller
{
    public function index(FormTemplate $formTemplate): Response
    {
        return Inertia::render('admin/form-templates/Fields', [
            'template' => $formTemplate->load(['department', 'category']),
            'fields'   => $formTemplate->fields()->get(),
        ]);
    }

    public function store(Request $request, FormTemplate $formTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'type'           => 'required|string|in:section,text,textarea,number,date,select,radio,checkbox,consent,signature',
            'key'            => 'nullable|string|max:64|alpha_dash',
            'label_ar'       => 'nullable|string|max:255',
            'label_en'       => 'nullable|string|max:255',
            'placeholder_ar' => 'nullable|string|max:255',
            'placeholder_en' => 'nullable|string|max:255',
            'content_ar'     => 'nullable|string',
            'content_en'     => 'nullable|string',
            'options'        => 'nullable|array',
            'settings'       => 'nullable|array',
            'is_required'    => 'boolean',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        if (empty($validated['sort_order'])) {
            $maxSort = $formTemplate->fields()->max('sort_order') ?? 0;
            $validated['sort_order'] = $maxSort + 10;
        }

        $formTemplate->fields()->create($validated);

        return redirect()->back()->with('success', 'Form field added successfully.');
    }

    public function update(Request $request, FormField $formField): RedirectResponse
    {
        $validated = $request->validate([
            'type'           => 'required|string|in:section,text,textarea,number,date,select,radio,checkbox,consent,signature',
            'key'            => 'nullable|string|max:64|alpha_dash',
            'label_ar'       => 'nullable|string|max:255',
            'label_en'       => 'nullable|string|max:255',
            'placeholder_ar' => 'nullable|string|max:255',
            'placeholder_en' => 'nullable|string|max:255',
            'content_ar'     => 'nullable|string',
            'content_en'     => 'nullable|string',
            'options'        => 'nullable|array',
            'settings'       => 'nullable|array',
            'is_required'    => 'boolean',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        $formField->update($validated);

        return redirect()->back()->with('success', 'Form field updated successfully.');
    }

    public function destroy(FormField $formField): RedirectResponse
    {
        $formField->delete();

        return redirect()->back()->with('success', 'Form field deleted successfully.');
    }

    public function reorder(Request $request, FormTemplate $formTemplate): RedirectResponse
    {
        $request->validate([
            'fields'         => 'required|array',
            'fields.*.id'    => 'required|exists:form_fields,id',
            'fields.*.order' => 'required|integer',
        ]);

        foreach ($request->input('fields') as $item) {
            FormField::where('id', $item['id'])
                ->where('form_template_id', $formTemplate->id)
                ->update(['sort_order' => $item['order']]);
        }

        return redirect()->back()->with('success', 'Fields reordered successfully.');
    }
}
