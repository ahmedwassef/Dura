<script setup lang="ts">
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import FormCard from '@/clinic/components/ui/FormCard.vue';
import StartScreen from '@/clinic/components/layout/StartScreen.vue';
import { FORM_CATEGORIES } from '@/clinic/data/categories';
import type { CategoryCode } from '@/clinic/types/clinic';

const props = defineProps<{
    category: string;
}>();

const categoryData = computed(() => FORM_CATEGORIES[props.category as CategoryCode]);

const backHref = computed(() => {
    if (props.category.startsWith('derm_')) {
        return '/dashboard/clinic/dermatology';
    }

    return '/dashboard/clinic/dental';
});

function openForm(code: string): void {
    router.visit(`/dashboard/clinic/forms/${code}`);
}
</script>

<template>
    <Head :title="categoryData?.title.ar ?? 'النماذج'" />

    <StartScreen
        v-if="categoryData"
        :title="categoryData.title"
        :subtitle="{
            ar: 'اختر النموذج المطلوب',
            en: 'Choose the required form',
        }"
        :form-name="categoryData.header"
        show-back
        :back-href="backHref"
        :breadcrumbs="[
            { 
                label: category.startsWith('derm_') ? { ar: 'قسم الجلدية', en: 'Dermatology' } : { ar: 'قسم الأسنان', en: 'Dental Department' },
                href: category.startsWith('derm_') ? '/dashboard/clinic/dermatology' : '/dashboard/clinic/dental'
            },
            { label: categoryData.title }
        ]"
    >
        <div class="dept-section">
            <div class="forms-grid">
                <FormCard
                    v-for="form in categoryData.forms"
                    :key="form.type"
                    :title="form.title"
                    :description="form.description"
                    :icon-class="form.icon"
                    :bilingual="form.bilingual"
                    :ar-only="form.arOnly"
                    :badge="{ ar: 'ابدأ التعبئة', en: 'Start filling' }"
                    @click="openForm(form.type)"
                />
            </div>
        </div>
    </StartScreen>
</template>
