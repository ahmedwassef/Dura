<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { onUnmounted, ref, watch } from 'vue';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { confirm, disable, enable } from '@/routes/two-factor';

type Props = {
    canManageTwoFactor?: boolean;
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
    passwordRules: string;
};

const props = withDefaults(defineProps<Props>(), {
    canManageTwoFactor: false,
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const { isArabic } = useClinicLocale();
const {
    qrCodeSvg,
    manualSetupKey,
    recoveryCodesList,
    errors: twoFactorErrors,
    hasSetupData,
    clearTwoFactorAuthData,
    fetchSetupData,
    fetchRecoveryCodes,
} = useTwoFactorAuth();

const showSetupPanel = ref(false);
const showRecoveryCodes = ref(false);
const confirmCode = ref('');

onUnmounted(() => clearTwoFactorAuthData());

watch(showSetupPanel, async (open) => {
    if (open && !hasSetupData.value) {
        await fetchSetupData();
    }
});

async function onEnableSuccess(): Promise<void> {
    showSetupPanel.value = true;
    await fetchSetupData();
}

function onConfirmSuccess(): void {
    showSetupPanel.value = false;
    confirmCode.value = '';
    router.reload();
}

async function toggleRecoveryCodes(): Promise<void> {
    showRecoveryCodes.value = !showRecoveryCodes.value;
    if (showRecoveryCodes.value && recoveryCodesList.value.length === 0) {
        await fetchRecoveryCodes();
    }
}
</script>

<template>
    <Head :title="isArabic ? 'الأمان' : 'Security'" />

    <div class="settings-section">
        <div class="settings-section-head">
            <h3>
                <LocalizedText
                    :value="{ ar: 'تحديث كلمة المرور', en: 'Update password' }"
                />
            </h3>
            <p>
                <LocalizedText
                    :value="{
                        ar: 'استخدم كلمة مرور قوية وفريدة',
                        en: 'Use a long, random password to stay secure',
                    }"
                />
            </p>
        </div>

        <Form
            v-bind="SecurityController.update.form()"
            :options="{ preserveScroll: true }"
            reset-on-success
            :reset-on-error="[
                'password',
                'password_confirmation',
                'current_password',
            ]"
            class="settings-form"
            v-slot="{ errors, processing }"
        >
            <div class="field">
                <label for="current_password">
                    <LocalizedText
                        :value="{
                            ar: 'كلمة المرور الحالية',
                            en: 'Current password',
                        }"
                    />
                </label>
                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    required
                />
                <p v-if="errors.current_password" class="auth-error">
                    {{ errors.current_password }}
                </p>
            </div>

            <div class="field">
                <label for="password">
                    <LocalizedText
                        :value="{ ar: 'كلمة المرور الجديدة', en: 'New password' }"
                    />
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    :passwordrules="props.passwordRules"
                    required
                />
                <p v-if="errors.password" class="auth-error">
                    {{ errors.password }}
                </p>
            </div>

            <div class="field">
                <label for="password_confirmation">
                    <LocalizedText
                        :value="{
                            ar: 'تأكيد كلمة المرور',
                            en: 'Confirm password',
                        }"
                    />
                </label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                />
                <p v-if="errors.password_confirmation" class="auth-error">
                    {{ errors.password_confirmation }}
                </p>
            </div>

            <button type="submit" class="btn btn-primary" :disabled="processing">
                <LocalizedText
                    :value="{ ar: 'حفظ كلمة المرور', en: 'Save password' }"
                />
            </button>
        </Form>
    </div>

    <div v-if="canManageTwoFactor" class="settings-section">
        <div class="settings-section-head">
            <h3>
                <LocalizedText
                    :value="{
                        ar: 'المصادقة الثنائية',
                        en: 'Two-factor authentication',
                    }"
                />
            </h3>
            <p>
                <LocalizedText
                    :value="{
                        ar: 'طبقة أمان إضافية لحسابك',
                        en: 'Add an extra layer of security to your account',
                    }"
                />
            </p>
        </div>

        <template v-if="!twoFactorEnabled">
            <p class="settings-note">
                <LocalizedText
                    :value="{
                        ar: 'عند التفعيل سيُطلب منك رمز من تطبيق المصادقة عند تسجيل الدخول.',
                        en: 'When enabled, you will be prompted for a code from your authenticator app when signing in.',
                    }"
                />
            </p>

            <button
                v-if="hasSetupData"
                type="button"
                class="btn btn-primary"
                @click="showSetupPanel = true"
            >
                <LocalizedText
                    :value="{ ar: 'متابعة الإعداد', en: 'Continue setup' }"
                />
            </button>
            <Form
                v-else
                v-bind="enable.form()"
                @success="onEnableSuccess"
                #default="{ processing }"
            >
                <button type="submit" class="btn btn-primary" :disabled="processing">
                    <LocalizedText :value="{ ar: 'تفعيل 2FA', en: 'Enable 2FA' }" />
                </button>
            </Form>
        </template>

        <template v-else>
            <p class="settings-note">
                <LocalizedText
                    :value="{
                        ar: 'المصادقة الثنائية مفعّلة على حسابك.',
                        en: 'Two-factor authentication is enabled on your account.',
                    }"
                />
            </p>

            <Form v-bind="disable.form()" #default="{ processing }">
                <button
                    type="submit"
                    class="btn btn-outline"
                    style="border-color: var(--red); color: var(--red)"
                    :disabled="processing"
                >
                    <LocalizedText :value="{ ar: 'إيقاف 2FA', en: 'Disable 2FA' }" />
                </button>
            </Form>

            <div class="settings-2fa-actions">
                <button
                    type="button"
                    class="btn btn-outline"
                    @click="toggleRecoveryCodes"
                >
                    <LocalizedText
                        :value="{
                            ar: 'رموز الاسترداد',
                            en: 'Recovery codes',
                        }"
                    />
                </button>
            </div>

            <ul v-if="showRecoveryCodes && recoveryCodesList.length" class="recovery-codes">
                <li v-for="code in recoveryCodesList" :key="code">
                    <code>{{ code }}</code>
                </li>
            </ul>
        </template>

        <div
            v-if="showSetupPanel && !twoFactorEnabled"
            class="settings-2fa-setup admin-table-card"
            style="margin-top: 20px"
        >
            <h4>
                <LocalizedText
                    :value="{ ar: 'إكمال الإعداد', en: 'Finish setup' }"
                />
            </h4>

            <div v-if="qrCodeSvg" class="qr-wrap" v-html="qrCodeSvg" />

            <p v-if="manualSetupKey" class="settings-note">
                <LocalizedText :value="{ ar: 'المفتاح:', en: 'Setup key:' }" />
                <code>{{ manualSetupKey }}</code>
            </p>

            <Form
                v-bind="confirm.form()"
                class="settings-form"
                @success="onConfirmSuccess"
                #default="{ errors, processing }"
            >
                <div class="field">
                    <label for="code">
                        <LocalizedText
                            :value="{
                                ar: 'رمز المصادقة',
                                en: 'Authentication code',
                            }"
                        />
                    </label>
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        autocomplete="one-time-code"
                        v-model="confirmCode"
                        required
                    />
                    <p v-if="errors.code" class="auth-error">{{ errors.code }}</p>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="processing">
                    <LocalizedText :value="{ ar: 'تأكيد', en: 'Confirm' }" />
                </button>
            </Form>

            <p v-for="err in twoFactorErrors" :key="err" class="auth-error">
                {{ err }}
            </p>

            <button
                type="button"
                class="btn btn-outline"
                style="margin-top: 12px"
                @click="showSetupPanel = false"
            >
                <LocalizedText :value="{ ar: 'إلغاء', en: 'Cancel' }" />
            </button>
        </div>
    </div>
</template>

<style scoped>
.settings-section {
    margin-bottom: 32px;
}
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
.settings-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 480px;
}
.settings-form .field label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 700;
    color: var(--ink-soft);
}
.settings-form .field input {
    width: 100%;
}
.settings-note {
    font-size: 13px;
    color: var(--ink-mute);
    margin-bottom: 16px;
    line-height: 1.5;
}
.settings-2fa-actions {
    margin-top: 16px;
}
.settings-2fa-setup {
    padding: 20px;
}
.settings-2fa-setup h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 12px;
}
.qr-wrap {
    margin: 12px 0;
}
.qr-wrap :deep(svg) {
    max-width: 200px;
    height: auto;
}
.recovery-codes {
    margin-top: 16px;
    padding: 16px;
    background: var(--bg-soft);
    border-radius: 10px;
    list-style: none;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px;
}
.recovery-codes code {
    font-family: monospace;
    font-size: 13px;
}
</style>
