<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { index, create, edit, destroy } from '@/routes/admin/users';
import type { Branch, Department, User } from '@/types/admin';

const props = defineProps<{
    users: {
        data: User[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        total: number;
    };
    filters: Record<string, string | undefined>;
    branches: Branch[];
    departments: Department[];
}>();

const { isArabic } = useClinicLocale();
const search = ref(props.filters.search || '');
const branchId = ref(props.filters.branch_id?.toString() || 'all');
const departmentId = ref(props.filters.department_id?.toString() || 'all');
const status = ref(props.filters.status || 'all');

watch([search, branchId, departmentId, status], () => {
    router.get(
        index().url,
        {
            search: search.value,
            branch_id: branchId.value === 'all' ? null : branchId.value,
            department_id: departmentId.value === 'all' ? null : departmentId.value,
            status: status.value === 'all' ? null : status.value,
        },
        { preserveState: true, replace: true },
    );
});
</script>

<template>
    <Head :title="isArabic ? 'المستخدمون' : 'Users'" />

    <AdminPageLayout
        :form-name="{ ar: 'إدارة المستخدمين', en: 'User Management' }"
        show-back
        back-href="/dashboard/clinic/home"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText :value="{ ar: 'المستخدمون', en: 'Users' }" />
                </h2>
                <p>
                    {{
                        isArabic
                            ? 'إدارة حسابات الموظفين والصلاحيات'
                            : 'Manage staff accounts and access'
                    }}
                </p>
            </div>
            <Link :href="create().url" class="btn btn-primary">
                <LocalizedText :value="{ ar: 'إضافة مستخدم', en: 'Add User' }" />
            </Link>
        </div>

        <div class="admin-table-card">
            <div class="admin-filters">
                <input
                    v-model="search"
                    type="search"
                    class="admin-search"
                    :placeholder="
                        isArabic
                            ? 'ابحث بالاسم أو البريد...'
                            : 'Search by name or email...'
                    "
                />
                <select v-model="branchId">
                    <option value="all">
                        {{ isArabic ? 'كل الفروع' : 'All Branches' }}
                    </option>
                    <option
                        v-for="branch in branches"
                        :key="branch.id"
                        :value="branch.id.toString()"
                    >
                        {{ branch.name_en }}
                    </option>
                </select>
                <select v-model="departmentId">
                    <option value="all">
                        {{ isArabic ? 'كل الأقسام' : 'All Departments' }}
                    </option>
                    <option
                        v-for="dept in departments"
                        :key="dept.id"
                        :value="dept.id.toString()"
                    >
                        {{ dept.name_en }}
                    </option>
                </select>
                <select v-model="status">
                    <option value="all">
                        {{ isArabic ? 'كل الحالات' : 'All Status' }}
                    </option>
                    <option value="active">
                        {{ isArabic ? 'نشط' : 'Active' }}
                    </option>
                    <option value="inactive">
                        {{ isArabic ? 'غير نشط' : 'Inactive' }}
                    </option>
                </select>
            </div>

            <div style="overflow-x: auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>{{ isArabic ? 'المستخدم' : 'User' }}</th>
                            <th>{{ isArabic ? 'الفرع / القسم' : 'Branch / Dept' }}</th>
                            <th>{{ isArabic ? 'الأدوار' : 'Roles' }}</th>
                            <th>{{ isArabic ? 'الحالة' : 'Status' }}</th>
                            <th>{{ isArabic ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td>
                                <strong>{{ user.name }}</strong>
                                <div style="font-size: 12px; color: var(--ink-mute)">
                                    {{ user.email }}
                                </div>
                            </td>
                            <td>
                                {{ user.branch?.name_en || '—' }}
                                <div style="font-size: 12px; color: var(--ink-mute)">
                                    {{ user.department?.name_en || '—' }}
                                </div>
                            </td>
                            <td>
                                <span
                                    v-for="role in user.roles"
                                    :key="role.id"
                                    style="
                                        display: inline-block;
                                        margin: 2px;
                                        padding: 2px 8px;
                                        border-radius: 6px;
                                        font-size: 11px;
                                        font-weight: 700;
                                        background: var(--primary-soft);
                                        color: var(--primary);
                                    "
                                >
                                    {{ role.name }}
                                </span>
                            </td>
                            <td>
                                <span
                                    v-if="user.email_verified_at"
                                    style="color: var(--green); font-weight: 700"
                                >
                                    {{ isArabic ? 'نشط' : 'Active' }}
                                </span>
                                <span v-else style="color: var(--red); font-weight: 700">
                                    {{ isArabic ? 'غير نشط' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align: center; white-space: nowrap">
                                <Link
                                    :href="edit(user.id).url"
                                    class="admin-icon-btn admin-icon-edit"
                                >
                                    {{ isArabic ? 'تعديل' : 'Edit' }}
                                </Link>
                                <button
                                    type="button"
                                    class="admin-icon-btn admin-icon-delete"
                                    @click="
                                        () => {
                                            if (confirm(isArabic ? 'هل أنت متأكد؟' : 'Are you sure?')) {
                                                router.delete(destroy(user.id).url);
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

            <div
                v-if="users.links.length > 3"
                style="
                    margin-top: 16px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 12px;
                "
            >
                <p style="font-size: 12px; color: var(--ink-mute); margin: 0">
                    {{ users.total }}
                    {{ isArabic ? 'نتيجة' : 'results' }}
                </p>
                <div style="display: flex; gap: 6px; flex-wrap: wrap">
                    <template v-for="(link, k) in users.links" :key="k">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="btn btn-outline"
                            :class="{ 'btn-primary': link.active }"
                            style="padding: 6px 12px; font-size: 12px"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AdminPageLayout>
</template>
