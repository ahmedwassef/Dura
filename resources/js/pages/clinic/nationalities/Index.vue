<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { Plus, Edit2, Trash2, X, Flag } from 'lucide-vue-next';

const props = defineProps<{
    nationalities: {
        data: Array<any>;
        links: Array<any>;
        total: number;
    };
    filters: {
        search?: string;
    };
}>();

const { isArabic } = useClinicLocale();
const search = ref(props.filters.search || '');
const showModal = ref(false);
const editingNationality = ref<any>(null);

const form = useForm({
    name_ar: '',
    name_en: '',
    code: ''
});

function openAddModal() {
    editingNationality.value = null;
    form.reset();
    showModal.value = true;
}

function openEditModal(nat: any) {
    editingNationality.value = nat;
    form.name_ar = nat.name_ar;
    form.name_en = nat.name_en;
    form.code = nat.code || '';
    showModal.value = true;
}

function submit() {
    if (editingNationality.value) {
        form.put(`/dashboard/clinic/nationalities/${editingNationality.value.id}`, {
            onSuccess: () => closeModal()
        });
    } else {
        form.post('/dashboard/clinic/nationalities', {
            onSuccess: () => closeModal()
        });
    }
}

function deleteNationality(nat: any) {
    if (confirm(isArabic.value ? 'هل أنت متأكد من حذف هذه الجنسية؟' : 'Are you sure you want to delete this nationality?')) {
        router.delete(`/dashboard/clinic/nationalities/${nat.id}`);
    }
}

function closeModal() {
    showModal.value = false;
    form.reset();
}

let searchTimer: ReturnType<typeof setTimeout> | undefined;
watch(search, (value) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            '/dashboard/clinic/nationalities',
            { search: value || undefined },
            { preserveState: true, replace: true },
        );
    }, 300);
});
</script>

<template>
    <Head title="إدارة الجنسيات" />

    <div class="admin-screen active">
        <ClinicHeader
            :form-name="{
                ar: 'إدارة الجنسيات',
                en: 'Manage Nationalities',
            }"
            show-back
            back-href="/dashboard/clinic/home"
            :breadcrumbs="[
                { label: { ar: 'إدارة الجنسيات', en: 'Manage Nationalities' } }
            ]"
        />

        <div class="admin-content">
            <div class="admin-table-card">
                <div class="admin-filters">
                    <input
                        v-model="search"
                        type="text"
                        class="admin-search"
                        :placeholder="isArabic ? 'ابحث عن جنسية...' : 'Search for nationality...'"
                    />
                    <button class="btn btn-primary" @click="openAddModal">
                        <Plus class="size-4" />
                        <LocalizedText :value="{ ar: 'إضافة جنسية', en: 'Add Nationality' }" />
                    </button>
                </div>

                <div v-if="nationalities.data.length === 0" class="arch-empty">
                    <h4><LocalizedText :value="{ ar: 'لا توجد نتائج', en: 'No results found' }" /></h4>
                </div>

                <div v-else class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th><LocalizedText :value="{ ar: 'الرمز', en: 'Code' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الاسم (عربي)', en: 'Name (Arabic)' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الاسم (إنجليزي)', en: 'Name (English)' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الإجراءات', en: 'Actions' }" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="nat in nationalities.data" :key="nat.id">
                                <td><span class="code-badge">{{ nat.code || '-' }}</span></td>
                                <td class="font-bold">{{ nat.name_ar }}</td>
                                <td>{{ nat.name_en }}</td>
                                <td class="actions-cell">
                                    <button class="action-btn edit" @click="openEditModal(nat)">
                                        <Edit2 class="size-4" />
                                    </button>
                                    <button class="action-btn delete" @click="deleteNationality(nat)">
                                        <Trash2 class="size-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pager -->
                <div class="pagination-footer" v-if="nationalities.links.length > 3">
                    <div class="pager-stats">
                        <LocalizedText :value="{ ar: 'عرض', en: 'Showing' }" />
                        {{ nationalities.data.length }}
                        <LocalizedText :value="{ ar: 'من', en: 'of' }" />
                        {{ nationalities.total }}
                    </div>
                    <div class="pager-buttons">
                        <template v-for="(link, k) in nationalities.links" :key="k">
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

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" v-if="showModal" @click.self="closeModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3>
                    <LocalizedText v-if="editingNationality" :value="{ ar: 'تعديل الجنسية', en: 'Edit Nationality' }" />
                    <LocalizedText v-else :value="{ ar: 'إضافة جنسية جديدة', en: 'Add New Nationality' }" />
                </h3>
                <button class="close-btn" @click="closeModal"><X /></button>
            </div>
            
            <form @submit.prevent="submit" class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'الاسم بالعربي', en: 'Arabic Name' }" /></label>
                        <input v-model="form.name_ar" type="text" required class="admin-input" />
                    </div>
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'الاسم بالإنجليزي', en: 'English Name' }" /></label>
                        <input v-model="form.name_en" type="text" required class="admin-input" />
                    </div>
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'رمز الجنسية (اختياري)', en: 'Nationality Code (Optional)' }" /></label>
                        <input v-model="form.code" type="text" class="admin-input" placeholder="e.g. SA, EG..." />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" @click="closeModal">
                        <LocalizedText :value="{ ar: 'إلغاء', en: 'Cancel' }" />
                    </button>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <LocalizedText v-if="editingNationality" :value="{ ar: 'حفظ', en: 'Save' }" />
                        <LocalizedText v-else :value="{ ar: 'إضافة', en: 'Add' }" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Reusing styles from Index pages */
.admin-filters { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
.admin-search { flex: 1; max-width: 400px; }
.table-container { overflow-x: auto; }
.admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.admin-table th { background: var(--bg-soft); padding: 12px 16px; text-align: right; font-weight: 700; color: var(--primary); border-bottom: 2px solid var(--line); }
:deep(body[data-lang="en"]) .admin-table th { text-align: left; }
.admin-table td { padding: 14px 16px; border-bottom: 1px solid var(--line); color: var(--ink-soft); }

.code-badge { background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 12px; }

.actions-cell { display: flex; gap: 8px; }
.action-btn {
    width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line);
    background: white; display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .2s;
}
.action-btn.edit:hover { color: var(--primary); border-color: var(--primary); }
.action-btn.delete:hover { color: var(--red); border-color: var(--red); }

/* Pager Styles */
.pagination-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding: 16px; border-top: 1px solid var(--line); }
.pager-stats { font-size: 13px; color: var(--ink-mute); }
.pager-buttons { display: flex; gap: 6px; }
.page-link {
    padding: 6px 14px; border: 1px solid var(--line); border-radius: 8px;
    text-decoration: none; color: var(--primary); background: white; font-size: 13px; font-weight: 600;
}
.page-link.active { background: var(--primary); color: white; border-color: var(--primary); }
.page-link.disabled { color: var(--ink-mute); opacity: 0.5; cursor: not-allowed; }

/* Modal Styles */
.modal-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4);
    display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px);
}
.modal-card { background: white; width: 100%; max-width: 500px; border-radius: 20px; overflow: hidden; }
.modal-header { padding: 20px 24px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { font-size: 18px; font-weight: 800; color: var(--primary); }
.close-btn { background: none; border: none; cursor: pointer; color: var(--ink-mute); }
.modal-body { padding: 24px; }
.form-grid { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 700; color: var(--ink-soft); }
.modal-footer { margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px; }
.btn {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
    border-radius: 10px; font-weight: 700; cursor: pointer; border: 1px solid transparent;
}
.btn-primary { background: var(--primary); color: white; }
.btn-outline { border-color: var(--line); background: white; color: var(--ink-soft); }
</style>
