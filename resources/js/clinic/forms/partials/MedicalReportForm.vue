<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useClinicSession } from '@/clinic/composables/useClinicSession';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import SignaturePad from '@/clinic/components/forms/SignaturePad.vue';
import PatientSelector from '@/clinic/components/forms/PatientSelector.vue';
import DoctorSelector from '@/clinic/components/forms/DoctorSelector.vue';
import { FileText, Plus, X, Lock } from 'lucide-vue-next';

const props = defineProps<{
    initialData?: any;
}>();

const { isArabic } = useClinicLocale();
const { role } = useClinicSession();

// Form Fields
const selectedPatient = ref<any>(null);
const selectedDoctor = ref<any>(null);

const manualPatientName = ref('');
const fileNumber = ref('');
const idNumber = ref('');
const reportDate = ref(new Date().toISOString().slice(0, 10));
const reportBody = ref('');
const doctorNameInput = ref('');

// Signatures and Stamps
const doctorSignature = ref('');
const doctorStamp = ref('');
const complexStamp = ref('');

// Refs for File Inputs
const doctorStampInput = ref<HTMLInputElement | null>(null);
const complexStampInput = ref<HTMLInputElement | null>(null);

// Computed Checks
const isAdmin = computed(() => role.value === 'admin');

// Sync Selected Patient details to inputs
function onPatientSelected(patient: any) {
    if (patient) {
        manualPatientName.value = patient.name || '';
        fileNumber.value = patient.file_number || '';
        idNumber.value = patient.id_number || patient.national_id || '';
    }
}

// Sync Selected Doctor name to input
function onDoctorSelected(doctor: any) {
    if (doctor) {
        doctorNameInput.value = doctor.name || '';
    }
}

// FileReader helper for base64 stamp uploads
function handleStampUpload(event: Event, type: 'doctor' | 'complex') {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    if (type === 'complex' && !isAdmin.value) {
        alert(isArabic.value ? 'ختم المجمع يتطلب صلاحية الإدارة' : 'Complex stamp requires admin permission');
        return;
    }

    if (!file.type.startsWith('image/')) {
        alert(isArabic.value ? 'الرجاء اختيار صورة فقط' : 'Please select an image only');
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        const base64 = e.target?.result as string;
        if (type === 'doctor') {
            doctorStamp.value = base64;
        } else {
            complexStamp.value = base64;
        }
    };
    reader.readAsDataURL(file);
    target.value = ''; // Reset file input
}

function clearStamp(type: 'doctor' | 'complex') {
    if (type === 'complex' && !isAdmin.value) {
        alert(isArabic.value ? 'حذف ختم المجمع يتطلب صلاحية الإدارة' : 'Clearing complex stamp requires admin permission');
        return;
    }

    if (type === 'doctor') {
        doctorStamp.value = '';
    } else {
        complexStamp.value = '';
    }
}

function triggerFileInput(inputRef: HTMLInputElement | null) {
    if (inputRef) {
        inputRef.click();
    }
}

// Initialize edit mode
onMounted(() => {
    if (props.initialData) {
        const d = props.initialData;
        selectedPatient.value = d.patient || null;
        selectedDoctor.value = d.doctor || null;
        
        manualPatientName.value = d.patient_name || '';
        fileNumber.value = d.file_number || '';
        idNumber.value = d.id_number || '';
        doctorNameInput.value = d.doctor_name || '';

        if (d.payload) {
            reportDate.value = d.payload.date || new Date().toISOString().slice(0, 10);
            reportBody.value = d.payload.body || '';
        }

        if (d.signatures) {
            d.signatures.forEach((sig: any) => {
                if (sig.role === 'doctor') {
                    doctorSignature.value = sig.image || '';
                } else if (sig.role === 'doctor_stamp') {
                    doctorStamp.value = sig.image || '';
                } else if (sig.role === 'complex_stamp') {
                    complexStamp.value = sig.image || '';
                }
            });
        }
    }
});

// buildPayload for form submission
function buildPayload() {
    const finalDoctorName = doctorNameInput.value.trim() || 
        (isArabic.value ? 'إلى من يهمه الأمر' : 'To Whom It May Concern');

    const sigs = [];
    if (doctorSignature.value) {
        sigs.push({
            role: 'doctor',
            signer_name: finalDoctorName,
            image: doctorSignature.value
        });
    }
    if (doctorStamp.value) {
        sigs.push({
            role: 'doctor_stamp',
            image: doctorStamp.value
        });
    }
    if (complexStamp.value) {
        sigs.push({
            role: 'complex_stamp',
            image: complexStamp.value
        });
    }

    return {
        patient_id: selectedPatient.value?.id || null,
        doctor_id: selectedDoctor.value?.id || null,
        patient_name: manualPatientName.value.trim() || '—',
        file_number: fileNumber.value.trim() || null,
        id_number: idNumber.value.trim() || null,
        doctor_name: finalDoctorName,
        payload: {
            date: reportDate.value,
            body: reportBody.value,
            needs_admin_stamp: !complexStamp.value,
        },
        grand_total: 0,
        is_signed: !!doctorSignature.value,
        signatures: sigs
    };
}

defineExpose({ buildPayload });
</script>

<template>
    <div class="medreport-paper">
        <!-- Header with logo and titles -->
        <div class="medreport-header">
            <div class="durat-logo-img medreport-logo" role="img" aria-label="شعار المجمع"></div>
            <div class="medreport-titles">
                <h1 v-if="isArabic">مجمع درة النخبة الطبي</h1>
                <h1 v-else>Durat Alnukhba Medical Complex</h1>
                <div class="en-name">Durat Alnukhba Medical Complex</div>
                <div class="medreport-doc-type">
                    <LocalizedText :value="{ ar: 'تقرير طبي / MEDICAL REPORT', en: 'MEDICAL REPORT / تقرير طبي' }" />
                </div>
            </div>
        </div>

        <!-- CRM Patient/Doctor Selectors (Hidden on Print) -->
        <div class="admin-selectors-row no-print">
            <div class="selector-field">
                <label class="field-label">
                    <LocalizedText :value="{ ar: 'اختر مريض مسجل (اختياري)', en: 'Select Registered Patient (Optional)' }" />
                </label>
                <PatientSelector v-model="selectedPatient" @update:modelValue="onPatientSelected" />
            </div>
            <div class="selector-field">
                <label class="field-label">
                    <LocalizedText :value="{ ar: 'اختر طبيب معالج (اختياري)', en: 'Select Doctor (Optional)' }" />
                </label>
                <DoctorSelector v-model="selectedDoctor" @update:modelValue="onDoctorSelected" />
            </div>
        </div>

        <!-- Patient info grid -->
        <div class="medreport-info-grid">
            <div class="field">
                <label><LocalizedText :value="{ ar: 'اسم المريض', en: 'Patient Name' }" /></label>
                <input v-model="manualPatientName" type="text" required class="form-control" />
            </div>
            <div class="field">
                <label><LocalizedText :value="{ ar: 'رقم الملف', en: 'File Number' }" /></label>
                <input v-model="fileNumber" type="text" class="form-control" />
            </div>
            <div class="field">
                <label><LocalizedText :value="{ ar: 'رقم الهوية', en: 'National ID' }" /></label>
                <input v-model="idNumber" type="text" class="form-control" inputmode="numeric" />
            </div>
            <div class="field">
                <label><LocalizedText :value="{ ar: 'التاريخ', en: 'Date' }" /></label>
                <input v-model="reportDate" type="date" class="form-control" />
            </div>
        </div>

        <!-- Doctor's report body -->
        <div class="medreport-body-label">
            <FileText class="size-4 text-emerald-600" />
            <span><LocalizedText :value="{ ar: 'نص التقرير', en: 'Report Body' }" /></span>
        </div>
        <textarea
            v-model="reportBody"
            class="medreport-body"
            required
            :placeholder="isArabic ? 'اكتب التقرير هنا...' : 'Write the report here...'"
        ></textarea>

        <!-- Signature, Doctor stamp, Complex stamp -->
        <div class="medreport-sign-row">
            <!-- Doctor signature -->
            <div class="medreport-sign-card">
                <SignaturePad
                    v-model="doctorSignature"
                    :label="{ ar: 'توقيع الطبيب', en: 'Doctor\'s Signature' }"
                    :placeholder="isArabic ? 'وقع هنا' : 'Sign here'"
                />
                <input
                    v-model="doctorNameInput"
                    type="text"
                    class="doctor-name-input"
                    :placeholder="isArabic ? 'اسم الطبيب (اختياري)' : 'Doctor Name (Optional)'"
                />
            </div>

            <!-- Doctor stamp -->
            <div class="medreport-sign-card">
                <div class="sign-label">
                    <LocalizedText :value="{ ar: 'ختم الطبيب', en: 'Doctor\'s Stamp' }" />
                </div>
                
                <div 
                    class="stamp-zone cursor-pointer" 
                    @click="triggerFileInput(doctorStampInput)"
                >
                    <img v-if="doctorStamp" :src="doctorStamp" alt="Doctor Stamp" />
                    <template v-else>
                        <Plus class="size-5 opacity-50" />
                        <span class="upload-hint">
                            <LocalizedText :value="{ ar: 'اضغط لرفع ختم الطبيب', en: 'Click to upload doctor\'s stamp' }" />
                        </span>
                    </template>
                </div>
                
                <input 
                    ref="doctorStampInput" 
                    type="file" 
                    accept="image/*" 
                    style="display:none;" 
                    @change="handleStampUpload($event, 'doctor')" 
                />
                
                <div class="sign-actions" v-if="doctorStamp">
                    <button type="button" class="sign-mini-btn delete" @click="clearStamp('doctor')">
                        <LocalizedText :value="{ ar: 'مسح', en: 'Clear' }" />
                    </button>
                </div>
            </div>

            <!-- Complex stamp (LOCKED unless admin) -->
            <div class="medreport-sign-card">
                <div class="sign-label">
                    <LocalizedText :value="{ ar: 'ختم المجمع', en: 'Complex Stamp' }" />
                </div>

                <div 
                    class="stamp-zone"
                    :class="{ 'locked': !isAdmin && !complexStamp, 'cursor-pointer': isAdmin }"
                    @click="isAdmin ? triggerFileInput(complexStampInput) : null"
                >
                    <img v-if="complexStamp" :src="complexStamp" alt="Complex Stamp" />
                    <template v-else-if="!isAdmin">
                        <Lock class="lock-icon" />
                        <span class="lock-text">
                            <LocalizedText :value="{ ar: 'يتطلب صلاحية الإدارة', en: 'Admin permission required' }" />
                        </span>
                    </template>
                    <template v-else>
                        <Plus class="size-5 opacity-50" />
                        <span class="upload-hint">
                            <LocalizedText :value="{ ar: 'اضغط لرفع ختم المجمع', en: 'Click to upload complex stamp' }" />
                        </span>
                    </template>
                </div>

                <input 
                    ref="complexStampInput" 
                    type="file" 
                    accept="image/*" 
                    style="display:none;" 
                    @change="handleStampUpload($event, 'complex')" 
                />

                <div class="sign-actions" v-if="complexStamp">
                    <button type="button" class="sign-mini-btn delete" @click="clearStamp('complex')">
                        <LocalizedText :value="{ ar: 'مسح', en: 'Clear' }" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.medreport-paper {
    max-width: 850px;
    margin: 0 auto;
}
.admin-selectors-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
    background: #f8fafc;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid var(--line);
}

.selector-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.field-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink-soft);
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--line);
    border-radius: 8px;
    font-size: 14px;
    background: white;
    color: var(--ink);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
}

.doctor-name-input {
    margin-top: 8px;
    padding: 8px 12px;
    border: 1px solid var(--line);
    border-radius: 8px;
    font-size: 13px;
    text-align: center;
    width: 100%;
}

.doctor-name-input:focus {
    outline: none;
    border-color: var(--primary);
}

.sign-actions {
    display: flex;
    gap: 6px;
    margin-top: 8px;
    justify-content: center;
}

.sign-mini-btn {
    padding: 5px 12px;
    font-size: 11px;
    border: 1px solid var(--line);
    background: white;
    border-radius: 6px;
    cursor: pointer;
    color: var(--ink-soft);
    font-weight: 700;
    transition: all 0.2s;
}

.sign-mini-btn.delete:hover {
    color: var(--red);
    border-color: var(--red);
    background: #fef2f2;
}

.cursor-pointer {
    cursor: pointer;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>
