<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useAppearance, type Appearance } from '@/composables/useAppearance';

const { isArabic } = useClinicLocale();
const { appearance, updateAppearance } = useAppearance();

const options: Array<{ value: Appearance; label: { ar: string; en: string } }> = [
    { value: 'light', label: { ar: 'فاتح', en: 'Light' } },
    { value: 'dark', label: { ar: 'داكن', en: 'Dark' } },
    { value: 'system', label: { ar: 'النظام', en: 'System' } },
];
</script>

<template>
    <Head :title="isArabic ? 'المظهر' : 'Appearance'" />

    <div class="settings-section">
        <div class="settings-section-head">
            <h3>
                <LocalizedText
                    :value="{ ar: 'إعدادات المظهر', en: 'Appearance settings' }"
                />
            </h3>
            <p>
                <LocalizedText
                    :value="{
                        ar: 'اختر مظهر الواجهة',
                        en: 'Choose how the interface looks',
                    }"
                />
            </p>
        </div>

        <div class="appearance-tabs">
            <button
                v-for="option in options"
                :key="option.value"
                type="button"
                class="appearance-tab"
                :class="{ active: appearance === option.value }"
                @click="updateAppearance(option.value)"
            >
                <LocalizedText :value="option.label" />
            </button>
        </div>
    </div>
</template>

<style scoped>
.settings-section-head h3 {
    font-size: 18px;
    font-weight: 800;
    color: var(--ink);
    margin-bottom: 4px;
}
.settings-section-head p {
    font-size: 13px;
    color: var(--ink-mute);
    margin-bottom: 16px;
}
.appearance-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.appearance-tab {
    padding: 10px 20px;
    border-radius: 10px;
    border: 1.5px solid var(--line);
    background: var(--bg-card);
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    color: var(--ink-mute);
    cursor: pointer;
    transition: all 0.15s ease;
}
.appearance-tab:hover {
    border-color: var(--primary);
    color: var(--primary);
}
.appearance-tab.active {
    background: var(--primary-soft);
    border-color: var(--primary);
    color: var(--primary);
}
</style>
