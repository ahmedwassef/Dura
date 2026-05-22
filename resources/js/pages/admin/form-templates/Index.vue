<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { wayfinderFormRoute } from '@/lib/utils';
import { store, update, destroy } from '@/routes/admin/form-templates';
import type { FormTemplate, FormCategory, Department } from '@/types/admin';

const props = defineProps<{
    templates: FormTemplate[];
    departments: Department[];
    categories: FormCategory[];
}>();

const { isArabic } = useClinicLocale();
const editing = ref<FormTemplate | null>(null);
const filterDeptId = ref<number | ''>('');
const search = ref('');

const form = useForm({
    department_id: '' as number | '',
    code: '',
    category_code: '' as string | '',
    name_ar: '',
    name_en: '',
    is_bilingual: false,
    is_ar_only: false,
    sort_order: 0,
    is_active: true,
});

// Filter categories by selected department in the form
const formCategories = computed(() =>
    props.categories.filter((c) => c.department_id === form.department_id && c.is_active),
);

// When department changes in form, reset category
watch(
    () => form.department_id,
    () => {
        form.category_code = '';
    },
);

const filtered = computed(() => {
    return props.templates.filter((t) => {
        const matchesDept = filterDeptId.value === '' || t.department_id === filterDeptId.value;
        const q = search.value.toLowerCase();
        const matchesSearch =
            !q ||
            t.code.toLowerCase().includes(q) ||
            t.name_ar.includes(q) ||
            t.name_en.toLowerCase().includes(q);
        return matchesDept && matchesSearch;
    });
});

function select(tmpl: FormTemplate | null): void {
    editing.value = tmpl;
    if (tmpl) {
        form.department_id = tmpl.department_id;
        form.code          = tmpl.code;
        form.category_code = tmpl.category_code ?? '';
        form.name_ar       = tmpl.name_ar;
        form.name_en       = tmpl.name_en;
        form.is_bilingual  = tmpl.is_bilingual;
        form.is_ar_only    = tmpl.is_ar_only;
        form.sort_order    = tmpl.sort_order;
        form.is_active     = tmpl.is_active;
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

function catName(cat?: FormCategory): string {
    if (!cat) return '—';
    return isArabic.value ? cat.name_ar : cat.name_en;
}

function langBadge(tmpl: FormTemplate): { label: string; color: string } {
    if (tmpl.is_bilingual) return { label: isArabic.value ? 'ثنائي' : 'Bilingual', color: 'var(--accent)' };
    if (tmpl.is_ar_only)   return { label: isArabic.value ? 'عربي فقط' : 'AR only',  color: '#6c4f9b' };
    return { label: isArabic.value ? 'عربي' : 'Arabic', color: 'var(--primary)' };
}
</script>

<template>
    <Head :title="isArabic ? 'نماذج النظام' : 'Form Templates'" />

    <AdminPageLayout
        :form-name="{ ar: 'إدارة نماذج النظام', en: 'Form Template Management' }"
        show-back
        back-href="/dashboard"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText :value="{ ar: 'نماذج النظام', en: 'Form Templates' }" />
                </h2>
                <p>
                    {{
                        isArabic
                            ? 'إضافة وتعديل وتنظيم النماذج الطبية حسب الأقسام والتصنيفات'
                            : 'Add, edit and organise medical forms by department and category'
                    }}
                </p>
            </div>
            <button type="button" class="btn btn-primary" @click="select(null)">
                <LocalizedText :value="{ ar: 'نموذج جديد', en: 'New Template' }" />
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
                    <LocalizedText :value="{ ar: 'قائمة النماذج', en: 'Templates List' }" />
                    <span class="count-badge">{{ filtered.length }}</span>
                </h3>

                <div style="overflow-x: auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>{{ isArabic ? 'الرمز' : 'Code' }}</th>
                                <th>{{ isArabic ? 'الاسم' : 'Name' }}</th>
                                <th>{{ isArabic ? 'القسم' : 'Department' }}</th>
                                <th>{{ isArabic ? 'التصنيف' : 'Category' }}</th>
                                <th>{{ isArabic ? 'اللغة' : 'Language' }}</th>
                                <th>{{ isArabic ? 'الحالة' : 'Status' }}</th>
                                <th>{{ isArabic ? 'إجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="filtered.length === 0">
                                <td colspan="7" style="text-align: center; color: var(--ink-mute); padding: 32px">
                                    {{ isArabic ? 'لا توجد نماذج' : 'No templates found' }}
                                </td>
                            </tr>
                            <tr v-for="tmpl in filtered" :key="tmpl.id">
                                <td>
                                    <code class="code-pill">{{ tmpl.code }}</code>
                                </td>
                                <td>
                                    <strong>{{ tmpl.name_en }}</strong>
                                    <div style="font-size: 12px; color: var(--ink-mute)">{{ tmpl.name_ar }}</div>
                                </td>
                                <td>
                                    <span class="dept-tag">{{ deptName(tmpl.department) }}</span>
                                </td>
                                <td>
                                    <span v-if="tmpl.category" class="cat-tag">
                                        {{ catName(tmpl.category) }}
                                    </span>
                                    <span v-else style="color: var(--ink-mute); font-size: 12px">—</span>
                                </td>
                                <td>
                                    <span
                                        class="lang-badge"
                                        :style="{ background: langBadge(tmpl).color + '18', color: langBadge(tmpl).color }"
                                    >
                                        {{ langBadge(tmpl).label }}
                                    </span>
                                </td>
                                <td>
                                    <span v-if="tmpl.is_active" class="status-active">
                                        {{ isArabic ? 'نشط' : 'Active' }}
                                    </span>
                                    <span v-else class="status-inactive">
                                        {{ isArabic ? 'معطّل' : 'Inactive' }}
                                    </span>
                                </td>
                                <td style="white-space: nowrap; text-align: center">
                                    <Link
                                        :href="`/dashboard/form-templates/${tmpl.id}/fields`"
                                        class="admin-icon-btn admin-icon-edit"
                                        style="background: var(--accent); color: white; border-color: var(--accent); margin-inline-end: 4px;"
                                    >
                                        {{ isArabic ? 'تصميم ✏️' : 'Build ✏️' }}
                                    </Link>
                                    <button
                                        type="button"
                                        class="admin-icon-btn admin-icon-edit"
                                        @click="select(tmpl)"
                                    >
                                        {{ isArabic ? 'تعديل' : 'Edit' }}
                                    </button>
                                    <button
                                        type="button"
                                        class="admin-icon-btn admin-icon-delete"
                                        @click="() => {
                                            if (confirm(isArabic ? 'هل أنت متأكد؟' : 'Are you sure?')) {
                                                router.delete(destroy(tmpl.id).url);
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
                            ? isArabic ? 'تعديل النموذج' : 'Edit Template'
                            : isArabic ? 'إضافة نموذج' : 'Add Template'
                    }}
                </h3>

                <form @submit.prevent="submit" class="admin-form-stack">
                    <!-- Department -->
                    <div class="field">
                        <label for="tmpl-dept">{{ isArabic ? 'القسم' : 'Department' }}</label>
                        <select id="tmpl-dept" v-model="form.department_id" required>
                            <option value="">{{ isArabic ? 'اختر قسماً...' : 'Select department...' }}</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">
                                {{ isArabic ? d.name_ar : d.name_en }}
                            </option>
                        </select>
                        <p v-if="form.errors.department_id" class="field-error">{{ form.errors.department_id }}</p>
                    </div>

                    <!-- Category (filtered by dept) -->
                    <div class="field">
                        <label for="tmpl-cat">{{ isArabic ? 'التصنيف' : 'Category' }}</label>
                        <select id="tmpl-cat" v-model="form.category_code" :disabled="!form.department_id">
                            <option value="">{{ isArabic ? 'بدون تصنيف' : 'No category' }}</option>
                            <option v-for="c in formCategories" :key="c.id" :value="c.code">
                                {{ isArabic ? c.name_ar : c.name_en }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_code" class="field-error">{{ form.errors.category_code }}</p>
                        <p
                            v-if="form.department_id && formCategories.length === 0"
                            style="font-size: 11px; color: var(--orange); margin-top: 4px"
                        >
                            {{ isArabic ? 'لا توجد تصنيفات لهذا القسم' : 'No categories for this department' }}
                        </p>
                    </div>

                    <!-- Code -->
                    <div class="field">
                        <label for="tmpl-code">{{ isArabic ? 'الرمز (Code)' : 'Code' }}</label>
                        <input
                            id="tmpl-code"
                            v-model="form.code"
                            :disabled="!!editing"
                            required
                            placeholder="surgery"
                            dir="ltr"
                        />
                        <p v-if="form.errors.code" class="field-error">{{ form.errors.code }}</p>
                    </div>

                    <!-- Names -->
                    <div class="field">
                        <label for="tmpl-name-en">{{ isArabic ? 'الاسم (EN)' : 'Name (EN)' }}</label>
                        <input id="tmpl-name-en" v-model="form.name_en" required dir="ltr" />
                        <p v-if="form.errors.name_en" class="field-error">{{ form.errors.name_en }}</p>
                    </div>

                    <div class="field">
                        <label for="tmpl-name-ar">{{ isArabic ? 'الاسم (AR)' : 'Name (AR)' }}</label>
                        <input id="tmpl-name-ar" v-model="form.name_ar" required dir="rtl" />
                        <p v-if="form.errors.name_ar" class="field-error">{{ form.errors.name_ar }}</p>
                    </div>

                    <!-- Language mode -->
                    <div class="field">
                        <label style="margin-bottom: 8px; display: block">{{ isArabic ? 'إعداد اللغة' : 'Language Mode' }}</label>
                        <div class="lang-options">
                            <label class="lang-option" :class="{ selected: !form.is_bilingual && !form.is_ar_only }">
                                <input
                                    v-model="form.is_bilingual"
                                    type="radio"
                                    :value="false"
                                    name="lang_mode"
                                    @change="form.is_ar_only = false"
                                />
                                {{ isArabic ? 'عربي' : 'Arabic' }}
                            </label>
                            <label class="lang-option" :class="{ selected: form.is_ar_only }">
                                <input
                                    v-model="form.is_ar_only"
                                    type="radio"
                                    :value="true"
                                    name="lang_mode"
                                    @change="form.is_bilingual = false"
                                />
                                {{ isArabic ? 'عربي فقط' : 'AR Only' }}
                            </label>
                            <label class="lang-option" :class="{ selected: form.is_bilingual }">
                                <input
                                    v-model="form.is_bilingual"
                                    type="radio"
                                    :value="true"
                                    name="lang_mode"
                                    @change="form.is_ar_only = false"
                                />
                                {{ isArabic ? 'ثنائي اللغة' : 'Bilingual' }}
                            </label>
                        </div>
                    </div>

                    <!-- Sort + Active -->
                    <div class="field">
                        <label for="tmpl-sort">{{ isArabic ? 'ترتيب العرض' : 'Sort Order' }}</label>
                        <input id="tmpl-sort" v-model.number="form.sort_order" type="number" min="0" />
                    </div>

                    <div class="field">
                        <label class="toggle-label">
                            <span>{{ isArabic ? 'نشط' : 'Active' }}</span>
                            <div class="toggle-wrap">
                                <input id="tmpl-active" v-model="form.is_active" type="checkbox" class="toggle-input" />
                                <label for="tmpl-active" class="toggle-track"></label>
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
.admin-page-head h2 { font-size: 22px; font-weight: 800; color: var(--primary); margin: 0 0 4px; }
.admin-page-head p  { color: var(--ink-mute); font-size: 13px; margin: 0; }
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
.filter-input:focus, .filter-select:focus { outline: none; border-color: var(--primary); }
.admin-split-layout {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    align-items: flex-start;
}
.admin-split-main { flex: 2; min-width: 300px; }
.admin-split-side { flex: 1; min-width: 290px; position: sticky; top: 120px; }
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
.cat-tag {
    background: rgba(13, 148, 136, 0.1);
    color: var(--accent);
    padding: 3px 10px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
}
.lang-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 10px;
    font-size: 11.5px;
    font-weight: 700;
}
.status-active  { color: var(--green); font-weight: 700; font-size: 13px; }
.status-inactive { color: var(--red);   font-weight: 700; font-size: 13px; }
.admin-form-stack { display: flex; flex-direction: column; gap: 14px; }
.admin-form-actions { display: flex; justify-content: space-between; gap: 10px; margin-top: 8px; }
.field-error { color: var(--red); font-size: 12px; font-weight: 700; margin-top: 4px; }

/* Language radio options */
.lang-options {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.lang-option {
    flex: 1;
    min-width: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 9px 12px;
    border: 1.5px solid var(--line);
    border-radius: 9px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: var(--ink-soft);
    transition: all 0.15s;
    background: var(--bg-soft);
    user-select: none;
}
.lang-option input { display: none; }
.lang-option:hover { border-color: var(--accent); color: var(--accent); }
.lang-option.selected { border-color: var(--accent); background: rgba(13,148,136,.12); color: var(--accent); font-weight: 700; }

/* Toggle */
.toggle-label { display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--ink-soft); }
.toggle-wrap { position: relative; display: flex; align-items: center; }
.toggle-input { opacity: 0; width: 0; height: 0; position: absolute; }
.toggle-track { display: block; width: 44px; height: 24px; background: var(--line); border-radius: 12px; cursor: pointer; transition: background 0.2s; position: relative; }
.toggle-track::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: white; border-radius: 50%; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
.toggle-input:checked + .toggle-track { background: var(--green); }
.toggle-input:checked + .toggle-track::after { transform: translateX(20px); }
</style>
