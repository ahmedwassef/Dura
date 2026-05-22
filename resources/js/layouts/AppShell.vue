<script setup lang="ts">
import ClinicToast from '@/clinic/components/ui/ClinicToast.vue';

defineProps<{
    class?: string;
}>();
</script>

<template>
    <div class="clinic-app" :class="$props.class">
        <slot />
        <ClinicToast />
    </div>
</template>

<style scoped>
.clinic-app {
    min-height: 100vh;
}
</style>
