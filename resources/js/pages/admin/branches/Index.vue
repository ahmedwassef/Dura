<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { wayfinderFormRoute } from '@/lib/utils';
import { store, update, destroy } from '@/routes/admin/branches';
import type { Branch } from '@/types/admin';

defineProps<{
    branches: Branch[];
}>();

const { isArabic } = useClinicLocale();
const editingBranch = ref<Branch | null>(null);

const form = useForm({
    code: '',
    name_ar: '',
    name_en: '',
    address: '',
    phone: '',
    is_active: true,
});

function selectBranch(branch: Branch | null): void {
    editingBranch.value = branch;
    if (branch) {
        form.code = branch.code;
        form.name_ar = branch.name_ar;
        form.name_en = branch.name_en;
        form.address = branch.address || '';
        form.phone = branch.phone || '';
        form.is_active = branch.is_active;
    } else {
        form.reset();
        form.is_active = true;
    }
}

function submit(): void {
    const route = editingBranch.value
        ? update.form(editingBranch.value.id)
        : store.form();
    const { method, url } = wayfinderFormRoute(route);

    form.submit(method, url, {
        onSuccess: () => selectBranch(null),
    });
}
</script>

<template>
    <Head :title="isArabic ? 'الفروع' : 'Branches'" />

    <AdminPageLayout
        :form-name="{ ar: 'إدارة الفروع', en: 'Branch Management' }"
        show-back
        back-href="/dashboard/clinic/home"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText :value="{ ar: 'الفروع', en: 'Branches' }" />
                </h2>
                <p>
                    {{
                        isArabic
                            ? 'إدارة فروع المجمع الطبي'
                            : 'Manage medical complex branches'
                    }}
                </p>
            </div>
            <button type="button" class="btn btn-primary" @click="selectBranch(null)">
                <LocalizedText :value="{ ar: 'فرع جديد', en: 'New Branch' }" />
            </button>
        </div>

        <div class="admin-split-layout">
            <div class="admin-table-card admin-split-main">
                <h3 style="font-size: 18px; color: var(--primary); margin: 0 0 16px">
                    <LocalizedText :value="{ ar: 'قائمة الفروع', en: 'Branches List' }" />
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
                            <tr v-for="branch in branches" :key="branch.id">
                                <td style="font-weight: 700; color: var(--primary)">
                                    {{ branch.code }}
                                </td>
                                <td>
                                    <strong>{{ branch.name_en }}</strong>
                                    <div style="font-size: 12px; color: var(--ink-mute)">
                                        {{ branch.name_ar }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        v-if="branch.is_active"
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
                                        @click="selectBranch(branch)"
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
                                                    router.delete(destroy(branch.id).url);
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
                        editingBranch
                            ? isArabic
                                ? 'تعديل الفرع'
                                : 'Edit Branch'
                            : isArabic
                              ? 'إضافة فرع'
                              : 'Add Branch'
                    }}
                </h3>

                <form @submit.prevent="submit" class="admin-form-stack">
                    <div class="field">
                        <label for="code">{{ isArabic ? 'رمز الفرع' : 'Branch Code' }}</label>
                        <input id="code" v-model="form.code" required placeholder="zulfi" />
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
                        <label for="address">{{ isArabic ? 'العنوان' : 'Address' }}</label>
                        <input id="address" v-model="form.address" />
                    </div>
                    <div class="field">
                        <label for="phone">{{ isArabic ? 'الهاتف' : 'Phone' }}</label>
                        <input id="phone" v-model="form.phone" />
                    </div>
                    <label class="admin-checkbox">
                        <input v-model="form.is_active" type="checkbox" />
                        <span>{{ isArabic ? 'فرع نشط' : 'Active branch' }}</span>
                    </label>
                    <div class="admin-form-actions">
                        <button
                            v-if="editingBranch"
                            type="button"
                            class="btn btn-outline"
                            @click="selectBranch(null)"
                        >
                            {{ isArabic ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{
                                editingBranch
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
.admin-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
}
.field-error {
    color: var(--red);
    font-size: 12px;
    font-weight: 700;
    margin-top: 4px;
}
</style>
