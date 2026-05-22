<script setup lang="ts">
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import type { LocalizedString } from '@/clinic/types/clinic';

const props = defineProps<{
    value: LocalizedString | string;
    tag?: keyof HTMLElementTagNameMap;
}>();

const { t } = useClinicLocale();
</script>

<template>
    <component :is="tag ?? 'span'" v-bind="$attrs">
        {{ t(props.value) }}
    </component>
</template>
