<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { wayfinderFormRoute } from '@/lib/utils';
import { store, update, destroy } from '@/routes/admin/form-categories';
import type { FormCategory, Department } from '@/types/admin';

const props = defineProps<{
    categories: FormCategory[];
    departments: Department[];
}>();

const { isArabic } = useClinicLocale();
const editing = ref<FormCategory | null>(null);
const filterDeptId = ref<number | ''>('');
const search = ref('');

const form = useForm({
    department_id: '' as number | '',
    code: '',
    name_ar: '',
    name_en: '',
    sort_order: 0,
    is_active: true,
});

const filtered = computed(() => {
    return props.categories.filter((c) => {
        const matchesDept = filterDeptId.value === '' || c.department_id === filterDeptId.value;
        const q = search.value.toLowerCase();
        const matchesSearch =
            !q ||
            c.code.toLowerCase().includes(q) ||
            c.name_ar.includes(q) ||
            c.name_en.toLowerCase().includes(q);
        return matchesDept && matchesSearch;
    });
});

function select(cat: FormCategory | null): void {
    editing.value = cat;
    if (cat) {
        form.department_id = cat.department_id;
        form.code          = cat.code;
        form.name_ar       = cat.name_ar;
        form.name_en       = cat.name_en;
        form.sort_order    = cat.sort_order;
        form.is_active     = cat.is_active;
    } else {
        form.reset();
        form.is_active  = true;
        form.sort_order = 0;
    }
}

function submit(): void {
    const route = editing.value
        ? update.form(editing.value.id)
        : store.form();
    const { method, url } = wayfinderFormRoute(route);
    form.submit(method, url, {
        onSuccess: () => select(null),
    });
}

function deptName(dept?: Department): string {
    if (!dept) return '—';
    return isArabic.value ? dept.name_ar : dept.name_en;
}
</script>

<template>
    <Head :title="isArabic ? 'تصنيفات النماذج' : 'Form Categories'" />

    <AdminPageLayout
        :form-name="{ ar: 'إدارة تصنيفات النماذج', en: 'Form Category Management' }"
        show-back
        back-href="/dashboard"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText :value="{ ar: 'تصنيفات النماذج', en: 'Form Categories' }" />
                </h2>
                <p>
                    {{
                        isArabic
                            ? 'إدارة تصنيفات النماذج الطبية وتخصيصها للأقسام'
                            : 'Manage medical form categories and assign them to departments'
                    }}
                </p>
            </div>
            <button type="button" class="btn btn-primary" @click="select(null)">
                <LocalizedText :value="{ ar: 'تصنيف جديد', en: 'New Category' }" />
            </button>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <input
                v-model="search"
                type="search"
                class="filter-input"
                :placeholder="isArabic ? 'بحث...' : 'Search...'"
            />
            <select v-model="filterDeptId" class="filter-select">
                <option value="">{{ isArabic ? 'كل الأقسام' : 'All Departments' }}</option>
                <option v-for="d in departments" :key="d.id" :value="d.id">
                    {{ isArabic ? d.name_ar : d.name_en }}
                </option>
            </select>
        </div>

        <div class="admin-split-layout">
            <!-- Table -->
            <div class="admin-table-card admin-split-main">
                <h3 class="card-section-title">
                    <LocalizedText :value="{ ar: 'قائمة التصنيفات', en: 'Categories List' }" />
                    <span class="count-badge">{{ filtered.length }}</span>
                </h3>

                <div style="overflow-x: auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>{{ isArabic ? 'الرمز' : 'Code' }}</th>
                                <th>{{ isArabic ? 'الاسم' : 'Name' }}</th>
                                <th>{{ isArabic ? 'القسم' : 'Department' }}</th>
                                <th>{{ isArabic ? 'الترتيب' : 'Order' }}</th>
                                <th>{{ isArabic ? 'الحالة' : 'Status' }}</th>
                                <th>{{ isArabic ? 'إجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="filtered.length === 0">
                                <td colspan="6" style="text-align: center; color: var(--ink-mute); padding: 32px">
                                    {{ isArabic ? 'لا توجد تصنيفات' : 'No categories found' }}
                                </td>
                            </tr>
                            <tr v-for="cat in filtered" :key="cat.id">
                                <td>
                                    <code class="code-pill">{{ cat.code }}</code>
                                </td>
                                <td>
                                    <strong>{{ cat.name_en }}</strong>
                                    <div style="font-size: 12px; color: var(--ink-mute)">{{ cat.name_ar }}</div>
                                </td>
                                <td>
                                    <span class="dept-tag">{{ deptName(cat.department) }}</span>
                                </td>
                                <td style="text-align: center">{{ cat.sort_order }}</td>
                                <td>
                                    <span v-if="cat.is_active" class="status-active">
                                        {{ isArabic ? 'نشط' : 'Active' }}
                                    </span>
                                    <span v-else class="status-inactive">
                                        {{ isArabic ? 'معطّل' : 'Inactive' }}
                                    </span>
                                </td>
                                <td style="white-space: nowrap; text-align: center">
                                    <button
                                        type="button"
                                        class="admin-icon-btn admin-icon-edit"
                                        @click="select(cat)"
                                    >
                                        {{ isArabic ? 'تعديل' : 'Edit' }}
                                    </button>
                                    <button
                                        type="button"
                                        class="admin-icon-btn admin-icon-delete"
                                        @click="() => {
                                            if (confirm(isArabic ? 'هل أنت متأكد؟' : 'Are you sure?')) {
                                                router.delete(destroy(cat.id).url);
                                            }
                                        }"
                                    >
                                        {{ isArabic ? 'حذف' : 'Delete' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Panel -->
            <div class="admin-table-card admin-split-side">
                <h3 class="card-section-title">
                    {{
                        editing
                            ? isArabic ? 'تعديل التصنيف' : 'Edit Category'
                            : isArabic ? 'إضافة تصنيف' : 'Add Category'
                    }}
                </h3>

                <form @submit.prevent="submit" class="admin-form-stack">
                    <div class="field">
                        <label for="cat-dept">{{ isArabic ? 'القسم' : 'Department' }}</label>
                        <select id="cat-dept" v-model="form.department_id" required>
                            <option value="">{{ isArabic ? 'اختر قسماً...' : 'Select department...' }}</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">
                                {{ isArabic ? d.name_ar : d.name_en }}
                            </option>
                        </select>
                        <p v-if="form.errors.department_id" class="field-error">{{ form.errors.department_id }}</p>
                    </div>

                    <div class="field">
                        <label for="cat-code">{{ isArabic ? 'الرمز (Code)' : 'Code' }}</label>
                        <input
                            id="cat-code"
                            v-model="form.code"
                            :disabled="!!editing"
                            required
                            placeholder="derm_clinic"
                            dir="ltr"
                        />
                        <p v-if="form.errors.code" class="field-error">{{ form.errors.code }}</p>
                        <p style="font-size: 11px; color: var(--ink-mute); margin-top: 4px">
                            {{ isArabic ? 'حروف إنجليزية وشرطات سفلية فقط — لا يمكن تغييره بعد الإنشاء' : 'Lowercase letters, numbers, underscores only — cannot change after creation' }}
                        </p>
                    </div>

                    <div class="field">
                        <label for="cat-name-en">{{ isArabic ? 'الاسم (EN)' : 'Name (EN)' }}</label>
                        <input id="cat-name-en" v-model="form.name_en" required dir="ltr" />
                        <p v-if="form.errors.name_en" class="field-error">{{ form.errors.name_en }}</p>
                    </div>

                    <div class="field">
                        <label for="cat-name-ar">{{ isArabic ? 'الاسم (AR)' : 'Name (AR)' }}</label>
                        <input id="cat-name-ar" v-model="form.name_ar" required dir="rtl" />
                        <p v-if="form.errors.name_ar" class="field-error">{{ form.errors.name_ar }}</p>
                    </div>

                    <div class="field">
                        <label for="cat-sort">{{ isArabic ? 'ترتيب العرض' : 'Sort Order' }}</label>
                        <input id="cat-sort" v-model.number="form.sort_order" type="number" min="0" />
                    </div>

                    <div class="field">
                        <label class="toggle-label">
                            <span>{{ isArabic ? 'نشط' : 'Active' }}</span>
                            <div class="toggle-wrap">
                                <input id="cat-active" v-model="form.is_active" type="checkbox" class="toggle-input" />
                                <label for="cat-active" class="toggle-track"></label>
                            </div>
                        </label>
                    </div>

                    <div class="admin-form-actions">
                        <button
                            v-if="editing"
                            type="button"
                            class="btn btn-outline"
                            @click="select(null)"
                        >
                            {{ isArabic ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ editing ? (isArabic ? 'تحديث' : 'Update') : (isArabic ? 'إنشاء' : 'Create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminPageLayout>
</template>

<style scoped>
.admin-page-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.admin-page-head h2 {
    font-size: 22px;
    font-weight: 800;
    color: var(--primary);
    margin: 0 0 4px;
}
.admin-page-head p {
    color: var(--ink-mute);
    font-size: 13px;
    margin: 0;
}
.filters-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.filter-input,
.filter-select {
    padding: 9px 14px;
    border: 1.5px solid var(--line);
    border-radius: 10px;
    font-family: inherit;
    font-size: 13px;
    background: var(--bg-card);
    color: var(--ink);
    transition: border-color 0.2s;
}
.filter-input { flex: 1; min-width: 200px; }
.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: var(--primary);
}
.admin-split-layout {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    align-items: flex-start;
}
.admin-split-main { flex: 2; min-width: 300px; }
.admin-split-side {
    flex: 1;
    min-width: 280px;
    position: sticky;
    top: 120px;
}
.card-section-title {
    font-size: 17px;
    color: var(--primary);
    margin: 0 0 16px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 10px;
}
.count-badge {
    background: var(--primary);
    color: white;
    font-size: 12px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
}
.code-pill {
    background: var(--bg-soft);
    color: var(--primary);
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    border: 1px solid var(--line);
}
.dept-tag {
    background: rgba(30, 58, 95, 0.08);
    color: var(--primary);
    padding: 3px 10px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
}
.status-active {
    color: var(--green);
    font-weight: 700;
    font-size: 13px;
}
.status-inactive {
    color: var(--red);
    font-weight: 700;
    font-size: 13px;
}
.admin-form-stack {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.admin-form-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 8px;
}
.field-error {
    color: var(--red);
    font-size: 12px;
    font-weight: 700;
    margin-top: 4px;
}
.toggle-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: var(--ink-soft);
}
.toggle-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.toggle-input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.toggle-track {
    display: block;
    width: 44px;
    height: 24px;
    background: var(--line);
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.2s;
    position: relative;
}
.toggle-track::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}
.toggle-input:checked + .toggle-track {
    background: var(--green);
}
.toggle-input:checked + .toggle-track::after {
    transform: translateX(20px);
}
</style>
