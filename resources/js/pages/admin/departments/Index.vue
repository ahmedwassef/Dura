<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { wayfinderFormRoute } from '@/lib/utils';
import { store, update, destroy } from '@/routes/admin/departments';
import type { Department } from '@/types/admin';

defineProps<{
    departments: Department[];
}>();

const { isArabic } = useClinicLocale();
const editingDept = ref<Department | null>(null);

const form = useForm({
    code: '',
    name_ar: '',
    name_en: '',
    status: 'active',
    description: '',
});

function selectDept(dept: Department | null): void {
    editingDept.value = dept;
    if (dept) {
        form.code = dept.code;
        form.name_ar = dept.name_ar;
        form.name_en = dept.name_en;
        form.status = dept.status;
        form.description = dept.description || '';
    } else {
        form.reset();
        form.status = 'active';
    }
}

function submit(): void {
    const route = editingDept.value
        ? update.form(editingDept.value.id)
        : store.form();
    const { method, url } = wayfinderFormRoute(route);

    form.submit(method, url, {
        onSuccess: () => selectDept(null),
    });
}
</script>

<template>
    <Head :title="isArabic ? 'الأقسام' : 'Departments'" />

    <AdminPageLayout
        :form-name="{ ar: 'إدارة الأقسام', en: 'Department Management' }"
        show-back
        back-href="/dashboard/clinic/home"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText :value="{ ar: 'الأقسام', en: 'Departments' }" />
                </h2>
                <p>
                    {{
                        isArabic
                            ? 'إدارة الأقسام الطبية في النظام'
                            : 'Manage medical departments in the system'
                    }}
                </p>
            </div>
            <button type="button" class="btn btn-primary" @click="selectDept(null)">
                <LocalizedText :value="{ ar: 'قسم جديد', en: 'New Department' }" />
            </button>
        </div>

        <div class="admin-split-layout">
            <div class="admin-table-card admin-split-main">
                <h3 style="font-size: 18px; color: var(--primary); margin: 0 0 16px">
                    <LocalizedText :value="{ ar: 'قائمة الأقسام', en: 'Departments List' }" />
                </h3>

                <div style="overflow-x: auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>{{ isArabic ? 'الرمز' : 'Code' }}</th>
                                <th>{{ isArabic ? 'الاسم' : 'Name' }}</th>
                                <th>{{ isArabic ? 'الحالة' : 'Status' }}</th>
                                <th>{{ isArabic ? 'إجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="dept in departments" :key="dept.id">
                                <td style="font-weight: 700; color: var(--primary)">
                                    {{ dept.code }}
                                </td>
                                <td>
                                    <strong>{{ dept.name_en }}</strong>
                                    <div style="font-size: 12px; color: var(--ink-mute)">
                                        {{ dept.name_ar }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        v-if="dept.status === 'active'"
                                        style="color: var(--green); font-weight: 700"
                                    >
                                        {{ isArabic ? 'نشط' : 'Active' }}
                                    </span>
                                    <span v-else style="color: var(--red); font-weight: 700">
                                        {{ isArabic ? 'غير نشط' : 'Inactive' }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap">
                                    <button
                                        type="button"
                                        class="admin-icon-btn admin-icon-edit"
                                        @click="selectDept(dept)"
                                    >
                                        {{ isArabic ? 'تعديل' : 'Edit' }}
                                    </button>
                                    <button
                                        type="button"
                                        class="admin-icon-btn admin-icon-delete"
                                        @click="
                                            () => {
                                                if (
                                                    confirm(
                                                        isArabic ? 'هل أنت متأكد؟' : 'Are you sure?',
                                                    )
                                                ) {
                                                    router.delete(destroy(dept.id).url);
                                                }
                                            }
                                        "
                                    >
                                        {{ isArabic ? 'حذف' : 'Delete' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-table-card admin-split-side">
                <h3 style="font-size: 18px; color: var(--primary); margin: 0 0 16px">
                    {{
                        editingDept
                            ? isArabic
                                ? 'تعديل القسم'
                                : 'Edit Department'
                            : isArabic
                              ? 'إضافة قسم'
                              : 'Add Department'
                    }}
                </h3>

                <form @submit.prevent="submit" class="admin-form-stack">
                    <div class="field">
                        <label for="code">{{ isArabic ? 'رمز القسم' : 'Department Code' }}</label>
                        <input id="code" v-model="form.code" required placeholder="dental" />
                        <p v-if="form.errors.code" class="field-error">{{ form.errors.code }}</p>
                    </div>
                    <div class="field">
                        <label for="name_en">{{ isArabic ? 'الاسم (EN)' : 'Name (EN)' }}</label>
                        <input id="name_en" v-model="form.name_en" required />
                    </div>
                    <div class="field">
                        <label for="name_ar">{{ isArabic ? 'الاسم (AR)' : 'Name (AR)' }}</label>
                        <input id="name_ar" v-model="form.name_ar" dir="rtl" required />
                    </div>
                    <div class="field">
                        <label for="status">{{ isArabic ? 'الحالة' : 'Status' }}</label>
                        <select id="status" v-model="form.status">
                            <option value="active">
                                {{ isArabic ? 'نشط' : 'Active' }}
                            </option>
                            <option value="inactive">
                                {{ isArabic ? 'غير نشط' : 'Inactive' }}
                            </option>
                        </select>
                    </div>
                    <div class="admin-form-actions">
                        <button
                            v-if="editingDept"
                            type="button"
                            class="btn btn-outline"
                            @click="selectDept(null)"
                        >
                            {{ isArabic ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{
                                editingDept
                                    ? isArabic
                                        ? 'تحديث'
                                        : 'Update'
                                    : isArabic
                                      ? 'إنشاء'
                                      : 'Create'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminPageLayout>
</template>

<style scoped>
.admin-split-layout {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    align-items: flex-start;
}
.admin-split-main {
    flex: 2;
    min-width: 300px;
}
.admin-split-side {
    flex: 1;
    min-width: 280px;
    position: sticky;
    top: 120px;
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
</style>
