<script setup lang="ts">
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import type { ClinicLang } from '@/clinic/types/clinic';

const { lang, setLang } = useClinicLocale();

function onSelect(next: ClinicLang): void {
    setLang(next);
}
</script>

<template>
    <div class="lang-switch">
        <button
            type="button"
            class="lang-btn"
            :class="{ active: lang === 'ar' }"
            @click="onSelect('ar')"
        >
            العربية
        </button>
        <button
            type="button"
            class="lang-btn"
            :class="{ active: lang === 'en' }"
            @click="onSelect('en')"
        >
            English
        </button>
    </div>
</template>
