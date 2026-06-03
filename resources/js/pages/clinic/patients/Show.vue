<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useClinicToast } from '@/clinic/composables/useClinicToast';
import { Pen, X, User } from 'lucide-vue-next';

const props = defineProps<{
    patient: {
        id: number;
        name: string;
        id_number: string;
        file_number: string;
        phone: string;
        nationality: any;
        age: number;
        sex: string;
        address: string;
        branch: any;
        form_submissions: Array<any>;
        history?: Array<any>;
    };
}>();

const { isArabic } = useClinicLocale();
const { toast } = useClinicToast();

const showEditModal = ref(false);
const isSaving = ref(false);
const nationalities = ref<any[]>([]);

const form = ref({
    name: '',
    id_number: '',
    file_number: '',
    phone: '',
    nationality_id: '' as string | number,
    date_of_birth: '',
    sex: 'male',
    address: '',
});

watch(() => props.patient, (newPatient) => {
    if (newPatient) {
        form.value = {
            name: newPatient.name || '',
            id_number: newPatient.id_number || '',
            file_number: newPatient.file_number || '',
            phone: newPatient.phone || '',
            nationality_id: (typeof newPatient.nationality === 'object' && newPatient.nationality)
                ? newPatient.nationality.id
                : (newPatient.nationality_id || ''),
            date_of_birth: (newPatient as any).date_of_birth || '',
            sex: newPatient.sex || 'male',
            address: newPatient.address || '',
        };
    }
}, { immediate: true });

onMounted(async () => {
    try {
        const res = await axios.get('/dashboard/clinic/nationalities');
        nationalities.value = res.data.nationalities;
    } catch (e) {
        console.error('Failed to load nationalities', e);
    }
});

async function updatePatient() {
    if (!form.value.name.trim()) {
        toast(isArabic.value ? 'الاسم مطلوب' : 'Name is required', 'error');
        return;
    }
    isSaving.value = true;
    try {
        await axios.put(`/dashboard/clinic/patients/${props.patient.id}`, form.value);
        toast(isArabic.value ? 'تم تحديث بيانات المريض بنجاح' : 'Patient updated successfully', 'success');
        showEditModal.value = false;
        router.reload({ only: ['patient'] });
    } catch (e: any) {
        console.error('Failed to update patient', e);
        const errMsg = e.response?.data?.message || (isArabic.value ? 'فشل تحديث البيانات' : 'Failed to update patient');
        toast(errMsg, 'error');
    } finally {
        isSaving.value = false;
    }
}

function formatDate(dateStr: string) {
    if (!dateStr) return '';
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString(isArabic.value ? 'ar-SA' : 'en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return dateStr;
    }
}

function getFieldLabel(field: string) {
    const labels: Record<string, { ar: string, en: string }> = {
        name: { ar: 'الاسم', en: 'Name' },
        id_number: { ar: 'رقم الهوية', en: 'ID Number' },
        file_number: { ar: 'رقم الملف', en: 'File Number' },
        phone: { ar: 'رقم الجوال', en: 'Phone' },
        nationality_id: { ar: 'الجنسية', en: 'Nationality' },
        date_of_birth: { ar: 'تاريخ الميلاد', en: 'Date of Birth' },
        sex: { ar: 'الجنس', en: 'Sex' },
        address: { ar: 'العنوان', en: 'Address' },
    };
    const label = labels[field];
    if (!label) return field;
    return isArabic.value ? label.ar : label.en;
}

function getNationalityName(id: any) {
    if (!id || nationalities.value.length === 0) return '—';
    const n = nationalities.value.find(nat => nat.id === id || String(nat.id) === String(id));
    return n ? (isArabic.value ? n.name_ar : n.name_en) : '—';
}
</script>

<template>
    <Head :title="'ملف المريض: ' + patient.name" />

    <div class="admin-screen active">
        <ClinicHeader
            :form-name="{
                ar: 'ملف المريض',
                en: 'Patient File',
            }"
            show-back
            back-href="/dashboard/clinic/patients"
            :breadcrumbs="[
                { label: { ar: 'قائمة المرضى', en: 'Patients List' }, href: '/dashboard/clinic/patients' },
                { label: patient.name }
            ]"
        />

        <div class="admin-content">
            <div class="patient-profile-header">
                <div class="profile-info flex-header">
                    <div class="profile-info-left">
                        <div class="profile-avatar">
                            {{ patient.name ? patient.name.charAt(0) : 'P' }}
                        </div>
                        <div>
                            <h1>{{ patient.name }}</h1>
                            <p class="profile-meta">
                                <span class="badge">{{ patient.file_number }}</span>
                                <span class="separator">|</span>
                                <span>{{ patient.phone }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <button @click="showEditModal = true" class="edit-profile-btn" :style="isArabic ? { marginRight: 'auto' } : { marginLeft: 'auto' }">
                        <Pen class="size-4 mr-2" />
                        {{ isArabic ? 'تعديل البيانات' : 'Edit Details' }}
                    </button>
                </div>
            </div>

            <div class="profile-grid">
                <!-- Sidebar: Patient Details -->
                <div class="profile-sidebar">
                    <div class="info-card">
                        <h3><LocalizedText :value="{ ar: 'المعلومات الشخصية', en: 'Personal Info' }" /></h3>
                        <div class="info-row">
                            <label><LocalizedText :value="{ ar: 'رقم الهوية', en: 'ID Number' }" /></label>
                            <span>{{ patient.id_number || '-' }}</span>
                        </div>
                        <div class="info-row">
                            <label><LocalizedText :value="{ ar: 'الجنسية', en: 'Nationality' }" /></label>
                            <span>
                                {{ 
                                    typeof patient.nationality === 'object' && patient.nationality
                                        ? (isArabic ? patient.nationality.name_ar : patient.nationality.name_en) 
                                        : (patient.nationality || '-') 
                                }}
                            </span>
                        </div>
                        <div class="info-row">
                            <label><LocalizedText :value="{ ar: 'العمر', en: 'Age' }" /></label>
                            <span>{{ patient.age || '-' }}</span>
                        </div>
                        <div class="info-row">
                            <label><LocalizedText :value="{ ar: 'الجنس', en: 'Sex' }" /></label>
                            <span>{{ patient.sex || '-' }}</span>
                        </div>
                        <div class="info-row">
                            <label><LocalizedText :value="{ ar: 'الفرع', en: 'Branch' }" /></label>
                            <span>{{ patient.branch?.name || '-' }}</span>
                        </div>
                        <div class="info-row">
                            <label><LocalizedText :value="{ ar: 'العنوان', en: 'Address' }" /></label>
                            <span>{{ patient.address || '-' }}</span>
                        </div>
                    </div>

                    <!-- Change History Timeline -->
                    <div class="info-card mt-4" v-if="patient.history && patient.history.length > 0">
                        <h3><LocalizedText :value="{ ar: 'سجل التعديلات', en: 'Change History' }" /></h3>
                        <div class="timeline-container">
                            <div v-for="(entry, index) in patient.history" :key="index" class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <span class="username">{{ entry.user_name }}</span>
                                        <span class="date">{{ formatDate(entry.updated_at) }}</span>
                                    </div>
                                    <ul class="timeline-changes">
                                        <li v-for="(change, field) in entry.changes" :key="field">
                                            <strong>{{ getFieldLabel(field) }}:</strong>
                                            <span class="old-val">
                                                {{ field === 'nationality_id' ? getNationalityName(change.old) : (change.old || '—') }}
                                            </span>
                                            <span class="arrow">←</span>
                                            <span class="new-val">
                                                {{ field === 'nationality_id' ? getNationalityName(change.new) : (change.new || '—') }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main: Submissions History -->
                <div class="profile-main">
                    <div class="info-card">
                        <h3><LocalizedText :value="{ ar: 'سجل النماذج والزيارات', en: 'Form Submissions' }" /></h3>
                        
                        <div v-if="!patient.form_submissions || patient.form_submissions.length === 0" class="arch-empty">
                            <p><LocalizedText :value="{ ar: 'لا توجد نماذج سابقة لهذا المريض', en: 'No previous forms for this patient' }" /></p>
                        </div>

                        <div v-else class="submissions-list">
                            <div v-for="sub in patient.form_submissions" :key="sub.id" class="sub-item">
                                <div class="sub-icon">
                                    <svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                </div>
                                <div class="sub-details">
                                    <h4>
                                        <LocalizedText v-if="sub.template" :value="{ ar: sub.template.name_ar, en: sub.template.name_en }" />
                                        <span v-else>{{ sub.form_type }}</span>
                                    </h4>
                                    <p class="sub-meta">
                                        <span>{{ new Date(sub.created_at).toLocaleDateString('ar-SA') }}</span>
                                        <span class="dot">·</span>
                                        <span v-if="sub.user" class="doctor">{{ isArabic ? 'بواسطة:' : 'By:' }} {{ sub.user.name }}</span>
                                    </p>
                                </div>
                                <div class="sub-actions">
                                    <Link :href="`/dashboard/clinic/submissions/${sub.uuid}`" class="btn-view">
                                        <LocalizedText :value="{ ar: 'عرض', en: 'View' }" />
                                    </Link>
                                    <a :href="`/dashboard/clinic/submissions/${sub.uuid}/pdf`" target="_blank" class="btn-pdf">
                                        PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div v-if="showEditModal" class="modal-overlay" @click.self="showEditModal = false">
        <div class="modal-content">
            <div class="modal-header">
                <h3><LocalizedText :value="{ ar: 'تعديل بيانات المريض', en: 'Edit Patient Details' }" /></h3>
                <button @click="showEditModal = false" class="close-btn"><X class="size-5" /></button>
            </div>
            <div class="modal-body">
                <div class="form-grid-modal">
                    <div class="m-field full">
                        <label><LocalizedText :value="{ ar: 'الاسم الكامل', en: 'Full Name' }" /></label>
                        <input v-model="form.name" type="text" required class="modal-input" />
                    </div>
                    <div class="m-field">
                        <label><LocalizedText :value="{ ar: 'رقم الهوية', en: 'ID Number' }" /></label>
                        <input v-model="form.id_number" type="text" class="modal-input" />
                    </div>
                    <div class="m-field">
                        <label><LocalizedText :value="{ ar: 'رقم الجوال', en: 'Phone' }" /></label>
                        <input v-model="form.phone" type="text" class="modal-input" />
                    </div>
                    <div class="m-field">
                        <label><LocalizedText :value="{ ar: 'رقم الملف', en: 'File Number' }" /></label>
                        <input v-model="form.file_number" type="text" class="modal-input" />
                    </div>
                    <div class="m-field">
                        <label><LocalizedText :value="{ ar: 'تاريخ الميلاد', en: 'Date of Birth' }" /></label>
                        <input v-model="form.date_of_birth" type="date" class="modal-input" />
                    </div>
                    <div class="m-field">
                        <label><LocalizedText :value="{ ar: 'الجنسية', en: 'Nationality' }" /></label>
                        <select v-model="form.nationality_id" class="modal-select">
                            <option value=""><LocalizedText :value="{ ar: 'اختر الجنسية', en: 'Select Nationality' }" /></option>
                            <option v-for="n in nationalities" :key="n.id" :value="n.id">
                                {{ isArabic ? n.name_ar : n.name_en }}
                            </option>
                        </select>
                    </div>
                    <div class="m-field">
                        <label><LocalizedText :value="{ ar: 'الجنس', en: 'Sex' }" /></label>
                        <select v-model="form.sex" class="modal-select">
                            <option value="male">{{ isArabic ? 'ذكر' : 'Male' }}</option>
                            <option value="female">{{ isArabic ? 'أنثى' : 'Female' }}</option>
                        </select>
                    </div>
                    <div class="m-field full">
                        <label><LocalizedText :value="{ ar: 'العنوان', en: 'Address' }" /></label>
                        <textarea v-model="form.address" rows="2" class="modal-textarea" style="padding: 10px 12px; border: 1.5px solid var(--line); border-radius: 10px; font-size: 14px; resize: vertical; width: 100%;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="updatePatient" class="btn-save" :disabled="isSaving">
                    {{ isSaving ? (isArabic ? 'جاري الحفظ...' : 'Saving...') : (isArabic ? 'حفظ التعديلات' : 'Save Changes') }}
                </button>
                <button @click="showEditModal = false" class="btn-cancel">
                    <LocalizedText :value="{ ar: 'إلغاء', en: 'Cancel' }" />
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.patient-profile-header {
    background: white;
    padding: 24px;
    border-radius: 14px;
    margin-bottom: 20px;
    border: 1.5px solid var(--line);
}
.profile-info {
    display: flex;
    align-items: center;
    gap: 20px;
}
.profile-avatar {
    width: 64px;
    height: 64px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 800;
}
.profile-info h1 {
    font-size: 24px;
    font-weight: 800;
    color: var(--primary);
    margin: 0;
}
.profile-meta {
    color: var(--ink-mute);
    margin: 5px 0 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.profile-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
}
@media (max-width: 900px) {
    .profile-grid { grid-template-columns: 1fr; }
}
.info-card {
    background: white;
    padding: 20px;
    border-radius: 14px;
    border: 1.5px solid var(--line);
}
.info-card h3 {
    font-size: 16px;
    font-weight: 800;
    color: var(--primary);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--line);
}
.info-row {
    margin-bottom: 12px;
}
.info-row label {
    display: block;
    font-size: 11px;
    color: var(--ink-mute);
    margin-bottom: 2px;
}
.info-row span {
    font-weight: 700;
    color: var(--ink-soft);
}
.submissions-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.sub-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: 10px;
}
.sub-icon {
    width: 40px;
    height: 40px;
    background: var(--bg-soft);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}
.sub-details {
    flex: 1;
}
.sub-details h4 {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
}
.sub-details p {
    font-size: 12px;
    color: var(--ink-mute);
    margin: 2px 0 0;
}
.doctor {
    font-style: italic;
    color: var(--accent) !important;
}
.badge {
    background: var(--accent-soft);
    color: var(--accent);
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
}
.btn-pdf {
    display: inline-block;
    padding: 6px 14px;
    background: #fee2e2;
    color: #991b1b;
    border-radius: 8px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    transition: all .2s;
}
.btn-pdf:hover {
    background: #fecaca;
}
.btn-view {
    display: inline-block;
    padding: 6px 14px;
    background: var(--bg-soft);
    color: var(--primary);
    border-radius: 8px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid var(--line);
    transition: all .2s;
}
.btn-view:hover {
    border-color: var(--primary);
    background: white;
}
.sub-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--ink-mute);
    margin-top: 4px !important;
}
.dot {
    opacity: 0.5;
}
.separator {
    color: var(--line);
}
.arch-empty {
    text-align: center;
    padding: 40px;
    color: var(--ink-mute);
}

/* Edit profile and timeline styles */
.flex-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}
.profile-info-left {
    display: flex;
    align-items: center;
    gap: 20px;
}
.edit-profile-btn {
    border: 1.5px solid var(--line);
    background: white;
    color: var(--primary);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    font-size: 13px;
    transition: all .2s;
}
.edit-profile-btn:hover {
    border-color: var(--primary);
    background: var(--bg-soft);
}
.timeline-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-top: 10px;
    max-height: 350px;
    overflow-y: auto;
    padding-right: 4px;
}
.timeline-item {
    display: flex;
    gap: 12px;
    position: relative;
    padding-bottom: 8px;
}
.timeline-item::before {
    content: '';
    position: absolute;
    top: 6px;
    bottom: 0;
    left: 4px;
    width: 2px;
    background: var(--line);
}
[dir="rtl"] .timeline-item::before {
    left: auto;
    right: 4px;
}
.timeline-item:last-child::before {
    display: none;
}
.timeline-dot {
    width: 10px;
    height: 10px;
    background: var(--primary);
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
    z-index: 1;
}
.timeline-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.timeline-header {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
}
.timeline-header .username {
    font-weight: 700;
    color: var(--primary);
}
.timeline-header .date {
    color: var(--ink-mute);
}
.timeline-changes {
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 12px;
    color: var(--ink-soft);
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.timeline-changes li {
    background: var(--bg-soft);
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid var(--line);
}
.timeline-changes .old-val {
    color: var(--red);
    text-decoration: line-through;
    margin: 0 4px;
}
.timeline-changes .arrow {
    color: var(--ink-mute);
    margin: 0 4px;
}
.timeline-changes .new-val {
    color: var(--primary);
    font-weight: 700;
    margin: 0 4px;
}

/* Modal styles copied from PatientSelector */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1100;
}
.modal-content {
    background: white;
    width: 90%;
    max-width: 550px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
}
.modal-header {
    padding: 20px;
    border-bottom: 1px solid var(--line);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: var(--primary);
}
.close-btn {
    border: none;
    background: none;
    cursor: pointer;
    color: var(--ink-mute);
}
.modal-body {
    padding: 20px;
}
.form-grid-modal {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.m-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.m-field label {
    font-size: 12px;
    font-weight: 700;
    color: var(--ink-mute);
}
.modal-input, .modal-select, .modal-textarea {
    padding: 10px 12px;
    border: 1.5px solid var(--line);
    border-radius: 10px;
    font-size: 14px;
    background: white;
    width: 100%;
}
.modal-input:focus, .modal-select:focus, .modal-textarea:focus {
    border-color: var(--primary);
    outline: none;
}
.m-field.full {
    grid-column: span 2;
}
.modal-footer {
    padding: 20px;
    background: var(--bg-soft);
    display: flex;
    gap: 12px;
}
.btn-save {
    background: var(--primary);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    flex: 2;
}
.btn-save:hover {
    background: var(--primary-dark);
}
.btn-cancel {
    background: white;
    color: var(--ink-mute);
    border: 1px solid var(--line);
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    flex: 1;
}
.btn-cancel:hover {
    background: var(--bg-soft);
}
</style>
