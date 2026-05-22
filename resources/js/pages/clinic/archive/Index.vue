<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ArchiveCard from '@/clinic/components/archive/ArchiveCard.vue';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';

const props = defineProps<{
    archive: Array<Record<string, unknown>>;
    filters: {
        branches: Array<{ id: number; name_ar: string; name_en: string }>;
        forms: Array<{ id: number; name_ar: string; name_en: string }>;
    };
}>();

const search = ref('');
const branchId = ref('');
const formId = ref('');

let searchTimer: ReturnType<typeof setTimeout> | undefined;

const fetchArchive = () => {
    router.get(
        '/dashboard/clinic/archive',
        { 
            search: search.value || undefined,
            branch_id: branchId.value || undefined,
            form_id: formId.value || undefined
        },
        { preserveState: true, replace: true },
    );
};

watch(search, (value) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(fetchArchive, 300);
});

watch([branchId, formId], fetchArchive);
</script>

<template>
    <Head title="الأرشيف" />

    <div class="admin-screen active">
        <ClinicHeader
            :form-name="{
                ar: 'الأرشيف والاستقبال',
                en: 'Archive & Reception',
            }"
            show-back
            back-href="/dashboard/clinic/home"
            :breadcrumbs="[
                { label: { ar: 'الأرشيف', en: 'Archive' } }
            ]"
        />

        <div class="admin-content">
            <div class="admin-stats" />

            <div class="admin-table-card">
                <div class="admin-filters archive-filters-bar">
                    <input
                        v-model="search"
                        type="text"
                        class="admin-search"
                        :placeholder="'ابحث بالاسم أو رقم الملف...'"
                    />
                    
                    <select v-model="branchId" class="filter-select">
                        <option value="">كل الفروع / All Branches</option>
                        <option v-for="b in filters.branches" :key="b.id" :value="b.id">
                            {{ b.name_ar }} / {{ b.name_en }}
                        </option>
                    </select>

                    <select v-model="formId" class="filter-select">
                        <option value="">كل النماذج / All Forms</option>
                        <option v-for="f in filters.forms" :key="f.id" :value="f.id">
                            {{ f.name_ar }} / {{ f.name_en }}
                        </option>
                    </select>
                </div>

                <div v-if="archive.length === 0" class="arch-empty">
                    <h4>
                        <LocalizedText
                            :value="{
                                ar: 'لا توجد ملفات في الأرشيف',
                                en: 'No files in archive yet',
                            }"
                        />
                    </h4>
                    <p>
                        <LocalizedText
                            :value="{
                                ar: 'الملفات الموقّعة ستظهر هنا تلقائياً عند الحفظ',
                                en: 'Signed forms will appear here automatically on save',
                            }"
                        />
                    </p>
                    <Link href="/dashboard/clinic/home" class="btn btn-primary" style="margin-top: 16px">
                        <LocalizedText :value="{ ar: 'العودة للرئيسية', en: 'Back to home' }" />
                    </Link>
                </div>

                <div v-else class="archive-grid">
                    <ArchiveCard
                        v-for="item in archive"
                        :key="String(item.id)"
                        :item="item as any"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.archive-filters-bar {
    display: flex;
    gap: 12px;
    align-items: center;
}
.filter-select {
    padding: 10px 16px;
    border: 1px solid var(--line);
    border-radius: 12px;
    font-size: 14px;
    background: white;
    color: var(--ink);
    min-width: 200px;
}
@media (max-width: 768px) {
    .archive-filters-bar {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
