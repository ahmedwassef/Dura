<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import FormSection from '@/clinic/components/forms/FormSection.vue';
import PatientSelector from '@/clinic/components/forms/PatientSelector.vue';
import DoctorSelector from '@/clinic/components/forms/DoctorSelector.vue';
import SignaturePad from '@/clinic/components/forms/SignaturePad.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import type { FormField } from '@/types/admin';

const { isArabic } = useClinicLocale();

const props = defineProps<{
    formCode: string;
    titleAr: string;
    titleEn: string;
    fields: FormField[];
    initialData?: any;
}>();

// Standard patient & doctor selectors
const selectedPatient = ref<any>(null);
const selectedDoctor = ref<any>(null);

// Values of dynamic fields
const formValues = ref<Record<string, any>>({});
const signatureValues = ref<Record<string, string>>({}); // Stores signature pad images by field key

// Group fields by sections to display them under their matching FormSection!
// If a field comes before any section, we group it under a default section.
interface SectionGroup {
    titleAr: string;
    titleEn: string;
    bodyAr?: string;
    bodyEn?: string;
    fields: FormField[];
}

const sectionGroups = computed(() => {
    const groups: SectionGroup[] = [];
    let currentGroup: SectionGroup = {
        titleAr: isArabic.value ? 'البيانات التفصيلية' : 'Form Details',
        titleEn: 'Form Details',
        fields: [],
    };

    props.fields.forEach(field => {
        if (field.type === 'section') {
            if (currentGroup.fields.length > 0 || groups.length > 0) {
                groups.push(currentGroup);
            }
            currentGroup = {
                titleAr: field.label_ar || '',
                titleEn: field.label_en || '',
                bodyAr: field.content_ar || '',
                bodyEn: field.content_en || '',
                fields: [],
            };
        } else {
            currentGroup.fields.push(field);
        }
    });

    if (currentGroup.fields.length > 0 || groups.length === 0) {
        groups.push(currentGroup);
    }

    return groups;
});

// Setup default form state on mount or when fields/initialData load
onMounted(() => {
    // Initialise empty form fields
    props.fields.forEach(field => {
        if (field.key) {
            if (field.type === 'checkbox') {
                formValues.value[field.key] = false;
            } else if (field.type === 'select' || field.type === 'radio') {
                formValues.value[field.key] = '';
            } else if (field.type === 'signature') {
                signatureValues.value[field.key] = '';
            } else {
                formValues.value[field.key] = '';
            }
        }
    });

    // Populate initial submission data if editing
    if (props.initialData) {
        selectedPatient.value = props.initialData.patient;
        selectedDoctor.value = props.initialData.doctor;

        if (props.initialData.payload) {
            Object.keys(props.initialData.payload).forEach(key => {
                if (key in formValues.value) {
                    formValues.value[key] = props.initialData.payload[key];
                }
            });
        }

        if (props.initialData.signatures) {
            props.initialData.signatures.forEach((sig: any) => {
                // If signature matches a specific key or is standard
                if (sig.role && sig.image) {
                    signatureValues.value[sig.role] = sig.image;
                }
            });
        }
    }
});

// Expose buildPayload for saving submissions!
function buildPayload() {
    const signaturesList: Array<{ role: string; image: string; signer_name?: string }> = [];
    
    // Add all signatures configured in the builder
    Object.keys(signatureValues.value).forEach(key => {
        if (signatureValues.value[key]) {
            const field = props.fields.find(f => f.key === key);
            signaturesList.push({
                role: key,
                image: signatureValues.value[key],
                signer_name: key === 'doctor' ? selectedDoctor.value?.name : selectedPatient.value?.name,
            });
        }
    });

    // Determine if the form is fully signed (at least one signature is present)
    const isSigned = signaturesList.length > 0;

    return {
        patient_id: selectedPatient.value?.id || null,
        doctor_id: selectedDoctor.value?.id || null,
        patient_name: selectedPatient.value?.name || '—',
        file_number: selectedPatient.value?.file_number || null,
        doctor_name: selectedDoctor.value?.name || null,
        id_number: selectedPatient.value?.id_number || null,
        payload: {
            ...formValues.value,
        },
        grand_total: 0,
        is_signed: isSigned,
        signatures: signaturesList,
    };
}

defineExpose({ buildPayload });

function getWidthClass(field: FormField) {
    const w = field.settings?.width || 'full';
    if (w === 'half') return 'col-span-6';
    if (w === 'third') return 'col-span-4';
    return 'col-span-12';
}
</script>

<template>
    <div class="dynamic-form-renderer">
        <!-- Section 1: Patient Information (Required on all forms) -->
        <FormSection number="١" :title="{ ar: 'بيانات المريض والمعالج', en: 'Patient & Doctor Information' }">
            <div class="case-info-grid">
                <PatientSelector v-model="selectedPatient" />
                <DoctorSelector v-model="selectedDoctor" />
            </div>
        </FormSection>

        <!-- Dynamic Sections & Fields -->
        <FormSection 
            v-for="(group, idx) in sectionGroups" 
            :key="idx" 
            :number="String(idx + 2)"
            :title="{ ar: group.titleAr, en: group.titleEn }"
        >
            <div v-if="group.bodyAr || group.bodyEn" class="section-desc-box">
                <p>{{ isArabic ? group.bodyAr : group.bodyEn }}</p>
            </div>

            <div class="fields-grid-layout">
                <div 
                    v-for="field in group.fields" 
                    :key="field.id" 
                    class="field-container"
                    :class="getWidthClass(field)"
                >
                    <!-- Text / Number / Date Inputs -->
                    <div v-if="['text', 'number', 'date'].includes(field.type)" class="field">
                        <label>
                            {{ isArabic ? field.label_ar : field.label_en }}
                            <span v-if="field.is_required" class="required-star">*</span>
                        </label>
                        <input 
                            v-if="field.key"
                            v-model="formValues[field.key]"
                            :type="field.type"
                            :placeholder="isArabic ? field.placeholder_ar || '' : field.placeholder_en || ''"
                            :required="field.is_required"
                            class="renderer-input"
                        />
                    </div>

                    <!-- Textarea Input -->
                    <div v-else-if="field.type === 'textarea'" class="field">
                        <label>
                            {{ isArabic ? field.label_ar : field.label_en }}
                            <span v-if="field.is_required" class="required-star">*</span>
                        </label>
                        <textarea 
                            v-if="field.key"
                            v-model="formValues[field.key]"
                            :placeholder="isArabic ? field.placeholder_ar || '' : field.placeholder_en || ''"
                            :rows="field.settings?.rows || 3"
                            :required="field.is_required"
                            class="renderer-textarea"
                        ></textarea>
                    </div>

                    <!-- Dropdown Select -->
                    <div v-else-if="field.type === 'select'" class="field">
                        <label>
                            {{ isArabic ? field.label_ar : field.label_en }}
                            <span v-if="field.is_required" class="required-star">*</span>
                        </label>
                        <select 
                            v-if="field.key"
                            v-model="formValues[field.key]"
                            :required="field.is_required"
                            class="renderer-select"
                        >
                            <option value="">{{ isArabic ? 'اختر الخيار...' : 'Select option...' }}</option>
                            <option 
                                v-for="opt in field.options" 
                                :key="opt.value" 
                                :value="opt.value"
                            >
                                {{ isArabic ? opt.label_ar : opt.label_en }}
                            </option>
                        </select>
                    </div>

                    <!-- Radio Buttons Group -->
                    <div v-else-if="field.type === 'radio'" class="field">
                        <label class="group-label">
                            {{ isArabic ? field.label_ar : field.label_en }}
                            <span v-if="field.is_required" class="required-star">*</span>
                        </label>
                        <div class="radio-choices-row">
                            <label 
                                v-for="opt in field.options" 
                                :key="opt.value" 
                                class="radio-label-item"
                                :class="{ 'selected': field.key && formValues[field.key] === opt.value }"
                            >
                                <input 
                                    v-if="field.key"
                                    type="radio" 
                                    :name="field.key" 
                                    :value="opt.value" 
                                    v-model="formValues[field.key]" 
                                />
                                <span>{{ isArabic ? opt.label_ar : opt.label_en }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Checkbox Single -->
                    <div v-else-if="field.type === 'checkbox'" class="checkbox-container-row">
                        <label class="checkbox-label-item">
                            <input 
                                v-if="field.key"
                                type="checkbox" 
                                v-model="formValues[field.key]" 
                            />
                            <span class="checkbox-text">
                                {{ isArabic ? field.label_ar : field.label_en }}
                                <span v-if="field.is_required" class="required-star">*</span>
                            </span>
                        </label>
                    </div>

                    <!-- Consent block -->
                    <div v-else-if="field.type === 'consent'" class="consent-block-desc">
                        <h4 class="consent-block-header">{{ isArabic ? field.label_ar : field.label_en }}</h4>
                        <p class="consent-block-text">{{ isArabic ? field.content_ar : field.content_en }}</p>
                    </div>

                    <!-- Signature pad -->
                    <div v-else-if="field.type === 'signature'" class="renderer-signature-wrapper">
                        <SignaturePad 
                            v-if="field.key"
                            v-model="signatureValues[field.key]" 
                            :label="{ ar: field.label_ar || 'توقيع', en: field.label_en || 'Signature' }" 
                        />
                    </div>
                </div>
            </div>
        </FormSection>
    </div>
</template>

<style scoped>
.dynamic-form-renderer {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.case-info-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    align-items: start;
}
@media (max-width: 768px) {
    .case-info-grid { grid-template-columns: 1fr; }
}

.section-desc-box {
    background: var(--bg-soft);
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid var(--line);
    margin-bottom: 16px;
    font-size: 13.5px;
    color: var(--ink-soft);
}

.fields-grid-layout {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 16px;
}

.field-container {
    display: flex;
    flex-direction: column;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.field label {
    font-weight: 600;
    font-size: 13.5px;
    color: var(--ink);
}
.group-label {
    display: block;
    margin-bottom: 6px;
}

.required-star {
    color: var(--red);
    margin-inline-start: 2px;
}

.renderer-input, .renderer-textarea, .renderer-select {
    padding: 9px 14px;
    border: 1.5px solid var(--line);
    border-radius: 8px;
    background: white;
    color: var(--ink);
    font-family: inherit;
    font-size: 13.5px;
    transition: border-color 0.2s;
    width: 100%;
}
.renderer-input:focus, .renderer-textarea:focus, .renderer-select:focus {
    outline: none;
    border-color: var(--accent);
}
.renderer-textarea {
    resize: vertical;
}

/* Radio Choice styling */
.radio-choices-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.radio-label-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border: 1.5px solid var(--line);
    border-radius: 8px;
    background: var(--bg-card);
    cursor: pointer;
    font-size: 13px;
    transition: all 0.15s;
}
.radio-label-item:hover {
    border-color: var(--primary-soft);
}
.radio-label-item.selected {
    border-color: var(--accent);
    background: rgba(13, 148, 136, 0.06);
    color: var(--accent);
    font-weight: 600;
}
.radio-label-item input {
    margin: 0;
}

/* Checkbox choice styling */
.checkbox-container-row {
    display: flex;
    align-items: center;
    padding-top: 24px; /* Align nicely vertically next to standard inputs */
}
.checkbox-label-item {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}
.checkbox-label-item input {
    width: 18px;
    height: 18px;
    accent-color: var(--accent);
}
.checkbox-text {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
}

/* Consent Block Desc Styling */
.consent-block-desc {
    background: rgba(30, 58, 95, 0.02);
    border: 1.5px solid var(--line);
    border-radius: 12px;
    padding: 16px;
    margin: 8px 0;
}
.consent-block-header {
    font-size: 14px;
    font-weight: 800;
    color: var(--primary);
    margin: 0 0 8px;
}
.consent-block-text {
    font-size: 13px;
    line-height: 1.6;
    color: var(--ink-soft);
    margin: 0;
}

/* Signature Area Styling */
.renderer-signature-wrapper {
    margin-top: 12px;
}
</style>
