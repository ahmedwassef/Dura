<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';

const props = defineProps<{
    patients: {
        data: Array<any>;
        links: Array<any>;
    };
    filters: {
        search?: string;
    };
}>();

const { isArabic } = useClinicLocale();
const search = ref(props.filters.search || '');

let searchTimer: ReturnType<typeof setTimeout> | undefined;

watch(search, (value) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            '/dashboard/clinic/patients',
            { search: value || undefined },
            { preserveState: true, replace: true },
        );
    }, 300);
});
</script>

<template>
    <Head title="قائمة المرضى" />

    <div class="admin-screen active">
        <ClinicHeader
            :form-name="{
                ar: 'قائمة المرضى',
                en: 'Patients List',
            }"
            show-back
            back-href="/dashboard/clinic/home"
            :breadcrumbs="[
                { label: { ar: 'قائمة المرضى', en: 'Patients List' } }
            ]"
        />

        <div class="admin-content">
            <div class="admin-table-card">
                <div class="admin-filters">
                    <input
                        v-model="search"
                        type="text"
                        class="admin-search"
                        :placeholder="'ابحث بالاسم، رقم الملف، الجوال...'"
                    />
                </div>

                <div v-if="patients.data.length === 0" class="arch-empty">
                    <h4>
                        <LocalizedText
                            :value="{
                                ar: 'لا يوجد مرضى مطابقين للبحث',
                                en: 'No patients found matching your search',
                            }"
                        />
                    </h4>
                </div>

                <div v-else class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th><LocalizedText :value="{ ar: 'الاسم', en: 'Name' }" /></th>
                                <th><LocalizedText :value="{ ar: 'رقم الملف', en: 'File #' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الهوية', en: 'ID' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الجوال', en: 'Phone' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الفرع', en: 'Branch' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الإجراءات', en: 'Actions' }" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="patient in patients.data" :key="patient.id">
                                <td class="font-bold">{{ patient.name }}</td>
                                <td><span class="badge">{{ patient.file_number }}</span></td>
                                <td>{{ patient.id_number }}</td>
                                <td>{{ patient.phone }}</td>
                                <td>{{ patient.branch?.name }}</td>
                                <td>
                                    <Link :href="`/dashboard/clinic/patients/${patient.id}`" class="btn btn-sm btn-outline">
                                        <LocalizedText :value="{ ar: 'عرض', en: 'View' }" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Simple Pagination -->
                <div class="pagination-footer" v-if="patients.links.length > 3">
                    <div class="pager-stats">
                        <LocalizedText :value="{ ar: 'عرض', en: 'Showing' }" />
                        {{ patients.data.length }}
                        <LocalizedText :value="{ ar: 'من', en: 'of' }" />
                        {{ patients.total }}
                    </div>
                    <div class="pager-buttons">
                        <template v-for="(link, k) in patients.links" :key="k">
                            <div v-if="link.url === null" 
                                 class="page-link disabled" 
                                 v-html="link.label.includes('Previous') ? (isArabic ? 'السابق' : 'Prev') : (link.label.includes('Next') ? (isArabic ? 'التالي' : 'Next') : link.label)" />
                            
                            <Link v-else 
                                  class="page-link" 
                                  :class="{ 'active': link.active }" 
                                  :href="link.url" 
                                  v-html="link.label.includes('Previous') ? (isArabic ? 'السابق' : 'Prev') : (link.label.includes('Next') ? (isArabic ? 'التالي' : 'Next') : link.label)" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-container {
    overflow-x: auto;
    margin-top: 1rem;
}
.admin-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.admin-table th {
    background: var(--bg-soft);
    padding: 12px 16px;
    text-align: right;
    font-weight: 700;
    color: var(--primary);
    border-bottom: 2px solid var(--line);
}
:deep(body[data-lang="en"]) .admin-table th {
    text-align: left;
}
.admin-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--line);
    color: var(--ink-soft);
}
.admin-table tr:hover td {
    background: rgba(13, 148, 136, 0.05);
}
.badge {
    background: var(--accent-soft);
    color: var(--accent);
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
}
.pagination-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 24px;
    padding: 16px;
    border-top: 1px solid var(--line);
    background: #fdfdfd;
    border-radius: 0 0 14px 14px;
}
.pager-stats {
    font-size: 13px;
    color: var(--ink-mute);
    font-weight: 500;
}
.pager-buttons {
    display: flex;
    gap: 6px;
}
.page-link {
    padding: 6px 14px;
    border: 1px solid var(--line);
    border-radius: 8px;
    text-decoration: none;
    color: var(--primary);
    background: white;
    font-size: 13px;
    font-weight: 600;
    transition: all .2s;
}
.page-link:hover:not(.disabled) {
    border-color: var(--primary);
    background: var(--bg-soft);
}
.page-link.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}
.page-link.disabled {
    color: var(--ink-mute);
    cursor: not-allowed;
    background: #f9f9f9;
    opacity: 0.6;
}
.font-bold {
    font-weight: 700;
}
.btn-sm {
    padding: 4px 10px;
    font-size: 12px;
}
.btn-outline {
    border: 1px solid var(--line);
    background: white;
    color: var(--primary);
}
.btn-outline:hover {
    border-color: var(--primary);
    background: var(--bg-soft);
}
</style>
