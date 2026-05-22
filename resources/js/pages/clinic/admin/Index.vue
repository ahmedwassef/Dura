<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useLocalizedDate } from '@/clinic/composables/useLocalizedDate';

defineProps<{
    submissions: Array<{
        id: string;
        typeName?: string;
        typeNameEn?: string;
        patientName?: string;
        fileNum?: string;
        doctor?: string;
        date?: string;
        status?: string;
        creator?: string;
    }>;
}>();

const { isArabic } = useClinicLocale();
const { formatDateTime } = useLocalizedDate();
const search = ref('');

let searchTimer: ReturnType<typeof setTimeout> | undefined;

watch(search, (value) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            '/dashboard/clinic/records',
            { search: value || undefined },
            { preserveState: true, replace: true },
        );
    }, 300);
});
</script>

<template>
    <Head :title="isArabic ? 'سجلات النماذج' : 'Form Records'" />

    <AdminPageLayout
        :form-name="{
            ar: 'إحصائيات وسجلات النماذج',
            en: 'Forms Statistics & Records',
        }"
        show-back
        back-href="/dashboard/clinic/home"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText
                        :value="{ ar: 'سجلات النماذج', en: 'Form Records' }"
                    />
                </h2>
                <p>
                    {{
                        isArabic
                            ? 'جميع النماذج المحفوظة في النظام'
                            : 'All forms saved in the system'
                    }}
                </p>
            </div>
            <Link href="/dashboard/clinic/archive" class="btn btn-outline">
                <LocalizedText :value="{ ar: 'الأرشيف', en: 'Archive' }" />
            </Link>
        </div>

        <div class="admin-table-card">
            <div class="admin-filters">
                <input
                    v-model="search"
                    type="text"
                    class="admin-search"
                    :placeholder="
                        isArabic
                            ? 'ابحث بالاسم أو رقم الملف...'
                            : 'Search by name or file no...'
                    "
                />
            </div>

            <div v-if="submissions.length === 0" class="arch-empty">
                <p>
                    <LocalizedText
                        :value="{
                            ar: 'لا توجد سجلات بعد — ستظهر بعد حفظ النماذج',
                            en: 'No records yet — they will appear after saving forms',
                        }"
                    />
                </p>
            </div>

            <div v-else style="overflow-x: auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>{{ isArabic ? 'نوع النموذج' : 'Form type' }}</th>
                            <th>{{ isArabic ? 'المريض' : 'Patient' }}</th>
                            <th>{{ isArabic ? 'الملف' : 'File' }}</th>
                            <th>{{ isArabic ? 'الطبيب' : 'Doctor' }}</th>
                            <th>{{ isArabic ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ isArabic ? 'الحالة' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in submissions" :key="row.id">
                            <td>
                                {{
                                    isArabic
                                        ? row.typeName
                                        : row.typeNameEn || row.typeName
                                }}
                            </td>
                            <td>{{ row.patientName }}</td>
                            <td>{{ row.fileNum || '—' }}</td>
                            <td>{{ row.doctor || '—' }}</td>
                            <td>
                                {{ row.date ? formatDateTime(row.date) : '—' }}
                            </td>
                            <td>{{ row.status || '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminPageLayout>
</template>
