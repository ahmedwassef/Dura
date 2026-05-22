<script setup lang="ts">
import AdminNavbar from '@/clinic/components/layout/AdminNavbar.vue';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import type { LocalizedString } from '@/clinic/types/clinic';

defineProps<{
    formName: LocalizedString | string;
    headerClass?: string;
    showBack?: boolean;
    backHref?: string;
}>();
</script>

<template>
    <div class="admin-screen active">
        <ClinicHeader
            :form-name="formName"
            :header-class="headerClass"
            :show-back="showBack"
            :back-href="backHref"
            :show-navbar="false"
            show-branch
            show-date
            show-archive
        />

        <AdminNavbar />

        <div class="admin-content">
            <slot />
        </div>
    </div>
</template>
