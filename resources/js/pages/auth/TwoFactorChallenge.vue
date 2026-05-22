<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { computed, ref, watchEffect } from 'vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { store } from '@/routes/two-factor/login';

const { isArabic } = useClinicLocale();
const showRecoveryInput = ref(false);
const code = ref('');

const authConfigContent = computed(() => {
    if (showRecoveryInput.value) {
        return {
            title: isArabic.value
                ? 'رمز الاسترداد'
                : 'Recovery code',
            description: isArabic.value
                ? 'أدخل أحد رموز الاسترداد الطارئة.'
                : 'Please confirm access by entering one of your emergency recovery codes.',
            toggle: isArabic.value
                ? 'استخدام رمز المصادقة'
                : 'Use an authentication code',
        };
    }

    return {
        title: isArabic.value ? 'رمز المصادقة' : 'Authentication code',
        description: isArabic.value
            ? 'أدخل الرمز من تطبيق المصادقة.'
            : 'Enter the code from your authenticator application.',
        toggle: isArabic.value
            ? 'استخدام رمز استرداد'
            : 'Use a recovery code',
    };
});

watchEffect(() => {
    setLayoutProps({
        title: authConfigContent.value.title,
        description: authConfigContent.value.description,
    });
});

function toggleRecoveryMode(clearErrors: () => void): void {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = '';
}
</script>

<template>
    <Head :title="authConfigContent.title" />

    <template v-if="!showRecoveryInput">
        <Form
            v-bind="store.form()"
            class="auth-form"
            reset-on-error
            @error="code = ''"
            #default="{ errors, processing, clearErrors }"
        >
            <input type="hidden" name="code" :value="code" />

            <div class="field">
                <label for="otp">
                    <LocalizedText
                        :value="{
                            ar: 'رمز من 6 أرقام',
                            en: '6-digit code',
                        }"
                    />
                </label>
                <input
                    id="otp"
                    v-model="code"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    autocomplete="one-time-code"
                    autofocus
                    :disabled="processing"
                    style="text-align: center; letter-spacing: 0.3em; font-size: 20px"
                />
                <p v-if="errors.code" class="auth-error">{{ errors.code }}</p>
            </div>

            <button
                type="submit"
                class="btn btn-primary"
                style="width: 100%"
                :disabled="processing || code.length < 6"
            >
                <LocalizedText :value="{ ar: 'متابعة', en: 'Continue' }" />
            </button>

            <p class="auth-muted" style="text-align: center">
                <LocalizedText :value="{ ar: 'أو', en: 'or' }" />
                <button
                    type="button"
                    class="auth-link"
                    style="background: none; border: none; cursor: pointer"
                    @click="toggleRecoveryMode(clearErrors)"
                >
                    {{ authConfigContent.toggle }}
                </button>
            </p>
        </Form>
    </template>

    <template v-else>
        <Form
            v-bind="store.form()"
            class="auth-form"
            reset-on-error
            #default="{ errors, processing, clearErrors }"
        >
            <div class="field">
                <label for="recovery_code">
                    <LocalizedText
                        :value="{
                            ar: 'رمز الاسترداد',
                            en: 'Recovery code',
                        }"
                    />
                </label>
                <input
                    id="recovery_code"
                    name="recovery_code"
                    type="text"
                    autocomplete="one-time-code"
                    :autofocus="showRecoveryInput"
                    required
                />
                <p v-if="errors.recovery_code" class="auth-error">
                    {{ errors.recovery_code }}
                </p>
            </div>

            <button
                type="submit"
                class="btn btn-primary"
                style="width: 100%"
                :disabled="processing"
            >
                <LocalizedText :value="{ ar: 'متابعة', en: 'Continue' }" />
            </button>

            <p class="auth-muted" style="text-align: center">
                <LocalizedText :value="{ ar: 'أو', en: 'or' }" />
                <button
                    type="button"
                    class="auth-link"
                    style="background: none; border: none; cursor: pointer"
                    @click="toggleRecoveryMode(clearErrors)"
                >
                    {{ authConfigContent.toggle }}
                </button>
            </p>
        </Form>
    </template>
</template>
