<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import FormCard from '@/clinic/components/ui/FormCard.vue';
import StartScreen from '@/clinic/components/layout/StartScreen.vue';
import {
    DENTAL_CATEGORY_CARDS,
    DENTAL_STANDALONE_FORMS,
} from '@/clinic/data/formTemplates';
import { FORM_CATEGORIES } from '@/clinic/data/categories';

function openForm(code: string): void {
    router.visit(`/dashboard/clinic/forms/${code}`);
}

function openCategory(code: string): void {
    router.visit(`/dashboard/clinic/category/${code}`);
}
</script>

<template>
    <Head title="قسم الأسنان" />

    <StartScreen
        :title="{ ar: 'قسم الأسنان', en: 'Dental Department' }"
        :subtitle="{
            ar: 'اختر الفئة لعرض النماذج المتاحة',
            en: 'Choose a category to view available forms',
        }"
        :form-name="{
            ar: 'قسم الأسنان - اختر الفئة',
            en: 'Dental Department - Choose Category',
        }"
        show-back
        back-href="/dashboard/clinic/home"
        :breadcrumbs="[
            { label: { ar: 'قسم الأسنان', en: 'Dental Department' } }
        ]"
    >
        <div class="dept-section">
            <div class="forms-grid">
                <FormCard
                    v-for="form in DENTAL_STANDALONE_FORMS"
                    :key="form.code"
                    :primary="form.primary"
                    :title="form.title"
                    :description="form.subtitle"
                    :badge="form.badge"
                    :icon-class="form.icon"
                    @click="openForm(form.code)"
                />

                <FormCard
                    v-for="cat in DENTAL_CATEGORY_CARDS"
                    :key="cat.code"
                    :title="FORM_CATEGORIES[cat.code].title"
                    :description="{
                        ar: FORM_CATEGORIES[cat.code].forms.map((f) => f.title.ar).join('، '),
                        en: FORM_CATEGORIES[cat.code].forms.map((f) => f.title.en).join(', '),
                    }"
                    :badge="cat.count"
                    icon-class="surgery-icon"
                    @click="openCategory(cat.code)"
                />
            </div>
        </div>
    </StartScreen>
</template>
