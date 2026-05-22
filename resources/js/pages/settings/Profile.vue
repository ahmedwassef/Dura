<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { destroy } from '@/routes/profile';
import { send } from '@/routes/verification';

defineProps<{
    mustVerifyEmail: boolean;
    status?: string;
}>();

const { isArabic } = useClinicLocale();
const page = usePage();
const user = computed(() => page.props.auth?.user as {
    name: string;
    email: string;
    email_verified_at?: string | null;
});
const showDeleteConfirm = ref(false);
</script>

<template>
    <Head :title="isArabic ? 'الملف الشخصي' : 'Profile'" />

    <div class="settings-section">
        <div class="settings-section-head">
            <h3>
                <LocalizedText
                    :value="{
                        ar: 'معلومات الملف الشخصي',
                        en: 'Profile information',
                    }"
                />
            </h3>
            <p>
                <LocalizedText
                    :value="{
                        ar: 'تحديث الاسم والبريد الإلكتروني',
                        en: 'Update your name and email address',
                    }"
                />
            </p>
        </div>

        <Form
            v-bind="ProfileController.update.form()"
            class="settings-form"
            v-slot="{ errors, processing }"
        >
            <div class="field">
                <label for="name">
                    <LocalizedText :value="{ ar: 'الاسم', en: 'Name' }" />
                </label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    required
                    autocomplete="name"
                    :default-value="user?.name"
                />
                <p v-if="errors.name" class="auth-error">{{ errors.name }}</p>
            </div>

            <div class="field">
                <label for="email">
                    <LocalizedText :value="{ ar: 'البريد', en: 'Email' }" />
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autocomplete="username"
                    :default-value="user?.email"
                />
                <p v-if="errors.email" class="auth-error">{{ errors.email }}</p>
            </div>

            <div
                v-if="mustVerifyEmail && !user?.email_verified_at"
                class="settings-note"
            >
                <p>
                    <LocalizedText
                        :value="{
                            ar: 'بريدك غير مُفعّل.',
                            en: 'Your email is unverified.',
                        }"
                    />
                    <Link :href="send()" as="button" class="auth-link">
                        <LocalizedText
                            :value="{
                                ar: 'إعادة إرسال رابط التفعيل',
                                en: 'Resend verification email',
                            }"
                        />
                    </Link>
                </p>
                <p
                    v-if="status === 'verification-link-sent'"
                    style="color: var(--green); margin-top: 8px"
                >
                    <LocalizedText
                        :value="{
                            ar: 'تم إرسال رابط التفعيل.',
                            en: 'Verification link sent.',
                        }"
                    />
                </p>
            </div>

            <button type="submit" class="btn btn-primary" :disabled="processing">
                <LocalizedText :value="{ ar: 'حفظ', en: 'Save' }" />
            </button>
        </Form>
    </div>

    <div class="settings-section settings-danger">
        <div class="settings-section-head">
            <h3>
                <LocalizedText
                    :value="{ ar: 'حذف الحساب', en: 'Delete account' }"
                />
            </h3>
            <p>
                <LocalizedText
                    :value="{
                        ar: 'سيتم حذف حسابك وبياناتك نهائياً',
                        en: 'Permanently delete your account and data',
                    }"
                />
            </p>
        </div>

        <button
            v-if="!showDeleteConfirm"
            type="button"
            class="btn btn-outline"
            style="border-color: var(--red); color: var(--red)"
            @click="showDeleteConfirm = true"
        >
            <LocalizedText :value="{ ar: 'حذف الحساب', en: 'Delete account' }" />
        </button>

        <Form
            v-else
            v-bind="destroy.form()"
            class="settings-form"
            v-slot="{ errors, processing }"
        >
            <div class="field">
                <label for="delete-password">
                    <LocalizedText
                        :value="{
                            ar: 'أدخل كلمة المرور للتأكيد',
                            en: 'Enter your password to confirm',
                        }"
                    />
                </label>
                <input
                    id="delete-password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                />
                <p v-if="errors.password" class="auth-error">
                    {{ errors.password }}
                </p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap">
                <button
                    type="submit"
                    class="btn btn-primary"
                    style="background: var(--red)"
                    :disabled="processing"
                >
                    <LocalizedText
                        :value="{
                            ar: 'تأكيد الحذف',
                            en: 'Confirm delete',
                        }"
                    />
                </button>
                <button
                    type="button"
                    class="btn btn-outline"
                    @click="showDeleteConfirm = false"
                >
                    <LocalizedText :value="{ ar: 'إلغاء', en: 'Cancel' }" />
                </button>
            </div>
        </Form>
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
}
.settings-danger {
    padding-top: 24px;
    border-top: 1px solid var(--line);
}
</style>
