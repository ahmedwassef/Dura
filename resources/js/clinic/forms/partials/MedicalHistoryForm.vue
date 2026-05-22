<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import FormSection from '@/clinic/components/forms/FormSection.vue';
import PatientSelector from '@/clinic/components/forms/PatientSelector.vue';
import DoctorSelector from '@/clinic/components/forms/DoctorSelector.vue';
import SignaturePad from '@/clinic/components/forms/SignaturePad.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';

const { isArabic } = useClinicLocale();

const props = defineProps<{
    initialData?: any;
}>();

// State
const selectedPatient = ref<any>(null);
const selectedDoctor = ref<any>(null);
const diseases = ref<string[]>([]);
const detailQuestions = ref({
    hospitalized: '',
    bleeding: '',
    medication: '',
    allergies: '',
    other: ''
});
const womenOnly = ref({
    pregnant: false,
    miscarriage: false,
    contraceptive: false,
    notes: ''
});
const dentalQuestions = ref({
    purpose: '',
    lastVisit: '',
    brushingFrequency: '',
    gumProblems: false,
    wantFullTreatment: false,
    painNow: false,
    continueUntilEnd: false
});

const patientSignature = ref('');
const doctorSignature = ref('');

// Computed
const isFemale = computed(() => {
    const sex = selectedPatient.value?.sex || '';
    return sex === 'female' || sex === 'أنثى';
});

// Initialization
onMounted(() => {
    if (props.initialData) {
        const d = props.initialData;
        selectedPatient.value = d.patient;
        selectedDoctor.value = d.doctor;
        
        if (d.payload) {
            diseases.value = d.payload.diseases || [];
            detailQuestions.value = {
                hospitalized: d.payload.hospitalized || '',
                bleeding: d.payload.bleeding || '',
                medication: d.payload.medication || '',
                allergies: d.payload.allergies || '',
                other: d.payload.other || ''
            };
            womenOnly.value = {
                pregnant: !!d.payload.pregnant,
                miscarriage: !!d.payload.miscarriage,
                contraceptive: !!d.payload.contraceptive,
                notes: d.payload.notes || ''
            };
            dentalQuestions.value = {
                purpose: d.payload.purpose || '',
                lastVisit: d.payload.lastVisit || '',
                brushingFrequency: d.payload.brushingFrequency || '',
                gumProblems: !!d.payload.gumProblems,
                wantFullTreatment: !!d.payload.wantFullTreatment,
                painNow: !!d.payload.painNow,
                continueUntilEnd: !!d.payload.continueUntilEnd
            };
        }

        if (d.signatures) {
            d.signatures.forEach((sig: any) => {
                if (sig.role === 'patient') patientSignature.value = sig.image || '';
                if (sig.role === 'doctor') doctorSignature.value = sig.image || '';
            });
        }
    }
});

function buildPayload() {
    return {
        patient_id: selectedPatient.value?.id || null,
        doctor_id: selectedDoctor.value?.id || null,
        patient_name: selectedPatient.value?.name || '—',
        file_number: selectedPatient.value?.file_number || null,
        doctor_name: selectedDoctor.value?.name || null,
        payload: {
            diseases: diseases.value,
            ...detailQuestions.value,
            ...womenOnly.value,
            ...dentalQuestions.value,
        },
        grand_total: 0,
        is_signed: !!(patientSignature.value), // Patient signature is mandatory for medical history
        signatures: [
            { role: 'patient', image: patientSignature.value },
            { role: 'doctor', image: doctorSignature.value, signer_name: selectedDoctor.value?.name }
        ]
    };
}

defineExpose({ buildPayload });

const DISEASE_OPTIONS = [
    { ar: 'مرض القلب', en: 'Heart Disease' },
    { ar: 'مرض السكر', en: 'Diabetes' },
    { ar: 'آلام الصدر', en: 'Chest Pain' },
    { ar: 'أمراض الجلد', en: 'Skin Diseases' },
    { ar: 'ارتفاع أو انخفاض ضغط الدم', en: 'High/Low Blood Pressure' },
    { ar: 'فقر الدم', en: 'Anemia' },
    { ar: 'أمراض الكلى', en: 'Kidney Diseases' },
    { ar: 'الجيوب الأنفية أو الحساسية', en: 'Sinusitis or Allergies' },
    { ar: 'أمراض الغدد', en: 'Glandular Disorders' },
    { ar: 'الربو أو السل', en: 'Asthma or TB' },
    { ar: 'الصفراء أو التهاب الكبد الوبائي', en: 'Jaundice or Hepatitis' },
    { ar: 'الصرع أو الإغماء', en: 'Epilepsy or Fainting' },
    { ar: 'الحمى الروماتزمية', en: 'Rheumatic Fever' },
];
</script>

<template>
    <div class="medical-history-form">
        <!-- ١. بيانات المريض -->
        <FormSection number="١" :title="{ ar: 'بيانات المريض', en: 'Patient Information' }">
            <div class="case-info-grid">
                <PatientSelector v-model="selectedPatient" />
                <DoctorSelector v-model="selectedDoctor" />
            </div>
        </FormSection>

        <!-- ٢. التاريخ المرضي -->
        <FormSection number="٢" :title="{ ar: 'التاريخ المرضي - الأمراض', en: 'Medical History - Diseases' }">
            <p class="section-hint">
                <LocalizedText :value="{ ar: 'الرجاء تحديد ما ينطبق على المريض:', en: 'Please check all that apply:' }" />
            </p>
            <div class="checklist-grid">
                <label v-for="opt in DISEASE_OPTIONS" :key="opt.en" class="check-item">
                    <input type="checkbox" :value="opt.ar" v-model="diseases" />
                    <span><LocalizedText :value="opt" /></span>
                </label>
            </div>
        </FormSection>

        <!-- ٣. أسئلة تفصيلية -->
        <FormSection number="٣" :title="{ ar: 'أسئلة تفصيلية', en: 'Detailed Questions' }">
            <div class="detailed-questions">
                <div class="field">
                    <label><LocalizedText :value="{ ar: 'هل سبق أن دخلت المستشفى؟ ولماذا؟', en: 'Have you been hospitalized? Why?' }" /></label>
                    <textarea v-model="detailQuestions.hospitalized"></textarea>
                </div>
                <div class="field">
                    <label><LocalizedText :value="{ ar: 'بعد حدوث جرح، هل تنزف طويلاً؟ كم دقيقة يستمر النزيف؟', en: 'Do you bleed long after injury? How many minutes?' }" /></label>
                    <textarea v-model="detailQuestions.bleeding"></textarea>
                </div>
                <div class="field">
                    <label><LocalizedText :value="{ ar: 'هل تتعاطى أي علاج الآن؟ ما هو؟', en: 'Are you currently taking any medication?' }" /></label>
                    <textarea v-model="detailQuestions.medication"></textarea>
                </div>
                <div class="field">
                    <label><LocalizedText :value="{ ar: 'هل لديك حساسية ضد البنسلين أو أي دواء آخر؟', en: 'Are you allergic to penicillin or any other drug?' }" /></label>
                    <textarea v-model="detailQuestions.allergies"></textarea>
                </div>
                <div class="field">
                    <label><LocalizedText :value="{ ar: 'هل لديك أي مشكلة أخرى؟', en: 'Do you have any other condition?' }" /></label>
                    <textarea v-model="detailQuestions.other"></textarea>
                </div>
            </div>
        </FormSection>

        <!-- ٤. للنساء فقط -->
        <FormSection v-if="isFemale" number="٤" :title="{ ar: 'للنساء فقط', en: 'For Women Only' }">
            <div class="checklist-grid">
                <label class="check-item">
                    <input type="checkbox" v-model="womenOnly.pregnant" />
                    <span><LocalizedText :value="{ ar: 'هل أنت حامل؟', en: 'Are you pregnant?' }" /></span>
                </label>
                <label class="check-item">
                    <input type="checkbox" v-model="womenOnly.miscarriage" />
                    <span><LocalizedText :value="{ ar: 'هل حصل إجهاض أو مشكلة؟', en: 'Any miscarriage or issue?' }" /></span>
                </label>
                <label class="check-item">
                    <input type="checkbox" v-model="womenOnly.contraceptive" />
                    <span><LocalizedText :value="{ ar: 'هل تتعاطين حبوب منع الحمل أو هرمونات؟', en: 'Taking contraceptives or hormones?' }" /></span>
                </label>
            </div>
            <div class="field mt-4">
                <label><LocalizedText :value="{ ar: 'ملاحظات إضافية', en: 'Additional Notes' }" /></label>
                <textarea v-model="womenOnly.notes"></textarea>
            </div>
        </FormSection>

        <!-- ٥. أسئلة خاصة بالأسنان -->
        <FormSection number="٥" :title="{ ar: 'أسئلة خاصة بالأسنان', en: 'Dental Questions' }">
            <div class="field">
                <label><LocalizedText :value="{ ar: 'ما هو الغرض الأساسي من الزيارة؟', en: 'Main purpose of visit?' }" /></label>
                <input type="text" v-model="dentalQuestions.purpose" class="admin-input" />
            </div>
            <div class="field-grid mt-4">
                <div class="field">
                    <label><LocalizedText :value="{ ar: 'متى آخر زيارة لطبيب الأسنان؟', en: 'Last dental visit?' }" /></label>
                    <input type="text" v-model="dentalQuestions.lastVisit" class="admin-input" />
                </div>
                <div class="field">
                    <label><LocalizedText :value="{ ar: 'كم مرة تستعمل الفرشة والمعجون باليوم؟', en: 'Brushing frequency per day?' }" /></label>
                    <input type="text" v-model="dentalQuestions.brushingFrequency" class="admin-input" />
                </div>
            </div>
            <div class="checklist-grid mt-4">
                <label class="check-item">
                    <input type="checkbox" v-model="dentalQuestions.gumProblems" />
                    <span><LocalizedText :value="{ ar: 'هل تعاني من أمراض اللثة؟', en: 'Gum problems?' }" /></span>
                </label>
                <label class="check-item">
                    <input type="checkbox" v-model="dentalQuestions.wantFullTreatment" />
                    <span><LocalizedText :value="{ ar: 'هل ترغب في علاج كامل لأسنانك؟', en: 'Want full treatment?' }" /></span>
                </label>
                <label class="check-item">
                    <input type="checkbox" v-model="dentalQuestions.painNow" />
                    <span><LocalizedText :value="{ ar: 'هل تشعر بألم الآن في الأسنان؟', en: 'Feeling pain now?' }" /></span>
                </label>
                <label class="check-item">
                    <input type="checkbox" v-model="dentalQuestions.continueUntilEnd" />
                    <span><LocalizedText :value="{ ar: 'هل تستمر معنا حتى نهاية العلاج؟', en: 'Continue until treatment end?' }" /></span>
                </label>
            </div>
        </FormSection>

        <!-- ٦. إقرار وتوقيع -->
        <FormSection number="٦" :title="{ ar: 'إقرار وتوقيع', en: 'Declaration & Signature' }">
            <div class="consent-text-box">
                <span v-if="isArabic">
                    أُقر أنا الموقع أدناه بأن جميع المعلومات المذكورة أعلاه صحيحة وكاملة، وبأني مسؤول تماماً عن أي إغفال أو خطأ في البيانات المقدمة، وأني أعطيت موافقتي للبدء في الفحص والعلاج اللازم.
                </span>
                <span v-else>
                    I, the undersigned, declare that all the information provided above is true and complete, and I assume full responsibility for any omissions or errors in the data provided. I give my consent to proceed with the necessary examination and treatment.
                </span>
            </div>

            <div class="signatures-grid">
            
                <SignaturePad 
                    v-model="patientSignature" 
                    :label="{ ar: 'توقيع المريض', en: 'Patient Signature' }" 
                     
                />
                <SignaturePad 
                    v-model="doctorSignature" 
                    :label="{ ar: 'توقيع الطبيب', en: 'Doctor Signature' }" 
                    
                />
            </div>
        </FormSection>
    </div>
</template>

<style scoped>
.case-info-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    align-items: start;
}
.checklist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}
.check-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #fdfdfd;
    border: 1px solid var(--line);
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: all .2s;
}
.check-item:hover {
    border-color: var(--primary-light);
    background: white;
}
.check-item input {
    width: 18px;
    height: 18px;
}
.section-hint {
    color: var(--ink-soft);
    font-size: 13px;
    margin-bottom: 14px;
}
.detailed-questions {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.field label {
    font-weight: 600;
    font-size: 14px;
    color: var(--ink);
}
.field textarea {
    min-height: 80px;
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: 8px;
    font-family: inherit;
    resize: vertical;
}
.field-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.consent-text-box {
    background: var(--bg-soft);
    padding: 15px;
    border-radius: 10px;
    border: 1px solid var(--line);
    margin-bottom: 20px;
    font-size: 14px;
    line-height: 1.6;
    color: var(--ink);
}
.signatures-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}
.mt-4 { margin-top: 16px; }
@media (max-width: 768px) {
    .case-info-grid, .field-grid, .signatures-grid { grid-template-columns: 1fr; }
}
</style>
