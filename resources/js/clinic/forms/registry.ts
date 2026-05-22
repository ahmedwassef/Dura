import type { Component } from 'vue';
import type { FormTemplateCode } from '@/clinic/types/clinic';
import DentalPlanForm from '@/clinic/forms/partials/DentalPlanForm.vue';
import MedicalHistoryForm from '@/clinic/forms/partials/MedicalHistoryForm.vue';
import MedicalReportForm from '@/clinic/forms/partials/MedicalReportForm.vue';
import DynamicFormRenderer from '@/clinic/components/forms/DynamicFormRenderer.vue';

export function resolveFormComponent(code: FormTemplateCode): Component {
    if (code === 'dental') {
        return DentalPlanForm;
    }

    if (code === 'medhist') {
        return MedicalHistoryForm;
    }

    if (code === 'medreport') {
        return MedicalReportForm;
    }

    return DynamicFormRenderer;
}
