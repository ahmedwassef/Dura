<script setup lang="ts">
import AppShell from '@/layouts/AppShell.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <AppShell>
        <slot />
    </AppShell>
</template>
