<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import WelcomeScreen from '@/clinic/components/layout/WelcomeScreen.vue';
import { useClinicSession } from '@/clinic/composables/useClinicSession';
import type { BranchCode } from '@/clinic/types/clinic';

defineProps<{
    branches: Array<{ code: string; name_ar: string; name_en: string }>;
}>();

const { selectBranch } = useClinicSession();

function chooseBranch(code: BranchCode): void {
    selectBranch(code);

    router.post(
        '/dashboard/clinic/session/branch',
        { branch: code },
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head title="اختر الفرع" />

    <WelcomeScreen
        :title="{ ar: 'اختر الفرع', en: 'Select Branch' }"
        :subtitle="{
            ar: 'اختر فرع المجمع الذي تعمل به',
            en: 'Choose the branch you work at',
        }"
        :form-name="{
            ar: 'نظام النماذج الطبية',
            en: 'Medical Forms System',
        }"
    >
        <template #icon>
            <svg viewBox="0 0 24 24" width="40" height="40" fill="#1e3a5f">
                <path
                    d="M12 2C8 2 5 5 5 9c0 5 7 13 7 13s7-8 7-13c0-4-3-7-7-7zm0 9.5c-1.4 0-2.5-1.1-2.5-2.5S10.6 6.5 12 6.5 14.5 7.6 14.5 9 13.4 11.5 12 11.5z"
                />
            </svg>
        </template>

        <div class="welcome-cards">
            <div
                v-for="branch in branches"
                :key="branch.code"
                class="welcome-card"
                role="button"
                tabindex="0"
                @click="chooseBranch(branch.code as BranchCode)"
                @keyup.enter="chooseBranch(branch.code as BranchCode)"
            >
                <div class="welcome-card-icon">
                    <svg viewBox="0 0 24 24" width="36" height="36" fill="currentColor">
                        <path
                            d="M19 8h-2V3H7v5H5c-1.1 0-2 .9-2 2v11h18V10c0-1.1-.9-2-2-2zM9 5h6v3H9V5zm4 13h-2v-3H8v-2h3v-3h2v3h3v2h-3v3z"
                        />
                    </svg>
                </div>
                <h3>{{ branch.name_ar }}</h3>
                <p>{{ branch.name_en }}</p>
            </div>
        </div>
    </WelcomeScreen>
</template>
