<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { wayfinderFormRoute } from '@/lib/utils';
import { index, store, update } from '@/routes/admin/users';
import type { Branch, Department, Role, User } from '@/types/admin';

const props = defineProps<{
    user?: User;
    branches: Branch[];
    departments: Department[];
    roles: Role[];
}>();

const { isArabic } = useClinicLocale();

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
    password: '',
    display_name_ar: props.user?.display_name_ar || '',
    branch_id: props.user?.branch_id?.toString() || '',
    department_id: props.user?.department_id?.toString() || '',
    status: props.user?.status || 'active',
    roles: props.user?.roles?.map((r) => r.name) || [],
});

function submit(): void {
    const route = props.user
        ? update.form(props.user.id)
        : store.form();
    const { method, url } = wayfinderFormRoute(route);

    form.submit(method, url);
}

function toggleRole(roleName: string): void {
    const i = form.roles.indexOf(roleName);
    if (i === -1) {
        form.roles.push(roleName);
    } else {
        form.roles.splice(i, 1);
    }
}
</script>

<template>
    <Head :title="user ? (isArabic ? 'تعديل مستخدم' : 'Edit User') : isArabic ? 'إضافة مستخدم' : 'Add User'" />

    <AdminPageLayout
        :form-name="{
            ar: user ? 'تعديل مستخدم' : 'إضافة مستخدم',
            en: user ? 'Edit User' : 'Add User',
        }"
        show-back
        :back-href="index().url"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText
                        :value="{
                            ar: user ? 'تعديل مستخدم' : 'إضافة مستخدم جديد',
                            en: user ? 'Edit User' : 'Add New User',
                        }"
                    />
                </h2>
            </div>
            <Link :href="index().url" class="btn btn-outline">
                <LocalizedText :value="{ ar: 'العودة للقائمة', en: 'Back to list' }" />
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div class="admin-table-card" style="margin-bottom: 20px">
                <h3 style="font-size: 18px; color: var(--primary); margin: 0 0 16px">
                    <LocalizedText
                        :value="{ ar: 'معلومات الحساب', en: 'Account Information' }"
                    />
                </h3>

                <div class="field-grid">
                    <div class="field">
                        <label for="name">
                            {{ isArabic ? 'الاسم (إنجليزي)' : 'Full Name (English)' }}
                        </label>
                        <input id="name" v-model="form.name" required />
                        <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
                    </div>
                    <div class="field">
                        <label for="display_name_ar">
                            {{ isArabic ? 'الاسم (عربي)' : 'Full Name (Arabic)' }}
                        </label>
                        <input id="display_name_ar" v-model="form.display_name_ar" dir="rtl" />
                        <p v-if="form.errors.display_name_ar" class="field-error">
                            {{ form.errors.display_name_ar }}
                        </p>
                    </div>
                    <div class="field">
                        <label for="email">{{ isArabic ? 'البريد' : 'Email' }}</label>
                        <input id="email" v-model="form.email" type="email" required />
                        <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                    </div>
                    <div class="field">
                        <label for="phone">{{ isArabic ? 'الجوال' : 'Phone' }}</label>
                        <input id="phone" v-model="form.phone" />
                        <p v-if="form.errors.phone" class="field-error">{{ form.errors.phone }}</p>
                    </div>
                    <div class="field" style="grid-column: 1 / -1">
                        <label for="password">
                            {{
                                user
                                    ? isArabic
                                        ? 'كلمة مرور جديدة (اختياري)'
                                        : 'New password (optional)'
                                    : isArabic
                                      ? 'كلمة المرور'
                                      : 'Password'
                            }}
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            :required="!user"
                        />
                        <p v-if="form.errors.password" class="field-error">
                            {{ form.errors.password }}
                        </p>
                    </div>
                    <div class="field">
                        <label for="branch_id">{{ isArabic ? 'الفرع' : 'Branch' }}</label>
                        <select id="branch_id" v-model="form.branch_id">
                            <option value="">
                                {{ isArabic ? 'اختر الفرع' : 'Select branch' }}
                            </option>
                            <option
                                v-for="branch in branches"
                                :key="branch.id"
                                :value="branch.id.toString()"
                            >
                                {{ branch.name_en }}
                            </option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="department_id">
                            {{ isArabic ? 'القسم' : 'Department' }}
                        </label>
                        <select id="department_id" v-model="form.department_id">
                            <option value="">
                                {{ isArabic ? 'اختر القسم' : 'Select department' }}
                            </option>
                            <option
                                v-for="dept in departments"
                                :key="dept.id"
                                :value="dept.id.toString()"
                            >
                                {{ dept.name_en }}
                            </option>
                        </select>
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
                </div>
            </div>

            <div class="admin-table-card">
                <h3 style="font-size: 18px; color: var(--primary); margin: 0 0 16px">
                    <LocalizedText
                        :value="{ ar: 'الأدوار والصلاحيات', en: 'Roles & Permissions' }"
                    />
                </h3>

                <div
                    style="
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                        gap: 12px;
                    "
                >
                    <button
                        v-for="role in roles"
                        :key="role.id"
                        type="button"
                        class="role-pick-card"
                        :class="{ active: form.roles.includes(role.name) }"
                        @click="toggleRole(role.name)"
                    >
                        {{ role.name }}
                    </button>
                </div>
                <p v-if="form.errors.roles" class="field-error">{{ form.errors.roles }}</p>

                <div style="margin-top: 20px; display: flex; justify-content: flex-end">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <LocalizedText
                            :value="{
                                ar: user ? 'تحديث المستخدم' : 'إنشاء المستخدم',
                                en: user ? 'Update User' : 'Create User',
                            }"
                        />
                    </button>
                </div>
            </div>
        </form>
    </AdminPageLayout>
</template>

<style scoped>
.field-error {
    color: var(--red);
    font-size: 12px;
    font-weight: 700;
    margin-top: 4px;
}
.role-pick-card {
    padding: 14px;
    border-radius: 10px;
    border: 1.5px solid var(--line);
    background: var(--bg-soft);
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
}
.role-pick-card.active {
    border-color: var(--primary);
    background: var(--primary-soft);
    color: var(--primary);
}
</style>
