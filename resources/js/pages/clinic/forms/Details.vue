<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Printer, Download, Eye, ArrowLeft } from 'lucide-vue-next';
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';
import FormPageLayout from '@/clinic/layouts/FormPageLayout.vue';
import { resolveFormComponent } from '@/clinic/forms/registry';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useClinicSession } from '@/clinic/composables/useClinicSession';
import type { FormTemplateCode } from '@/clinic/types/clinic';

const props = defineProps<{
    form: string;
    template: {
        code: string;
        name_ar: string;
        name_en: string;
        is_bilingual: boolean;
        is_ar_only: boolean;
        fields?: any[];
    };
    initialSubmission?: any;
}>();

const { isArabic } = useClinicLocale();
const { role } = useClinicSession();

const isAdmin = computed(() => role.value === 'admin');
const isPendingAdmin = computed(() => {
    return props.form === 'medreport' && props.initialSubmission?.status === 'pending_admin';
});
const isPending = computed(() => {
    return props.form === 'medreport' && props.initialSubmission?.status === 'pending';
});

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

const backHref = computed(() => `/dashboard/clinic/forms/${props.form}`);

const generating = ref(false);

async function generatePdf(): Promise<string | null> {
    const el = document.querySelector('.form-container') as HTMLElement;
    if (!el) return null;

    generating.value = true;
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
        generating.value = false;
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
    <Head :title="formTitle + ' - ' + (isArabic ? 'تفاصيل النموذج' : 'Form Details')" />

    <FormPageLayout :form-name="{ ar: template.name_ar, en: template.name_en }" :back-href="backHref" :breadcrumbs="breadcrumbs">
        <div class="details-view-container">
            <!-- Pending Admin Approval Banner -->
            <div v-if="isPendingAdmin" class="pending-admin-banner no-print">
                <div class="banner-content">
                    <span class="pulse-indicator"></span>
                    <span v-if="isArabic">هذا التقرير الطبي معلق بانتظار موافقة الإدارة وتطبيق الختم الرسمي.</span>
                    <span v-else>This medical report is pending administrative approval and stamp application.</span>
                </div>
                <Link
                    v-if="isAdmin"
                    :href="`/dashboard/clinic/submissions/${initialSubmission?.uuid}/edit`"
                    class="btn-approve-banner"
                >
                    {{ isArabic ? 'تعديل للاعتماد والختم' : 'Edit to Approve & Stamp' }}
                </Link>
            </div>

            <!-- Pending Submission Draft Banner -->
            <div v-if="isPending" class="pending-banner no-print">
                <div class="banner-content">
                    <span class="pulse-indicator-gray"></span>
                    <span v-if="isArabic">هذا التقرير الطبي مسودة ولم يتم إرساله للإدارة بعد.</span>
                    <span v-else>This medical report is saved as pending and has not been sent to the administration yet.</span>
                </div>
                <Link
                    v-if="role !== 'admin'"
                    :href="`/dashboard/clinic/submissions/${initialSubmission?.uuid}/edit`"
                    class="btn-edit-banner"
                >
                    {{ isArabic ? 'تعديل وإرسال للفريق' : 'Edit & Send to Team' }}
                </Link>
            </div>

            <component
                :is="formComponent"
                :form-code="form"
                :title-ar="template.name_ar"
                :title-en="template.name_en"
                :initial-data="initialSubmission"
                :fields="template.fields || []"
                :read-only="true"
            />
        </div>

        <template #actions>
            <div class="flex items-center gap-3">
                <Link
                    v-slot="{}"
                    v-if="isPendingAdmin && isAdmin"
                    :href="`/dashboard/clinic/submissions/${initialSubmission?.uuid}/edit`"
                    class="btn-approve-main"
                >
                    {{ isArabic ? 'اعتماد وختم' : 'Approve & Stamp' }}
                </Link>

                <Link
                    v-if="isPending && role !== 'admin'"
                    :href="`/dashboard/clinic/submissions/${initialSubmission?.uuid}/edit`"
                    class="btn-edit-main"
                >
                    {{ isArabic ? 'تعديل وإرسال للفريق' : 'Edit & Send to Team' }}
                </Link>

                <button
                    type="button"
                    class="btn btn-outline"
                    :disabled="generating"
                    @click="printForm"
                >
                    <Printer class="size-4 mr-2" />
                    {{ isArabic ? 'طباعة' : 'Print' }}
                </button>

                <button
                    type="button"
                    class="btn btn-outline"
                    :disabled="generating"
                    @click="previewPdf"
                >
                    <Eye class="size-4 mr-2" />
                    {{ isArabic ? 'عرض PDF' : 'View PDF' }}
                </button>

                <button
                    type="button"
                    class="btn btn-outline"
                    :disabled="generating"
                    @click="downloadPdf"
                >
                    <Download class="size-4 mr-2" />
                    {{ isArabic ? 'تنزيل PDF' : 'Download PDF' }}
                </button>

                <Link :href="backHref" class="btn btn-primary">
                    <ArrowLeft class="size-4 mr-2" />
                    {{ isArabic ? 'العودة للنموذج' : 'Back to Form' }}
                </Link>
            </div>
        </template>
    </FormPageLayout>
</template>

<style scoped>
.details-view-container {
    padding: 10px 0;
}
.flex {
    display: flex;
}
.items-center {
    align-items: center;
}
.gap-3 {
    gap: 12px;
}
.mr-2 {
    margin-right: 8px;
}
.size-4 {
    width: 16px;
    height: 16px;
}
.pending-admin-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 20px;
    color: #b45309;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.banner-content {
    display: flex;
    align-items: center;
    gap: 10px;
}
.pulse-indicator {
    width: 8px;
    height: 8px;
    background-color: #d97706;
    border-radius: 50%;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(217, 119, 6, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0);
    }
}
.btn-approve-banner {
    background: #f59e0b;
    color: white !important;
    border: 1px solid #d97706;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-approve-banner:hover {
    background: #d97706;
    border-color: #b45309;
    transform: translateY(-1px);
}
.btn-approve-main {
    background: #f59e0b;
    color: white !important;
    border: 1px solid #d97706;
    border-radius: 10px;
    padding: 12px 22px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-approve-main:hover {
    background: #d97706;
    border-color: #b45309;
    transform: translateY(-1px);
}
.pending-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 20px;
    color: #4b5563;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.pulse-indicator-gray {
    width: 8px;
    height: 8px;
    background-color: #9ca3af;
    border-radius: 50%;
    animation: pulse-gray 2s infinite;
}
@keyframes pulse-gray {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(156, 163, 175, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(156, 163, 175, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(156, 163, 175, 0);
    }
}
.btn-edit-banner {
    background: #4b5563;
    color: white !important;
    border: 1px solid #374151;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-edit-banner:hover {
    background: #374151;
    border-color: #1f2937;
    transform: translateY(-1px);
}
.btn-edit-main {
    background: #4b5563;
    color: white !important;
    border: 1px solid #374151;
    border-radius: 10px;
    padding: 12px 22px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-edit-main:hover {
    background: #374151;
    border-color: #1f2937;
    transform: translateY(-1px);
}
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
