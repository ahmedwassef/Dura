<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'تأكيد البريد الإلكتروني',
        description: 'يرجى تأكيد بريدك عبر الرابط الذي أرسلناه إليك',
    },
});

defineProps<{
    status?: string;
}>();

const { isArabic } = useClinicLocale();
</script>

<template>
    <Head :title="isArabic ? 'تأكيد البريد' : 'Email verification'" />

    <div
        v-if="status === 'verification-link-sent'"
        class="auth-muted"
        style="color: var(--green); margin-bottom: 8px"
    >
        {{
            isArabic
                ? 'تم إرسال رابط تأكيد جديد إلى بريدك الإلكتروني'
                : 'A new verification link has been sent to your email address.'
        }}
    </div>

    <Form v-bind="send.form()" v-slot="{ processing }" class="auth-form">
        <button type="submit" class="btn btn-primary" style="width: 100%" :disabled="processing">
            {{
                processing
                    ? isArabic
                        ? 'جاري الإرسال...'
                        : 'Sending...'
                    : isArabic
                      ? 'إعادة إرسال رابط التأكيد'
                      : 'Resend verification email'
            }}
        </button>

        <Link
            :href="logout()"
            as="button"
            method="post"
            class="auth-link auth-muted"
            style="display: block; margin-top: 12px"
        >
            {{ isArabic ? 'تسجيل الخروج' : 'Log out' }}
        </Link>
    </Form>
</template>
