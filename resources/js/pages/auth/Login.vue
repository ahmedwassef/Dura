<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'تسجيل الدخول',
        description: 'أدخل بريدك الإلكتروني وكلمة المرور للدخول إلى النظام',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const { isArabic } = useClinicLocale();
</script>

<template>
    <Head :title="isArabic ? 'تسجيل الدخول' : 'Log in'" />

    <div
        v-if="status"
        class="auth-muted"
        style="color: var(--green); margin-bottom: 8px"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
    >
        <div class="field">
            <label for="email">
                {{ isArabic ? 'البريد الإلكتروني' : 'Email address' }}
            </label>
            <input
                id="email"
                type="email"
                name="email"
                required
                autofocus
                autocomplete="email"
                :placeholder="isArabic ? 'email@example.com' : 'email@example.com'"
            />
            <p v-if="errors.email" class="auth-error">{{ errors.email }}</p>
        </div>

        <div class="field">
            <div class="auth-row">
                <label for="password">
                    {{ isArabic ? 'كلمة المرور' : 'Password' }}
                </label>
                <Link
                    v-if="canResetPassword"
                    :href="request()"
                    class="auth-link"
                >
                    {{ isArabic ? 'نسيت كلمة المرور؟' : 'Forgot password?' }}
                </Link>
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                :placeholder="isArabic ? 'كلمة المرور' : 'Password'"
            />
            <p v-if="errors.password" class="auth-error">{{ errors.password }}</p>
        </div>

        <div class="auth-row">
            <label for="remember" style="display: flex; align-items: center; gap: 8px">
                <input id="remember" type="checkbox" name="remember" />
                <span>{{ isArabic ? 'تذكرني' : 'Remember me' }}</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%" :disabled="processing">
            {{ processing ? (isArabic ? 'جاري الدخول...' : 'Signing in...') : isArabic ? 'دخول' : 'Log in' }}
        </button>
 
    </Form>
</template>
