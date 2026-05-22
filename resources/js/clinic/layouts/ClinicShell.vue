<script setup lang="ts">
import AppShell from '@/layouts/AppShell.vue';

defineProps<{
    class?: string;
}>();
</script>

<template>
    <AppShell :class="$props.class">
        <slot />
    </AppShell>
</template>
