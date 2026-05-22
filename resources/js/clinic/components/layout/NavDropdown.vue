<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';
import { pathMatches, type NavRoute } from '@/clinic/config/navigation';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import type { LocalizedString } from '@/clinic/types/clinic';

defineProps<{
    label: LocalizedString;
    items: NavRoute[];
    active?: boolean;
}>();

const open = ref(false);
const root = ref<HTMLElement | null>(null);
const page = usePage();

const currentPath = computed(() => page.url.split('?')[0]);

function toggle(): void {
    open.value = !open.value;
}

function close(): void {
    open.value = false;
}

function onDocumentClick(event: MouseEvent): void {
    if (!root.value?.contains(event.target as Node)) {
        close();
    }
}

function onEscape(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        close();
    }
}

function isItemActive(item: NavRoute): boolean {
    return pathMatches(item, currentPath.value);
}

watch(open, async (isOpen) => {
    if (isOpen) {
        await nextTick();
        document.addEventListener('click', onDocumentClick);
        document.addEventListener('keydown', onEscape);
    } else {
        document.removeEventListener('click', onDocumentClick);
        document.removeEventListener('keydown', onEscape);
    }
});

onUnmounted(() => {
    document.removeEventListener('click', onDocumentClick);
    document.removeEventListener('keydown', onEscape);
});
</script>

<template>
    <div ref="root" class="admin-nav-dropdown" :class="{ open, active }">
        <button
            type="button"
            class="admin-tab admin-nav-link admin-nav-dropdown-trigger"
            :class="{ active }"
            :aria-expanded="open"
            aria-haspopup="menu"
            @click="toggle"
        >
            <LocalizedText :value="label" />
            <svg class="admin-nav-chevron" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true">
                <path d="M7 10l5 5 5-5z" fill="currentColor" />
            </svg>
        </button>

        <div v-show="open" class="admin-nav-dropdown-menu" role="menu">
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                class="admin-nav-dropdown-item"
                :class="{ active: isItemActive(item) }"
                role="menuitem"
                @click="close"
            >
                <LocalizedText :value="item.label" />
            </Link>
        </div>
    </div>
</template>
