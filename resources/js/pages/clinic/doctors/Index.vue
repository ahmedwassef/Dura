<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { Plus, Edit2, Trash2, X } from 'lucide-vue-next';

const props = defineProps<{
    doctors: {
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
const editingDoctor = ref<any>(null);

const form = useForm({
    name: '',
    specialty: '',
    phone: '',
    license_number: '',
    is_active: true
});

function openAddModal() {
    editingDoctor.value = null;
    form.reset();
    showModal.value = true;
}

function openEditModal(doctor: any) {
    editingDoctor.value = doctor;
    form.name = doctor.name;
    form.specialty = doctor.specialty || '';
    form.phone = doctor.phone || '';
    form.license_number = doctor.license_number || '';
    form.is_active = !!doctor.is_active;
    showModal.value = true;
}

function submit() {
    if (editingDoctor.value) {
        form.put(`/dashboard/clinic/doctors/${editingDoctor.value.id}`, {
            onSuccess: () => closeModal()
        });
    } else {
        form.post('/dashboard/clinic/doctors', {
            onSuccess: () => closeModal()
        });
    }
}

function deleteDoctor(doctor: any) {
    if (confirm(isArabic.value ? 'هل أنت متأكد من حذف هذا الطبيب؟' : 'Are you sure you want to delete this doctor?')) {
        router.delete(`/dashboard/clinic/doctors/${doctor.id}`);
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
            '/dashboard/clinic/doctors',
            { search: value || undefined },
            { preserveState: true, replace: true },
        );
    }, 300);
});
</script>

<template>
    <Head title="إدارة الأطباء" />

    <div class="admin-screen active">
        <ClinicHeader
            :form-name="{
                ar: 'إدارة الأطباء',
                en: 'Manage Doctors',
            }"
            show-back
            back-href="/dashboard/clinic/home"
            :breadcrumbs="[
                { label: { ar: 'إدارة الأطباء', en: 'Manage Doctors' } }
            ]"
        />

        <div class="admin-content">
            <div class="admin-table-card">
                <div class="admin-filters">
                    <input
                        v-model="search"
                        type="text"
                        class="admin-search"
                        :placeholder="isArabic ? 'ابحث بالاسم أو التخصص...' : 'Search by name or specialty...'"
                    />
                    <button class="btn btn-primary" @click="openAddModal">
                        <Plus class="size-4" />
                        <LocalizedText :value="{ ar: 'إضافة طبيب', en: 'Add Doctor' }" />
                    </button>
                </div>

                <div v-if="doctors.data.length === 0" class="arch-empty">
                    <h4>
                        <LocalizedText
                            :value="{
                                ar: 'لا يوجد أطباء مطابقين للبحث',
                                en: 'No doctors found matching your search',
                            }"
                        />
                    </h4>
                </div>

                <div v-else class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th><LocalizedText :value="{ ar: 'الاسم', en: 'Name' }" /></th>
                                <th><LocalizedText :value="{ ar: 'التخصص', en: 'Specialty' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الهاتف', en: 'Phone' }" /></th>
                                <th><LocalizedText :value="{ ar: 'رقم الترخيص', en: 'License' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الحالة', en: 'Status' }" /></th>
                                <th><LocalizedText :value="{ ar: 'الإجراءات', en: 'Actions' }" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="doctor in doctors.data" :key="doctor.id">
                                <td class="font-bold">{{ doctor.name }}</td>
                                <td>{{ doctor.specialty || '-' }}</td>
                                <td>{{ doctor.phone || '-' }}</td>
                                <td><span class="license-badge">{{ doctor.license_number || '-' }}</span></td>
                                <td>
                                    <span class="status-dot" :class="{ active: doctor.is_active }"></span>
                                    {{ doctor.is_active ? (isArabic ? 'نشط' : 'Active') : (isArabic ? 'غير نشط' : 'Inactive') }}
                                </td>
                                <td class="actions-cell">
                                    <button class="action-btn edit" @click="openEditModal(doctor)">
                                        <Edit2 class="size-4" />
                                    </button>
                                    <button class="action-btn delete" @click="deleteDoctor(doctor)">
                                        <Trash2 class="size-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pager -->
                <div class="pagination-footer" v-if="doctors.links.length > 3">
                    <div class="pager-stats">
                        <LocalizedText :value="{ ar: 'عرض', en: 'Showing' }" />
                        {{ doctors.data.length }}
                        <LocalizedText :value="{ ar: 'من', en: 'of' }" />
                        {{ doctors.total }}
                    </div>
                    <div class="pager-buttons">
                        <template v-for="(link, k) in doctors.links" :key="k">
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
                    <LocalizedText v-if="editingDoctor" :value="{ ar: 'تعديل بيانات الطبيب', en: 'Edit Doctor Details' }" />
                    <LocalizedText v-else :value="{ ar: 'إضافة طبيب جديد', en: 'Add New Doctor' }" />
                </h3>
                <button class="close-btn" @click="closeModal"><X /></button>
            </div>
            
            <form @submit.prevent="submit" class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'الاسم الكامل', en: 'Full Name' }" /></label>
                        <input v-model="form.name" type="text" required class="admin-input" />
                    </div>
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'التخصص', en: 'Specialty' }" /></label>
                        <input v-model="form.specialty" type="text" class="admin-input" />
                    </div>
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'رقم الهاتف', en: 'Phone Number' }" /></label>
                        <input v-model="form.phone" type="text" class="admin-input" />
                    </div>
                    <div class="field">
                        <label><LocalizedText :value="{ ar: 'رقم الترخيص', en: 'License Number' }" /></label>
                        <input v-model="form.license_number" type="text" class="admin-input" />
                    </div>
                    <div class="field checkbox-field">
                        <label class="check-item">
                            <input type="checkbox" v-model="form.is_active" />
                            <span><LocalizedText :value="{ ar: 'حساب نشط', en: 'Active Account' }" /></span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" @click="closeModal">
                        <LocalizedText :value="{ ar: 'إلغاء', en: 'Cancel' }" />
                    </button>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <LocalizedText v-if="editingDoctor" :value="{ ar: 'حفظ التغييرات', en: 'Save Changes' }" />
                        <LocalizedText v-else :value="{ ar: 'إضافة', en: 'Add' }" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.admin-filters {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
}
.admin-search {
    flex: 1;
    max-width: 400px;
}
.table-container {
    overflow-x: auto;
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
    font-size: 13px;
}
:deep(body[data-lang="en"]) .admin-table th { text-align: left; }

.admin-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--line);
    color: var(--ink-soft);
    font-size: 14px;
}
.admin-table tr:hover td { background: rgba(13, 148, 136, 0.05); }

.license-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 2px 8px;
    border-radius: 6px;
    font-family: monospace;
    font-size: 12px;
}

.status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #cbd5e1;
    margin-inline-end: 6px;
}
.status-dot.active { background: #10b981; }

.actions-cell {
    display: flex;
    gap: 8px;
}
.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid var(--line);
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .2s;
}
.action-btn.edit:hover { color: var(--primary); border-color: var(--primary); }
.action-btn.delete:hover { color: var(--red); border-color: var(--red); }

/* Pager Styles */
.pagination-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 24px;
    padding: 16px;
    border-top: 1px solid var(--line);
}
.pager-stats { font-size: 13px; color: var(--ink-mute); }
.pager-buttons { display: flex; gap: 6px; }
.page-link {
    padding: 6px 14px;
    border: 1px solid var(--line);
    border-radius: 8px;
    text-decoration: none;
    color: var(--primary);
    background: white;
    font-size: 13px;
    font-weight: 600;
}
.page-link.active { background: var(--primary); color: white; border-color: var(--primary); }
.page-link.disabled { color: var(--ink-mute); opacity: 0.5; cursor: not-allowed; }

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    backdrop-filter: blur(4px);
}
.modal-card {
    background: white;
    width: 100%;
    max-width: 500px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    overflow: hidden;
}
.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--line);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header h3 { font-size: 18px; font-weight: 800; color: var(--primary); }
.close-btn { background: none; border: none; cursor: pointer; color: var(--ink-mute); }

.modal-body { padding: 24px; }
.form-grid { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 700; color: var(--ink-soft); }

.check-item {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}
.check-item input { width: 18px; height: 18px; }

.modal-footer {
    margin-top: 24px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
    border: 1px solid transparent;
}
.btn-primary { background: var(--primary); color: white; }
.btn-primary:hover { background: var(--primary-dark); }
.btn-outline { border-color: var(--line); background: white; color: var(--ink-soft); }
.btn-outline:hover { border-color: var(--primary); color: var(--primary); }
</style>
