<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'إنشاء حساب',
        description: 'أدخل بياناتك لإنشاء حساب جديد في النظام',
    },
});

const { isArabic } = useClinicLocale();
</script>

<template>
    <Head :title="isArabic ? 'إنشاء حساب' : 'Register'" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="field">
            <label for="name">{{ isArabic ? 'الاسم' : 'Name' }}</label>
            <input
                id="name"
                type="text"
                name="name"
                required
                autofocus
                autocomplete="name"
                :placeholder="isArabic ? 'الاسم الكامل' : 'Full name'"
            />
            <p v-if="errors.name" class="auth-error">{{ errors.name }}</p>
        </div>

        <div class="field">
            <label for="email">
                {{ isArabic ? 'البريد الإلكتروني' : 'Email address' }}
            </label>
            <input
                id="email"
                type="email"
                name="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />
            <p v-if="errors.email" class="auth-error">{{ errors.email }}</p>
        </div>

        <div class="field">
            <label for="password">
                {{ isArabic ? 'كلمة المرور' : 'Password' }}
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                :placeholder="isArabic ? 'كلمة المرور' : 'Password'"
                :passwordrules="passwordRules"
            />
            <p v-if="errors.password" class="auth-error">{{ errors.password }}</p>
        </div>

        <div class="field">
            <label for="password_confirmation">
                {{ isArabic ? 'تأكيد كلمة المرور' : 'Confirm password' }}
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                :placeholder="isArabic ? 'تأكيد كلمة المرور' : 'Confirm password'"
                :passwordrules="passwordRules"
            />
            <p v-if="errors.password_confirmation" class="auth-error">
                {{ errors.password_confirmation }}
            </p>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%" :disabled="processing">
            {{
                processing
                    ? isArabic
                        ? 'جاري الإنشاء...'
                        : 'Creating...'
                    : isArabic
                      ? 'إنشاء الحساب'
                      : 'Create account'
            }}
        </button>

        <p class="auth-muted">
            {{ isArabic ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
            <Link :href="login()" class="auth-link">
                {{ isArabic ? 'تسجيل الدخول' : 'Log in' }}
            </Link>
        </p>
    </Form>
</template>
