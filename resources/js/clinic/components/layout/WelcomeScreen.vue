<script setup lang="ts">
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import type { LocalizedString } from '@/clinic/types/clinic';

defineProps<{
    title: LocalizedString;
    subtitle: LocalizedString;
    formName?: LocalizedString | string;
    headerClass?: string;
}>();
</script>

<template>
    <div class="start-screen welcome-screen">
        <ClinicHeader :form-name="formName" :header-class="headerClass">
            <template v-if="$slots['header-actions']" #actions>
                <slot name="header-actions" />
            </template>
        </ClinicHeader>

        <div class="welcome-content">
            <div v-if="$slots.icon" class="welcome-icon-circle">
                <slot name="icon" />
            </div>
            <h2 class="welcome-title">
                <LocalizedText :value="title" />
            </h2>
            <p class="welcome-subtitle">
                <LocalizedText :value="subtitle" />
            </p>
            <slot />
        </div>

        <div class="start-footer">
            <LocalizedText
                :value="{
                    ar: '© مجمع درة النخبة الطبي — جميع الحقوق محفوظة',
                    en: '© Durat Alnukhba Medical Complex — All Rights Reserved',
                }"
            />
        </div>
    </div>
</template>
