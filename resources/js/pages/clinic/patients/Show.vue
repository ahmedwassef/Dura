<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';

const props = defineProps<{
    patient: {
        id: number;
        name: string;
        id_number: string;
        file_number: string;
        phone: string;
        nationality: string;
        age: number;
        sex: string;
        address: string;
        branch: any;
        form_submissions: Array<any>;
    };
}>();
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
                <div class="profile-info">
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
                            <span>{{ patient.nationality || '-' }}</span>
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
                                    <Link :href="`/dashboard/clinic/submissions/${sub.uuid}/edit`" class="btn-view">
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
</style>
