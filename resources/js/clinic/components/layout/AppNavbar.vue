<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { CLINIC_BASE } from '@/clinic/constants';
import {
    NAV_GROUPS,
    STANDALONE_ROUTES,
    groupIsActive,
    pathMatches,
} from '@/clinic/config/navigation';
import NavDropdown from '@/clinic/components/layout/NavDropdown.vue';
import LangSwitch from '@/clinic/components/ui/LangSwitch.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';

const page = usePage();

const currentPath = computed(() => page.url.split('?')[0]);

const user = computed(() => page.props.auth?.user as { name?: string } | null | undefined);

const visibleGroups = computed(() =>
    NAV_GROUPS.filter((group) => {
        if (group.requiresVerified && !user.value) {
            return false;
        }

        if (group.requiresAuth && !user.value) {
            return false;
        }

        return true;
    }),
);

const visibleStandalone = computed(() =>
    STANDALONE_ROUTES.filter(() => Boolean(user.value)),
);

function isStandaloneActive(href: string, match?: string, exact?: boolean): boolean {
    return pathMatches({ href, match, exact, label: { ar: '', en: '' } }, currentPath.value);
}
</script>

<template>
    <nav class="admin-nav-bar" aria-label="Application navigation">
        <div class="admin-nav-inner">
        

            <div class="admin-tabs admin-nav-tabs">
                <NavDropdown
                    v-for="group in visibleGroups"
                    :key="group.id"
                    :label="group.label"
                    :items="group.items"
                    :active="groupIsActive(group, currentPath)"
                />

                <Link
                    v-for="item in visibleStandalone"
                    :key="item.href"
                    :href="item.href"
                    class="admin-tab admin-nav-link"
                    :class="{ active: isStandaloneActive(item.href, item.match, item.exact) }"
                >
                    <LocalizedText :value="item.label" />
                </Link>
            </div>

            <div class="admin-nav-actions">
     

                <template v-if="user">
                    <span class="admin-nav-user">{{ user.name }}</span>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="admin-nav-logout"
                    >
                        <LocalizedText :value="{ ar: 'خروج', en: 'Log out' }" />
                    </Link>
                </template>
                <Link v-else href="/login" class="admin-nav-logout">
                    <LocalizedText :value="{ ar: 'دخول', en: 'Log in' }" />
                </Link>
            </div>
        </div>
    </nav>
</template>
