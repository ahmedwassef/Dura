<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Printer, Download, Eye } from 'lucide-vue-next';
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';
import FormPageLayout from '@/clinic/layouts/FormPageLayout.vue';
import { resolveFormComponent } from '@/clinic/forms/registry';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useClinicSession } from '@/clinic/composables/useClinicSession';
import { useFormSubmission } from '@/clinic/composables/useFormSubmission';
import type { FormTemplateCode } from '@/clinic/types/clinic';

const props = defineProps<{
    form: string;
    template: {
        code: string;
        name_ar: string;
        name_en: string;
        is_bilingual: boolean;
        is_ar_only: boolean;
    };
    initialSubmission?: any;
}>();

const { isArabic } = useClinicLocale();
const { branch } = useClinicSession();
const { saving, save } = useFormSubmission();
const formRef = ref<{ buildPayload?: () => Record<string, unknown> } | null>(null);

const formComponent = computed(() =>
    resolveFormComponent(props.form as FormTemplateCode),
);

const formTitle = computed(() =>
    isArabic.value ? props.template.name_ar : props.template.name_en,
);

const breadcrumbs = computed(() => {
    const list = [];
    
    // 1. Department
    if (['proc_card', 'roaccutane', 'derm_photo', 'laser_hair', 'laser_bleach', 'self_laser', 'morpheus'].includes(props.form)) {
        list.push({ label: { ar: 'قسم الجلدية', en: 'Dermatology' }, href: '/dashboard/clinic/dermatology' });
    } else {
        list.push({ label: { ar: 'قسم الأسنان', en: 'Dental Department' }, href: '/dashboard/clinic/dental' });
    }

    // 2. Category (if applicable)
    if (['proc_card', 'roaccutane', 'derm_photo'].includes(props.form)) {
        list.push({ label: { ar: 'عيادة الدكتورة', en: "Doctor's Clinic" }, href: '/dashboard/clinic/category/derm_clinic' });
    } else if (['laser_hair', 'laser_bleach', 'self_laser', 'morpheus'].includes(props.form)) {
        list.push({ label: { ar: 'قسم الأجهزة', en: 'Devices Section' }, href: '/dashboard/clinic/category/derm_devices' });
    } else if (['surgery'].includes(props.form)) {
        list.push({ label: { ar: 'الجراحة والقلع', en: 'Surgery & Extraction' }, href: '/dashboard/clinic/category/extraction' });
    } else if (['rct', 'afinash'].includes(props.form)) {
        list.push({ label: { ar: 'علاج العصب', en: 'Endodontics' }, href: '/dashboard/clinic/category/endo' });
    } else if (['veneer', 'crown_rct', 'crown_implant'].includes(props.form)) {
        list.push({ label: { ar: 'التركيبات السنية', en: 'Prosthodontics' }, href: '/dashboard/clinic/category/prostho' });
    } else if (['ortho', 'ortho_end', 'ortho_photo', 'ortho_extraction'].includes(props.form)) {
        list.push({ label: { ar: 'تقويم الأسنان', en: 'Orthodontics' }, href: '/dashboard/clinic/category/ortho' });
    }

    // 3. Current Form
    list.push({ label: formTitle.value });

    return list;
});

const backHref = computed(() => {
    const parent = breadcrumbs.value[breadcrumbs.value.length - 2];
    return parent?.href || '/dashboard/clinic/home';
});

async function saveForm(): Promise<void> {
    if (! branch.value) {
        router.visit('/dashboard/clinic');
        return;
    }

    saving.value = true;
    const pdfBase64 = await generatePdf();

    const partial = formRef.value?.buildPayload?.() ?? {
        patient_name: '—',
        grand_total: 0,
        is_signed: false,
    };

    const ok = await save({
        uuid: props.initialSubmission?.uuid || null,
        form_code: props.form,
        branch_code: branch.value,
        patient_id: partial.patient_id as number | null,
        doctor_id: partial.doctor_id as number | null,
        patient_name: String(partial.patient_name ?? '—'),
        id_number: (partial.id_number as string) ?? null,
        file_number: (partial.file_number as string) ?? null,
        doctor_name: (partial.doctor_name as string) ?? null,
        language: isArabic.value ? 'ar' : 'en',
        payload: (partial.payload as Record<string, unknown>) ?? {},
        grand_total: Number(partial.grand_total ?? 0),
        is_signed: Boolean(partial.is_signed ?? false),
        pdf_base64: pdfBase64,
        services: (partial.services as Array<Record<string, unknown>>) ?? [],
        tooth_states: (partial.tooth_states as Record<string, unknown>) ?? {},
        plan_teeth: (partial.plan_teeth as Array<number | string>) ?? [],
        age_mode: (partial.age_mode as string) ?? null,
        signatures: (partial.signatures as Array<Record<string, unknown>>) ?? [],
    });

    if (ok) {
        router.visit('/dashboard/clinic/archive');
    }
}

async function generatePdf(): Promise<string | null> {
    const el = document.querySelector('.form-container') as HTMLElement;
    if (!el) return null;

    document.documentElement.classList.add('pdf-mode');

    try {
        const canvas = await html2canvas(el, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
        });

        const imgData = canvas.toDataURL('image/jpeg', 0.92);
        const pdf = new jsPDF('p', 'mm', 'a4', true);
        const pdfW = 210;
        const pdfH = 297;
        const imgW = pdfW - 10;
        const imgH = (canvas.height * imgW) / canvas.width;

        let y = 0;
        const pageH = pdfH - 10;

        while (y < imgH) {
            if (y > 0) pdf.addPage();
            pdf.addImage(imgData, 'JPEG', 5, 5 - y, imgW, imgH, '', 'FAST');
            y += pageH;
        }

        return pdf.output('datauristring');
    } catch (e) {
        console.error('PDF generation failed', e);
        return null;
    } finally {
        document.documentElement.classList.remove('pdf-mode');
    }
}

async function printForm(): Promise<void> {
    const pdfData = await generatePdf();
    if (!pdfData) {
        window.print();
        return;
    }

    const newWin = window.open('');
    if (!newWin) return;
    newWin.document.write(`<iframe src="${pdfData}" style="width:100%;height:100vh;border:none;" onload="this.contentWindow.print();"></iframe>`);
    newWin.document.close();
}

async function downloadPdf(): Promise<void> {
    if (props.initialSubmission?.pdfUrl) {
        const link = document.createElement('a');
        link.href = props.initialSubmission.pdfUrl;
        link.download = 'form-submission.pdf';
        link.click();
        return;
    }

    const pdfData = await generatePdf();
    if (!pdfData) return;

    const link = document.createElement('a');
    link.href = pdfData;
    link.download = `form-${props.form}-${new Date().getTime()}.pdf`;
    link.click();
}

async function previewPdf(): Promise<void> {
    if (props.initialSubmission?.pdfUrl) {
        window.open(props.initialSubmission.pdfUrl, '_blank');
        return;
    }

    const pdfData = await generatePdf();
    if (!pdfData) return;

    const newWin = window.open('');
    if (!newWin) return;
    newWin.document.write(`<iframe src="${pdfData}" style="width:100%;height:100vh;border:none;"></iframe>`);
    newWin.document.close();
}
</script>

<template>
    <Head :title="formTitle" />

    <FormPageLayout :form-name="{ ar: template.name_ar, en: template.name_en }" :back-href="backHref" :breadcrumbs="breadcrumbs">
        <component
            :is="formComponent"
            ref="formRef"
            :form-code="form"
            :title-ar="template.name_ar"
            :title-en="template.name_en"
            :initial-data="initialSubmission"
            :fields="template.fields || []"
        />

        <template #actions>
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="btn btn-outline"
                    @click="printForm"
                >
                    <Printer class="size-4 mr-2" />
                    {{ isArabic ? 'طباعة' : 'Print' }}
                </button>

                <button
                    type="button"
                    class="btn btn-outline"
                    @click="previewPdf"
                >
                    <Eye class="size-4 mr-2" />
                    {{ isArabic ? 'عرض PDF' : 'View PDF' }}
                </button>

                <button
                    type="button"
                    class="btn btn-outline"
                    @click="downloadPdf"
                >
                    <Download class="size-4 mr-2" />
                    {{ isArabic ? 'تنزيل PDF' : 'Download PDF' }}
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    :disabled="saving"
                    @click="saveForm"
                >
                    {{
                        saving
                            ? isArabic
                                ? 'جاري الحفظ...'
                                : 'Saving...'
                            : isArabic
                              ? 'حفظ وإرسال للأرشيف'
                              : 'Save & send to archive'
                    }}
                </button>
                <Link :href="backHref" class="btn btn-outline">
                    {{ isArabic ? 'رجوع' : 'Back' }}
                </Link>
            </div>
        </template>
    </FormPageLayout>
</template>
