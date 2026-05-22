<script setup lang="ts">
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import type { LocalizedString } from '@/clinic/types/clinic';

defineProps<{
    title: LocalizedString;
    description: LocalizedString;
    iconClass?: string;
    badge?: LocalizedString;
    primary?: boolean;
    archive?: boolean;
    bilingual?: boolean;
    arOnly?: boolean;
}>();

defineEmits<{
    click: [];
}>();
</script>

<template>
    <div
        class="form-card"
        :class="{
            'primary-card': primary,
            'archive-card': archive,
        }"
        role="button"
        tabindex="0"
        @click="$emit('click')"
        @keyup.enter="$emit('click')"
    >
        <div v-if="$slots.icon" class="form-icon" :class="iconClass">
            <slot name="icon" />
        </div>
        <h4><LocalizedText :value="title" /></h4>
        <p><LocalizedText :value="description" /></p>
        <div v-if="badge || bilingual || arOnly" class="form-card-footer">
            <span v-if="badge" class="form-badge available">
                <LocalizedText :value="badge" />
            </span>
            <span v-if="bilingual" class="lang-badge bilingual">ثنائي اللغة</span>
            <span v-if="arOnly" class="lang-badge ar-only">عربي</span>
        </div>
    </div>
</template>
