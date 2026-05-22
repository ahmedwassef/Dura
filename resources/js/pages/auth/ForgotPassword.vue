<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'استعادة كلمة المرور',
        description: 'أدخل بريدك الإلكتروني لاستلام رابط إعادة التعيين',
    },
});

defineProps<{
    status?: string;
}>();

const { isArabic } = useClinicLocale();
</script>

<template>
    <Head :title="isArabic ? 'نسيت كلمة المرور' : 'Forgot password'" />

    <div
        v-if="status"
        class="auth-muted"
        style="color: var(--green); margin-bottom: 8px"
    >
        {{ status }}
    </div>

    <Form v-bind="email.form()" v-slot="{ errors, processing }">
        <div class="field">
            <label for="email">
                {{ isArabic ? 'البريد الإلكتروني' : 'Email address' }}
            </label>
            <input
                id="email"
                type="email"
                name="email"
                autocomplete="off"
                autofocus
                placeholder="email@example.com"
            />
            <p v-if="errors.email" class="auth-error">{{ errors.email }}</p>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%" :disabled="processing">
            {{
                processing
                    ? isArabic
                        ? 'جاري الإرسال...'
                        : 'Sending...'
                    : isArabic
                      ? 'إرسال رابط إعادة التعيين'
                      : 'Email password reset link'
            }}
        </button>
    </Form>

    <p class="auth-muted" style="margin-top: 12px">
        {{ isArabic ? 'أو العودة إلى' : 'Or, return to' }}
        <Link :href="login()" class="auth-link">
            {{ isArabic ? 'تسجيل الدخول' : 'log in' }}
        </Link>
    </p>
</template>
