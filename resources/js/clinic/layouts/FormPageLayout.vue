<script setup lang="ts">
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import type { LocalizedString } from '@/clinic/types/clinic';

defineProps<{
    formName: LocalizedString | string;
    backHref?: string;
    breadcrumbs?: Array<{ label: any; href?: string }>;
}>();
</script>

<template>
    <div class="form-page active">
        <ClinicHeader
            :form-name="formName"
            show-branch
            show-date
            :show-back="!!backHref"
            :back-href="backHref"
            :breadcrumbs="breadcrumbs"
        />
        <div class="form-container">
            <slot />
        </div>
        <div v-if="$slots.actions" class="action-bar">
            <slot name="actions" />
        </div>
    </div>
</template>
