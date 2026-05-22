<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'تأكيد كلمة المرور',
        description: 'منطقة آمنة — يرجى تأكيد كلمة المرور للمتابعة',
    },
});

const { isArabic } = useClinicLocale();
</script>

<template>
    <Head :title="isArabic ? 'تأكيد كلمة المرور' : 'Confirm password'" />

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
    >
        <div class="field">
            <label for="password">
                {{ isArabic ? 'كلمة المرور' : 'Password' }}
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                autofocus
            />
            <p v-if="errors.password" class="auth-error">{{ errors.password }}</p>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%" :disabled="processing">
            {{
                processing
                    ? isArabic
                        ? 'جاري التأكيد...'
                        : 'Confirming...'
                    : isArabic
                      ? 'تأكيد'
                      : 'Confirm password'
            }}
        </button>
    </Form>
</template>
