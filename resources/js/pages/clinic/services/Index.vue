<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { Plus, Edit2, Trash2, X, Package } from 'lucide-vue-next';

const props = defineProps<{
    services: {
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
const editingService = ref<any>(null);

const form = useForm({
    code: '',
    name_ar: '',
    name_en: '',
    price: 0,
    category_ar: '',
    category_en: '',
    is_active: true
});

function openAddModal() {
    editingService.value = null;
    form.reset();
    showModal.value = true;
}

function openEditModal(service: any) {
    editingService.value = service;
    form.code = service.code;
    form.name_ar = service.name_ar;
    form.name_en = service.name_en || '';
    form.price = Number(service.price);
    form.category_ar = service.category_ar || '';
    form.category_en = service.category_en || '';
    form.is_active = !!service.is_active;
    showModal.value = true;
}

function submit() {
    if (editingService.value) {
        form.put(`/dashboard/clinic/services/${editingService.value.id}`, {
            onSuccess: () => closeModal()
        });
    } else {
        form.post('/dashboard/clinic/services', {
            onSuccess: () => closeModal()
        });
    }
}

function deleteService(service: any) {
    if (confirm(isArabic.value ? 'هل أنت متأكد من حذف هذه الخدمة؟' : 'Are you sure you want to delete this service?')) {
        router.delete(`/dashboard/clinic/services/${service.id}`);
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
            '/dashboard/clinic/services',
            { search: value || undefined },
            { preserveState: true, replace: true },
        );
    }, 300);
});
</script>

<template>
    <Head title="إدارة الخدمات" />

    <div class="admin-screen active">
        <ClinicHeader
            :form-name="{
                ar: 'إدارة الخدمات الطبية',
                en: 'Manage Medical Services',
            }"
            show-back
            back-href="/dashboard/clinic/home"
            :breadcrumbs="[
                { label: { ar: 'كتالوج الخدمات', en: 'Service Catalog' } }
            ]"
        />

        <div class="admin-content">
            <div class="admin-table-card">
                <div class="admin-filters">
                    <input
                        v-model="search"
                        type="text"
                        class="admin-search"
                        :placeholder="isArabic ? 'ابحث بالاسم أو الرمز...' : 'Search by name or code...'"
                    />
                    <button class="btn btn-primary" @click="openAddModal">
                        <Plus class="size-4" />
                        <LocalizedText :value="{ ar: 'إضافة خدمة', en: 'Add Service' }" />
                    </button>
                </div>

                <div v-if="services.data.length === 0" class="arch-empty">
                    <h4><LocalizedText :value="{ ar: 'لا توجد خدمات مطابقة', en: 'No services found' }" /></h4>
                </div>

                <div v-else class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th><LocalizedText :value="{ ar: 'الرمز', en: 'Code' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الخدمة', en: 'Service Name' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الفئة', en: 'Category' }" /></th>
                                <th><LocalizedText :value="{ ar: 'السعر', en: 'Price' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الحالة', en: 'Status' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الإجراءات', en: 'Actions' }" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="service in services.data" :key="service.id">
                                <td><span class="code-badge">{{ service.code }}</span></td>
                                <td class="font-bold">
                                    <LocalizedText :value="{ ar: service.name_ar, en: service.name_en }" />
                                </td>
                                <td>
                                    <LocalizedText :value="{ ar: service.category_ar, en: service.category_en }" />
                                </td>
                                <td class="price-cell">{{ Number(service.price).toFixed(2) }} ﷼</td>
                                <td>
                                    <span class="status-dot" :class="{ active: service.is_active }"></span>
                                    {{ service.is_active ? (isArabic ? 'نشط' : 'Active') : (isArabic ? 'غير نشط' : 'Inactive') }}
                                </td>
                                <td class="actions-cell">
                                    <button class="action-btn edit" @click="openEditModal(service)">
                                        <Edit2 class="size-4" />
                                    </button>
                                    <button class="action-btn delete" @click="deleteService(service)">
                                        <Trash2 class="size-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pager -->
                <div class="pagination-footer" v-if="services.links.length > 3">
                    <div class="pager-stats">
                        <LocalizedText :value="{ ar: 'عرض', en: 'Showing' }" />
                        {{ services.data.length }}
                        <LocalizedText :value="{ ar: 'من', en: 'of' }" />
                        {{ services.total }}
                    </div>
                    <div class="pager-buttons">
                        <template v-for="(link, k) in services.links" :key="k">
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
                    <LocalizedText v-if="editingService" :value="{ ar: 'تعديل الخدمة', en: 'Edit Service' }" />
                    <LocalizedText v-else :value="{ ar: 'إضافة خدمة جديدة', en: 'Add New Service' }" />
                </h3>
                <button class="close-btn" @click="closeModal"><X /></button>
            </div>
            
            <form @submit.prevent="submit" class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'الرمز الكودي', en: 'Service Code' }" /></label>
                        <input v-model="form.code" type="text" required class="admin-input" />
                        <span class="error" v-if="form.errors.code">{{ form.errors.code }}</span>
                    </div>
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'الاسم (عربي)', en: 'Name (Arabic)' }" /></label>
                        <input v-model="form.name_ar" type="text" required class="admin-input" />
                    </div>
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'الاسم (إنجليزي)', en: 'Name (English)' }" /></label>
                        <input v-model="form.name_en" type="text" class="admin-input" />
                    </div>
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'السعر (﷼)', en: 'Price (SAR)' }" /></label>
                        <input v-model="form.price" type="number" step="0.01" required class="admin-input" />
                    </div>
                    <div class="field-grid">
                        <div class="field">
                            <label><LocalizedText :value="{ ar: 'الفئة (عربي)', en: 'Category (Arabic)' }" /></label>
                            <input v-model="form.category_ar" type="text" class="admin-input" />
                        </div>
                        <div class="field">
                            <label><LocalizedText :value="{ ar: 'الفئة (إنجليزي)', en: 'Category (English)' }" /></label>
                            <input v-model="form.category_en" type="text" class="admin-input" />
                        </div>
                    </div>
                    <div class="field checkbox-field">
                        <label class="check-item">
                            <input type="checkbox" v-model="form.is_active" />
                            <span><LocalizedText :value="{ ar: 'تفعيل الخدمة', en: 'Active Service' }" /></span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" @click="closeModal">
                        <LocalizedText :value="{ ar: 'إلغاء', en: 'Cancel' }" />
                    </button>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <LocalizedText v-if="editingService" :value="{ ar: 'حفظ', en: 'Save' }" />
                        <LocalizedText v-else :value="{ ar: 'إضافة', en: 'Add' }" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Reusing styles from Doctors Index */
.admin-filters { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
.admin-search { flex: 1; max-width: 400px; }
.table-container { overflow-x: auto; }
.admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.admin-table th {
    background: var(--bg-soft);
    padding: 12px 16px;
    text-align: right;
    font-weight: 700;
    color: var(--primary);
    border-bottom: 2px solid var(--line);
}
:deep(body[data-lang="en"]) .admin-table th { text-align: left; }
.admin-table td { padding: 14px 16px; border-bottom: 1px solid var(--line); color: var(--ink-soft); }

.code-badge { background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 12px; }
.price-cell { font-weight: 800; color: var(--accent); }

.status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; margin-inline-end: 6px; }
.status-dot.active { background: #10b981; }

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
.modal-card { background: white; width: 100%; max-width: 550px; border-radius: 20px; overflow: hidden; }
.modal-header { padding: 20px 24px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { font-size: 18px; font-weight: 800; color: var(--primary); }
.close-btn { background: none; border: none; cursor: pointer; color: var(--ink-mute); }
.modal-body { padding: 24px; }
.form-grid { display: flex; flex-direction: column; gap: 16px; }
.field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 700; color: var(--ink-soft); }
.check-item { display: flex; align-items: center; gap: 10px; cursor: pointer; }
.modal-footer { margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px; }
.btn {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
    border-radius: 10px; font-weight: 700; cursor: pointer; border: 1px solid transparent;
}
.btn-primary { background: var(--primary); color: white; }
.btn-outline { border-color: var(--line); background: white; color: var(--ink-soft); }
.error { color: var(--red); font-size: 12px; margin-top: 2px; }
</style>
