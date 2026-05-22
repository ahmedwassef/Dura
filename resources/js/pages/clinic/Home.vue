<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import FormCard from '@/clinic/components/ui/FormCard.vue';
import StartScreen from '@/clinic/components/layout/StartScreen.vue';

defineProps<{
    departments: Array<{
        code: string;
        name_ar: string;
        name_en: string;
        templates_count: number;
        description_ar: string;
        description_en: string;
    }>;
}>();

function openDepartment(code: string): void {
    router.visit(`/dashboard/clinic/${code}`);
}

function openArchive(): void {
    router.visit('/dashboard/clinic/archive');
}
</script>

<template>
    <Head title="نظام النماذج الطبية" />

    <StartScreen
        :title="{ ar: 'مرحباً بكم', en: 'Welcome' }"
        :subtitle="{
            ar: 'اختر القسم الطبي للبدء',
            en: 'Choose a medical department',
        }"
        :form-name="{
            ar: 'نظام النماذج الطبية',
            en: 'Medical Forms System',
        }"
        show-branch
        show-date
        show-archive
        show-admin
    >
        <div class="dept-section">
            <div class="forms-grid">
                <FormCard
                    v-for="dept in departments"
                    :key="dept.code"
                    primary
                    :title="{ ar: dept.name_ar, en: dept.name_en }"
                    :description="{
                        ar: dept.description_ar,
                        en: dept.description_en,
                    }"
                    :badge="{
                        ar: `${dept.templates_count} أقسام فرعية`,
                        en: `${dept.templates_count} sub-categories`,
                    }"
                    icon-class="primary-icon"
                    @click="openDepartment(dept.code)"
                />

                <FormCard
                    archive
                    :title="{ ar: 'الأرشيف والاستقبال', en: 'Archive & Reception' }"
                    :description="{
                        ar: 'الملفات الموقّعة بصيغة PDF — جاهزة للتنزيل والطباعة',
                        en: 'Signed forms as PDF — ready to download',
                    }"
                    :badge="{ ar: 'عرض الملفات', en: 'View Files' }"
                    @click="openArchive"
                />

                <FormCard
                    :title="{ ar: 'إدارة المرضى', en: 'Patient Management' }"
                    :description="{
                        ar: 'عرض سجلات المرضى والزيارات السابقة',
                        en: 'View patient records and previous visits',
                    }"
                    :badge="{ ar: 'قائمة المرضى', en: 'Patients List' }"
                    icon-class="medhist-icon"
                    @click="router.visit('/dashboard/clinic/patients')"
                >
                    <template #icon>
                        <svg viewBox="0 0 24 24" width="32" height="32"><path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    </template>
                </FormCard>

                <FormCard
                    :title="{ ar: 'إدارة الأطباء', en: 'Doctor Management' }"
                    :description="{
                        ar: 'إضافة وتعديل بيانات الأطباء والترخيص',
                        en: 'Add and edit doctor profiles and licenses',
                    }"
                    :badge="{ ar: 'قائمة الأطباء', en: 'Doctors List' }"
                    icon-class="doctor-icon"
                    @click="router.visit('/dashboard/clinic/doctors')"
                >
                    <template #icon>
                        <svg viewBox="0 0 24 24" width="32" height="32"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-9h10v2H7z"/></svg>
                    </template>
                </FormCard>

                <FormCard
                    :title="{ ar: 'كتالوج الخدمات', en: 'Service Catalog' }"
                    :description="{
                        ar: 'إدارة الخدمات والأسعار والفئات العلاجية',
                        en: 'Manage services, pricing, and treatment categories',
                    }"
                    :badge="{ ar: 'قائمة الخدمات', en: 'Services List' }"
                    icon-class="primary-icon"
                    @click="router.visit('/dashboard/clinic/services')"
                >
                    <template #icon>
                        <svg viewBox="0 0 24 24" width="32" height="32"><path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
                    </template>
                </FormCard>

                <FormCard
                    :title="{ ar: 'إدارة الجنسيات', en: 'Nationality Settings' }"
                    :description="{
                        ar: 'تخصيص قائمة الجنسيات المتاحة للمرضى',
                        en: 'Customize the list of available nationalities',
                    }"
                    :badge="{ ar: 'قائمة الجنسيات', en: 'Nationalities' }"
                    @click="router.visit('/dashboard/clinic/nationalities')"
                >
                    <template #icon>
                        <svg viewBox="0 0 24 24" width="32" height="32"><path fill="currentColor" d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/></svg>
                    </template>
                </FormCard>
            </div>
        </div>
    </StartScreen>
</template>
