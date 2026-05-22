<script setup lang="ts">
import ClinicAuthLayout from '@/layouts/auth/ClinicAuthLayout.vue';

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <ClinicAuthLayout :title="title" :description="description">
        <slot />
    </ClinicAuthLayout>
</template>
