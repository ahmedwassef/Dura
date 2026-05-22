<script setup lang="ts">
import { ref } from 'vue';
import FormSection from '@/clinic/components/forms/FormSection.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';

defineProps<{
    formCode: string;
    titleAr: string;
    titleEn: string;
}>();

const patientName = ref('');
const fileNum = ref('');
const doctorName = ref('');

function buildPayload() {
    return {
        patient_name: patientName.value || '—',
        file_number: fileNum.value || null,
        doctor_name: doctorName.value || null,
        payload: {
            patient_name: patientName.value,
            file_number: fileNum.value,
            doctor_name: doctorName.value,
        },
        grand_total: 0,
        is_signed: false,
    };
}

defineExpose({ buildPayload });
</script>

<template>
    <FormSection
        number="١"
        :title="{ ar: 'بيانات المريض', en: 'Patient Information' }"
    >
        <div class="field-grid">
            <div class="field">
                <label>
                    <LocalizedText :value="{ ar: 'اسم المريض', en: 'Patient Name' }" />
                </label>
                <input v-model="patientName" type="text" :placeholder="'—'" />
            </div>
            <div class="field">
                <label>
                    <LocalizedText :value="{ ar: 'رقم الملف', en: 'File No.' }" />
                </label>
                <input v-model="fileNum" type="text" :placeholder="'—'" />
            </div>
            <div class="field">
                <label>
                    <LocalizedText :value="{ ar: 'اسم الطبيب', en: 'Doctor' }" />
                </label>
                <input v-model="doctorName" type="text" :placeholder="'—'" />
            </div>
        </div>
    </FormSection>

    <FormSection
        number="٢"
        :title="{ ar: 'الإقرار والتوقيع', en: 'Declaration & Signature' }"
    >
        <div class="consent-box">
            <p>
                <LocalizedText
                    :value="{
                        ar: `نموذج ${titleAr} — سيتم استكمال الحقول التفصيلية في المرحلة التالية.`,
                        en: `${titleEn} form — detailed fields will be ported in the next phase.`,
                    }"
                />
            </p>
        </div>
        <div class="signature-area active">
            <canvas class="signature-pad" height="120" />
        </div>
    </FormSection>
</template>
