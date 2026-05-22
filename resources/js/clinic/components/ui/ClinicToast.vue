<script setup lang="ts">
import { useClinicToast } from '@/clinic/composables/useClinicToast';

const { visible, message, type } = useClinicToast();
</script>

<template>
    <div
        class="toast"
        :class="{ show: visible, success: type === 'success', error: type === 'error' }"
    >
        {{ message }}
    </div>
</template>
