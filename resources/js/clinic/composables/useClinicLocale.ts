import { computed, ref, watch } from 'vue';
import type { ClinicLang, LocalizedString } from '@/clinic/types/clinic';

const lang = ref<ClinicLang>('ar');

export function useClinicLocale() {
    const isArabic = computed(() => lang.value === 'ar');

    function setLang(next: ClinicLang): void {
        lang.value = next;
        if (typeof document !== 'undefined') {
            document.body.dataset.lang = next;
            document.documentElement.lang = next;
            document.documentElement.dir = next === 'ar' ? 'rtl' : 'ltr';
        }
    }

    function t(value: LocalizedString | string): string {
        if (typeof value === 'string') {
            return value;
        }

        return value[lang.value] ?? value.ar;
    }

    watch(
        lang,
        (value) => {
            if (typeof document === 'undefined') return;
            document.body.dataset.lang = value;
            document.documentElement.lang = value;
            document.documentElement.dir = value === 'ar' ? 'rtl' : 'ltr';
        },
        { immediate: true },
    );

    return {
        lang,
        isArabic,
        setLang,
        t,
    };
}
