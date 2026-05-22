<script setup lang="ts">
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import type { LocalizedString } from '@/clinic/types/clinic';

defineProps<{
    title: LocalizedString;
    subtitle: LocalizedString;
    formName?: LocalizedString | string;
    headerClass?: string;
    showBranch?: boolean;
    showDate?: boolean;
    showArchive?: boolean;
    showAdmin?: boolean;
    showBack?: boolean;
    backHref?: string;
    breadcrumbs?: Array<{ label: any; href?: string }>;
}>();
</script>

<template>
    <div class="start-screen">
        <ClinicHeader
            :form-name="formName"
            :header-class="headerClass"
            :show-branch="showBranch"
            :show-date="showDate"
            :show-archive="showArchive"
            :show-admin="showAdmin"
            :show-back="showBack"
            :back-href="backHref"
            :breadcrumbs="breadcrumbs"
        />

        <div class="start-content">
            <h2><LocalizedText :value="title" /></h2>
            <p><LocalizedText :value="subtitle" /></p>
            <slot />
        </div>

        <div class="start-footer">
            <LocalizedText
                :value="{
                    ar: '© مجمع درة النخبة الطبي',
                    en: '© Durat Alnukhba Medical Complex',
                }"
            />
        </div>
    </div>
</template>
