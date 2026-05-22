<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';

defineProps<{
    title?: string;
    description?: string;
}>();

const { isArabic } = useClinicLocale();
</script>

<template>
    <ClinicHeader :show-top-bar="false" />
    <div class="admin-login-body admin-login" :dir="isArabic ? 'rtl' : 'ltr'">
        <div class="admin-login-card auth-login-card">
            <div class="admin-lock" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"
                    />
                </svg>
            </div>

            <h2>{{ title }}</h2>
            <p v-if="description">{{ description }}</p>

            <div class="auth-form">
                <slot />
            </div>

           
        </div>

    </div>
</template>

<style scoped>
.auth-login-card {
    text-align: start;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-top: 8px;
}

.auth-form :deep(.field) {
    margin-bottom: 0;
}

.auth-form :deep(.field label) {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 700;
    color: var(--ink-soft);
}

.auth-form :deep(.field input) {
    width: 100%;
}

.auth-form :deep(.auth-row) {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    font-size: 13px;
}

.auth-form :deep(.auth-error) {
    color: var(--red);
    font-size: 12px;
    margin-top: 4px;
}

.auth-form :deep(.auth-link) {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
}

.auth-form :deep(.auth-link:hover) {
    text-decoration: underline;
}

.auth-form :deep(.auth-muted) {
    text-align: center;
    font-size: 13px;
    color: var(--ink-mute);
}

.auth-back-link {
    display: inline-block;
    margin-top: 20px;
    font-size: 13px;
    font-weight: 600;
    color: var(--primary);
    text-decoration: none;
}

.auth-back-link:hover {
    text-decoration: underline;
}
</style>
