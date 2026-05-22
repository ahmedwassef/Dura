<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'إعادة تعيين كلمة المرور',
        description: 'أدخل كلمة المرور الجديدة أدناه',
    },
});

const props = defineProps<{
    token: string;
    email: string;
    passwordRules: string;
}>();

const { isArabic } = useClinicLocale();
const inputEmail = ref(props.email);
</script>

<template>
    <Head :title="isArabic ? 'إعادة تعيين كلمة المرور' : 'Reset password'" />

    <Form
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="field">
            <label for="email">{{ isArabic ? 'البريد' : 'Email' }}</label>
            <input
                id="email"
                v-model="inputEmail"
                type="email"
                name="email"
                readonly
                autocomplete="email"
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
                autofocus
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
                        ? 'جاري الحفظ...'
                        : 'Saving...'
                    : isArabic
                      ? 'إعادة التعيين'
                      : 'Reset password'
            }}
        </button>
    </Form>
</template>
