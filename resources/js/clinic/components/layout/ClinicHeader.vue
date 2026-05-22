<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppNavbar from '@/clinic/components/layout/AppNavbar.vue';
import ClinicBrand from '@/clinic/components/layout/ClinicBrand.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import Breadcrumbs from '@/clinic/components/ui/Breadcrumbs.vue';
import { useClinicSession } from '@/clinic/composables/useClinicSession';
import { useLocalizedDate } from '@/clinic/composables/useLocalizedDate';
import { CLINIC_BASE } from '@/clinic/constants';
import type { LocalizedString } from '@/clinic/types/clinic';

withDefaults(
    defineProps<{
        formName?: LocalizedString | string;
        headerClass?: string;
        showBranch?: boolean;
        showDate?: boolean;
        showArchive?: boolean;
        showAdmin?: boolean;
        showBack?: boolean;
        backHref?: string;
        /** Show the app nav bar below the header (disable on admin CRUD — AdminPageLayout adds its own). */
        showNavbar?: boolean;
        showTopBar?: boolean;
        breadcrumbs?: Array<{ label: any; href?: string }>;
    }>(),
    {
        showNavbar: true,
    }
);

const { branch, selectBranch } = useClinicSession();
const { today } = useLocalizedDate();

function onBranchChange(event: Event): void {
    const value = (event.target as HTMLSelectElement).value;
    if (value === 'zulfi' || value === 'dawadmi') {
        selectBranch(value);
    }
}
</script>

<template>
    <div class="clinic-header-stack">
        <header   class="top-header" :class="headerClass">
            <div class="header-inner">
                <ClinicBrand :form-name="formName" />

                <div class="header-controls">
                    <div v-if="showDate" class="date-pill">
                        <span class="label">
                            <LocalizedText :value="{ ar: 'التاريخ', en: 'Date' }" />
                        </span>
                        {{ today }}
                    </div>

                    <select
                        v-if="showBranch && branch"
                        class="branch-pill"
                        :value="branch"
                        @change="onBranchChange"
                    >
                        <option value="zulfi">فرع الزلفي / Zulfi</option>
                        <option value="dawadmi">فرع الدوادمي / Dawadmi</option>
                    </select>

                    <Link
                        v-if="showArchive"
                        :href="`${CLINIC_BASE}/archive`"
                        class="archive-btn-header"
                    >
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path
                                d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27z"
                            />
                        </svg>
                        <LocalizedText :value="{ ar: 'الأرشيف', en: 'Archive' }" />
                    </Link>

                    <Link
                        v-if="showAdmin"
                        href="/dashboard"
                        class="admin-btn-header"
                    >
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path
                                d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"
                            />
                        </svg>
                        <LocalizedText :value="{ ar: 'الإدارة', en: 'Admin' }" />
                    </Link>

                    <Link
                        v-if="showBack && backHref"
                        :href="backHref"
                        class="admin-btn-header"
                        style="background: rgba(255,255,255,.15); color: white; border-color: rgba(255,255,255,.25)"
                    >
                        <LocalizedText :value="{ ar: 'رجوع', en: 'Back' }" />
                    </Link>

                    <slot name="actions" />
                </div>
            </div>
        </header>
        <AppNavbar v-if="showNavbar !== false" />
        <Breadcrumbs v-if="breadcrumbs && breadcrumbs.length > 0" :items="breadcrumbs" />
    </div>
</template>

<style scoped>
.clinic-header-stack {
    position: sticky;
    top: 0;
    z-index: 40;
    overflow: visible;
}
</style>
