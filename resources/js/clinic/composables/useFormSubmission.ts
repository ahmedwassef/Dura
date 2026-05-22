import { ref } from 'vue';
import axios from 'axios';
import { useClinicSession } from '@/clinic/composables/useClinicSession';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { CLINIC_BASE } from '@/clinic/constants';
import { useClinicToast } from '@/clinic/composables/useClinicToast';

export type SubmissionPayload = {
    uuid?: string | null;
    form_code: string;
    branch_code: string;
    patient_name: string;
    id_number?: string | null;
    file_number?: string | null;
    doctor_name?: string | null;
    language: string;
    payload?: Record<string, unknown>;
    grand_total?: number;
    is_signed?: boolean;
    pdf_base64?: string | null;
    services?: Array<Record<string, unknown>>;
    tooth_states?: Record<string, unknown>;
    plan_teeth?: Array<number | string>;
    age_mode?: string | null;
    extra_discount?: number;
    extra_discount_type?: string;
    specialty_breakdown?: Record<string, unknown> | null;
    signatures?: Array<{ role: string; signer_name?: string; image?: string }>;
};

export function useFormSubmission() {
    const { branch } = useClinicSession();
    const { isArabic } = useClinicLocale();
    const { toast } = useClinicToast();
    const saving = ref(false);

    async function save(payload: SubmissionPayload): Promise<boolean> {
        if (! branch.value) {
            toast(
                isArabic.value
                    ? 'يرجى اختيار الفرع أولاً'
                    : 'Please select a branch first',
                'error',
            );

            return false;
        }

        saving.value = true;

        try {
            const csrf =
                typeof document !== 'undefined'
                    ? document
                          .querySelector('meta[name="csrf-token"]')
                          ?.getAttribute('content') ?? ''
                    : '';

            const response = await axios.post(`${CLINIC_BASE}/submissions`, {
                ...payload,
                branch_code: payload.branch_code || branch.value,
                language: payload.language || (isArabic.value ? 'ar' : 'en'),
            }, {
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (response.status !== 201 && response.status !== 200) {
                throw new Error('Save failed');
            }

            toast(
                isArabic.value
                    ? 'تم حفظ النموذج في الأرشيف'
                    : 'Form saved to archive',
                'success',
            );

            return true;
        } catch {
            toast(
                isArabic.value
                    ? 'تعذر حفظ النموذج، حاول مرة أخرى'
                    : 'Could not save form, please try again',
                'error',
            );

            return false;
        } finally {
            saving.value = false;
        }
    }

    return { saving, save };
}
