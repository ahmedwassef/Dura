<script setup lang="ts">
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import type { LocalizedString } from '@/clinic/types/clinic';

defineProps<{
    formName?: LocalizedString | string;
}>();

const { isArabic } = useClinicLocale();
</script>

<template>
    <div class="brand-block">
        <div class="logo-mark">
            <div
                class="durat-logo-img"
                role="img"
                :aria-label="isArabic ? 'مجمع درة النخبة الطبي' : 'Durat Alnukhba Medical Complex'"
            />
        </div>
        <div class="brand-text">
            <h1>
                <LocalizedText
                    :value="{
                        ar: 'مجمع درة النخبة الطبي',
                        en: 'Durat Alnukhba Medical Complex',
                    }"
                />
            </h1>
            <div class="en-name">Durat Alnukhba Medical Complex</div>
            <div v-if="formName" class="form-name">
                <LocalizedText :value="formName" />
            </div>
        </div>
    </div>
</template>
