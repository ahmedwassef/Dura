<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import FormCard from '@/clinic/components/ui/FormCard.vue';
import StartScreen from '@/clinic/components/layout/StartScreen.vue';

function openCategory(code: string): void {
    router.visit(`/dashboard/clinic/category/${code}`);
}
</script>

<template>
    <Head title="قسم الجلدية" />

    <StartScreen
        :title="{ ar: 'قسم الجلدية', en: 'Dermatology Department' }"
        :subtitle="{ ar: 'اختر القسم الفرعي', en: 'Choose a sub-category' }"
        :form-name="{
            ar: 'قسم الجلدية - اختر النموذج',
            en: 'Dermatology Department',
        }"
        header-class="derm-header"
        show-back
        back-href="/dashboard/clinic/home"
        :breadcrumbs="[
            { label: { ar: 'قسم الجلدية', en: 'Dermatology' } }
        ]"
    >
        <div class="dept-section">
            <div class="forms-grid">
                <FormCard
                    primary
                    :title="{ ar: 'عيادة الدكتورة', en: 'Doctor\'s Clinic' }"
                    :description="{
                        ar: 'كرت الإجراءات والحقن، الروكتان، التصوير',
                        en: 'Procedures card, Roaccutane, photography',
                    }"
                    :badge="{ ar: '٣ نماذج', en: '3 forms' }"
                    @click="openCategory('derm_clinic')"
                />
                <FormCard
                    primary
                    :title="{ ar: 'قسم الأجهزة', en: 'Devices Section' }"
                    :description="{
                        ar: 'السجل الصحي، الليزر، التشقير، الليزر الذاتي، المورفيس',
                        en: 'Medical history, laser, bleaching, self-laser, Morpheus',
                    }"
                    :badge="{ ar: '٥ نماذج', en: '5 forms' }"
                    @click="openCategory('derm_devices')"
                />
            </div>
        </div>
    </StartScreen>
</template>

<style scoped>
:deep(.derm-header),
:deep(.top-header.derm-header) {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
}
</style>
