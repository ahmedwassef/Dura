<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import WelcomeScreen from '@/clinic/components/layout/WelcomeScreen.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicSession } from '@/clinic/composables/useClinicSession';
import type { ClinicRole } from '@/clinic/types/clinic';

const props = defineProps<{
    branch: string;
}>();

const { selectRole, branchLabel } = useClinicSession();

function chooseRole(role: ClinicRole): void {
    selectRole(role);

    router.post(
        '/dashboard/clinic/session/role',
        { role, branch: props.branch },
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head title="اختر الدور" />

    <WelcomeScreen
        :title="{ ar: 'اختر دورك الوظيفي', en: 'Select Your Role' }"
        :subtitle="{
            ar: 'حسب وظيفتك تظهر لك الواجهة المناسبة',
            en: 'The right interface will appear for your role',
        }"
        :form-name="branchLabel?.name ?? { ar: 'فرع الزلفي', en: 'Zulfi Branch' }"
    >
        <template #header-actions>
            <Link href="/dashboard/clinic" class="role-back-btn">
                <LocalizedText :value="{ ar: 'تغيير الفرع', en: 'Change Branch' }" />
            </Link>
        </template>

        <div class="welcome-cards welcome-cards-3">
            <div
                class="welcome-card role-card-reception"
                role="button"
                tabindex="0"
                @click="chooseRole('reception')"
            >
                <h3>{{ 'الاستقبال' }}</h3>
                <p>{{ 'عرض الأرشيف والملفات المحفوظة' }}</p>
            </div>
            <div
                class="welcome-card role-card-doctor"
                role="button"
                tabindex="0"
                @click="chooseRole('doctor')"
            >
                <h3>الطبيب</h3>
                <p>جميع النماذج الطبية للأقسام</p>
            </div>
            <div
                class="welcome-card role-card-admin"
                role="button"
                tabindex="0"
                @click="chooseRole('admin')"
            >
                <h3>الإدارة</h3>
                <p>صلاحيات كاملة + لوحة الإدارة</p>
            </div>
        </div>
    </WelcomeScreen>
</template>
