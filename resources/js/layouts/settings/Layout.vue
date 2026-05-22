<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ClinicHeader from '@/clinic/components/layout/ClinicHeader.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';

const { isCurrentOrParentUrl } = useCurrentUrl();

const sidebarNavItems = [
    {
        href: editProfile(),
        label: { ar: 'الملف الشخصي', en: 'Profile' },
    },
    {
        href: editSecurity(),
        label: { ar: 'الأمان', en: 'Security' },
    },
    {
        href: editAppearance(),
        label: { ar: 'المظهر', en: 'Appearance' },
    },
];
</script>

<template>
    <div class="admin-screen active">
        <ClinicHeader :show-top-bar="false" />

        <div class="admin-content settings-layout">
            <div class="settings-layout-head">
                <h2>
                    <LocalizedText
                        :value="{ ar: 'الإعدادات', en: 'Settings' }"
                    />
                </h2>
                <p>
                    <LocalizedText
                        :value="{
                            ar: 'إدارة الملف الشخصي والأمان',
                            en: 'Manage your profile and account settings',
                        }"
                    />
                </p>
            </div>

            <div class="settings-layout-body">
                <aside class="settings-sidebar">
                    <nav aria-label="Settings">
                        <Link
                            v-for="item in sidebarNavItems"
                            :key="toUrl(item.href)"
                            :href="item.href"
                            class="settings-sidebar-link"
                            :class="{
                                active: isCurrentOrParentUrl(item.href),
                            }"
                        >
                            <LocalizedText :value="item.label" />
                        </Link>
                    </nav>
                </aside>

                <div class="settings-main">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.settings-layout-head {
    margin-bottom: 24px;
}
.settings-layout-head h2 {
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
}
.settings-layout-head p {
    font-size: 14px;
    color: var(--ink-mute);
    margin-top: 4px;
}
.settings-layout-body {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
@media (min-width: 900px) {
    .settings-layout-body {
        flex-direction: row;
        align-items: flex-start;
    }
}
.settings-sidebar {
    flex-shrink: 0;
}
.settings-sidebar nav {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
@media (min-width: 900px) {
    .settings-sidebar nav {
        min-width: 180px;
    }
}
.settings-sidebar-link {
    display: block;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    color: var(--ink-mute);
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
}
.settings-sidebar-link:hover {
    background: var(--bg-soft);
    color: var(--primary);
}
.settings-sidebar-link.active {
    background: var(--primary-soft);
    color: var(--primary);
}
.settings-main {
    flex: 1;
    min-width: 0;
}
</style>
