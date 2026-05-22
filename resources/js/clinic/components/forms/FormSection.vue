<script setup lang="ts">
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import type { LocalizedString } from '@/clinic/types/clinic';

defineProps<{
    number: string;
    title: LocalizedString | string;
    disabled?: boolean;
}>();
</script>

<template>
    <section class="section" :class="{ 'section-disabled': disabled }">
        <div class="section-title">
            <div class="num">{{ number }}</div>
            <h3><LocalizedText :value="title" /></h3>
        </div>
        <div class="section-content">
            <slot />
        </div>
    </section>
</template>

<style scoped>
.section-disabled {
    opacity: 0.5;
    pointer-events: none;
    user-select: none;
}
</style>
