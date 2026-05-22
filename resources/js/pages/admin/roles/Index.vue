<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { wayfinderFormRoute } from '@/lib/utils';
import { store, update, destroy } from '@/routes/admin/roles';
import type { Role } from '@/types/admin';

const props = defineProps<{
    roles: Role[];
    permissions: Record<string, Array<{ id: number; name: string }>>;
}>();

const { isArabic } = useClinicLocale();
const editingRole = ref<Role | null>(null);

const form = useForm({
    name: '',
    permissions: [] as string[],
});

function selectRole(role: Role | null): void {
    editingRole.value = role;
    if (role) {
        form.name = role.name;
        form.permissions = role.permissions?.map((p) => p.name) || [];
    } else {
        form.name = '';
        form.permissions = [];
    }
    form.clearErrors();
}

function submit(): void {
    const route = editingRole.value
        ? update.form(editingRole.value.id)
        : store.form();
    const { method, url } = wayfinderFormRoute(route);

    form.submit(method, url, {
        onSuccess: () => selectRole(null),
    });
}

function togglePermission(permissionName: string): void {
    if (editingRole.value?.name === 'super-admin') {
        return;
    }
    const i = form.permissions.indexOf(permissionName);
    if (i === -1) {
        form.permissions.push(permissionName);
    } else {
        form.permissions.splice(i, 1);
    }
}

function toggleModule(permissions: Array<{ name: string }>): void {
    if (editingRole.value?.name === 'super-admin') {
        return;
    }
    const names = permissions.map((p) => p.name);
    const hasAll = names.every((p) => form.permissions.includes(p));
    if (hasAll) {
        form.permissions = form.permissions.filter((p) => !names.includes(p));
    } else {
        form.permissions = [...new Set([...form.permissions, ...names])];
    }
}
</script>

<template>
    <Head :title="isArabic ? 'الأدوار' : 'Roles'" />

    <AdminPageLayout
        :form-name="{ ar: 'الأدوار والصلاحيات', en: 'Roles & Permissions' }"
        show-back
        back-href="/dashboard/clinic/home"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText
                        :value="{ ar: 'الأدوار والصلاحيات', en: 'Roles & Permissions' }"
                    />
                </h2>
                <p>
                    {{
                        isArabic
                            ? 'تحكم بمستويات الوصول والصلاحيات'
                            : 'Control access levels and permissions'
                    }}
                </p>
            </div>
            <button type="button" class="btn btn-primary" @click="selectRole(null)">
                <LocalizedText :value="{ ar: 'دور جديد', en: 'New Role' }" />
            </button>
        </div>

        <div class="admin-split-layout roles-layout">
            <div class="admin-table-card admin-split-side roles-list">
                <h3 style="font-size: 18px; color: var(--primary); margin: 0 0 16px">
                    <LocalizedText :value="{ ar: 'قائمة الأدوار', en: 'Roles List' }" />
                </h3>

                <div class="roles-cards">
                    <button
                        v-for="role in roles"
                        :key="role.id"
                        type="button"
                        class="role-list-card"
                        :class="{ active: editingRole?.id === role.id }"
                        @click="selectRole(role)"
                    >
                        <span class="role-list-name">{{ role.name }}</span>
                        <span class="role-list-count">
                            {{ role.permissions?.length || 0 }}
                            {{ isArabic ? 'صلاحية' : 'perms' }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="admin-table-card admin-split-main">
                <h3 style="font-size: 18px; color: var(--primary); margin: 0 0 8px">
                    {{
                        editingRole
                            ? `${isArabic ? 'تعديل:' : 'Edit:'} ${editingRole.name}`
                            : isArabic
                              ? 'إنشاء دور جديد'
                              : 'Create New Role'
                    }}
                </h3>

                <form @submit.prevent="submit">
                    <div class="field" style="margin-bottom: 20px">
                        <label for="role-name">{{ isArabic ? 'اسم الدور' : 'Role Name' }}</label>
                        <input
                            id="role-name"
                            v-model="form.name"
                            :disabled="editingRole?.name === 'super-admin'"
                            placeholder="doctor"
                        />
                        <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
                    </div>

                    <h4 class="perm-matrix-title">
                        {{ isArabic ? 'مصفوفة الصلاحيات' : 'Permissions Matrix' }}
                    </h4>

                    <div
                        v-for="(perms, module) in permissions"
                        :key="module"
                        class="perm-module-card"
                    >
                        <div class="perm-module-head">
                            <span>{{ module }}</span>
                            <button
                                type="button"
                                class="perm-toggle-all"
                                @click="toggleModule(perms)"
                            >
                                {{ isArabic ? 'تحديد الكل' : 'Toggle all' }}
                            </button>
                        </div>
                        <div class="perm-grid">
                            <button
                                v-for="permission in perms"
                                :key="permission.id"
                                type="button"
                                class="perm-chip"
                                :class="{
                                    active: form.permissions.includes(permission.name),
                                    disabled: editingRole?.name === 'super-admin',
                                }"
                                @click="togglePermission(permission.name)"
                            >
                                {{ permission.name.split('.')[1] || permission.name }}
                            </button>
                        </div>
                    </div>

                    <div class="admin-form-actions" style="margin-top: 24px">
                        <button
                            v-if="editingRole"
                            type="button"
                            class="btn btn-outline"
                            @click="selectRole(null)"
                        >
                            {{ isArabic ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button
                            v-if="editingRole && editingRole.name !== 'super-admin'"
                            type="button"
                            class="btn btn-outline"
                            style="color: var(--red); border-color: var(--red-soft)"
                            @click="
                                () => {
                                    if (
                                        confirm(isArabic ? 'هل أنت متأكد؟' : 'Are you sure?')
                                    ) {
                                        router.delete(destroy(editingRole!.id).url);
                                    }
                                }
                            "
                        >
                            {{ isArabic ? 'حذف الدور' : 'Delete role' }}
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            :disabled="form.processing || editingRole?.name === 'super-admin'"
                        >
                            <LocalizedText
                                :value="{
                                    ar: editingRole ? 'تحديث الدور' : 'إنشاء الدور',
                                    en: editingRole ? 'Update Role' : 'Create Role',
                                }"
                            />
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminPageLayout>
</template>

<style scoped>
.roles-layout {
    align-items: flex-start;
}
.roles-list {
    flex: 0 0 280px;
    min-width: 260px;
}
.admin-split-main {
    flex: 1;
    min-width: 300px;
}
.admin-split-layout {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}
.roles-cards {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.role-list-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 14px 16px;
    border-radius: 10px;
    border: 1.5px solid var(--line);
    background: var(--bg-soft);
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
}
.role-list-card.active,
.role-list-card:hover {
    border-color: var(--primary);
    background: var(--primary-soft);
}
.role-list-name {
    font-weight: 800;
    font-size: 14px;
    color: var(--ink);
}
.role-list-count {
    font-size: 11px;
    font-weight: 700;
    color: var(--ink-mute);
}
.perm-matrix-title {
    font-size: 13px;
    font-weight: 800;
    color: var(--ink-mute);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0 0 14px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--line);
}
.perm-module-card {
    border: 1px solid var(--line);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 14px;
}
.perm-module-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: var(--bg-soft);
    border-bottom: 1px solid var(--line);
    font-weight: 800;
    font-size: 13px;
    text-transform: capitalize;
}
.perm-toggle-all {
    border: none;
    background: none;
    color: var(--primary);
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    font-family: inherit;
}
.perm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 10px;
    padding: 14px;
}
.perm-chip {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1.5px solid var(--line);
    background: white;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    text-transform: capitalize;
    transition: all 0.15s;
}
.perm-chip.active {
    border-color: var(--primary);
    background: var(--primary-soft);
    color: var(--primary);
}
.perm-chip.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.admin-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}
.field-error {
    color: var(--red);
    font-size: 12px;
    font-weight: 700;
    margin-top: 4px;
}
</style>
