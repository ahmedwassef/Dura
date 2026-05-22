<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useLocalizedDate } from '@/clinic/composables/useLocalizedDate';

const props = defineProps<{
    item: {
        id: string;
        type?: string;
        typeName?: string;
        typeNameEn?: string;
        patientName?: string;
        fileNum?: string;
        idNumber?: string;
        doctor?: string;
        branch?: string;
        branchName?: string;
        date?: string;
        grandTotal?: number;
        signed?: boolean;
        status?: string;
        pdfUrl?: string | null;
    };
}>();

const { isArabic } = useClinicLocale();
const { formatDateTime } = useLocalizedDate();

const typeLabel = computed(() =>
    isArabic.value ? props.item.typeName : props.item.typeNameEn || props.item.typeName,
);

const formattedDate = computed(() =>
    props.item.date ? formatDateTime(props.item.date) : '',
);
</script>

<template>
    <div class="arch-card-wrapper" @click="router.visit(`/dashboard/clinic/submissions/${item.id}/edit`)">
        <div class="arch-card">
        <div class="arch-card-header">
            <div class="arch-card-icon" :class="item.type || 'dental'">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M19 3c-1.5 0-2.7.7-3.5 1.5C14.7 5.3 13.5 6 12 6s-2.7-.7-3.5-1.5C7.7 3.7 6.5 3 5 3 3.3 3 2 4.3 2 6c0 1.5.5 3 1.5 4.5C5 13 6 16 6 18c0 1.7 1.3 3 3 3 .8 0 1.5-.3 2-.9.5.6 1.2.9 2 .9.8 0 1.5-.3 2-.9.5.6 1.2.9 2 .9 1.7 0 3-1.3 3-3 0-2 1-5 2.5-7.5 1-1.5 1.5-3 1.5-4.5 0-1.7-1.3-3-3-3z"
                    />
                </svg>
            </div>
            <span class="arch-card-type">{{ typeLabel || item.type }}</span>
        </div>

        <div>
            <div class="arch-card-name">{{ item.patientName }}</div>
            <div class="arch-card-meta-row">
                <span v-if="item.fileNum" class="arch-card-meta">
                    <b>{{ isArabic ? 'ملف:' : 'File:' }}</b> {{ item.fileNum }}
                </span>
                <span v-if="item.idNumber" class="arch-card-meta">
                    <b>{{ isArabic ? 'هوية:' : 'ID:' }}</b> {{ item.idNumber }}
                </span>
            </div>
            <div class="arch-card-meta-row" style="margin-top: 4px">
                <span v-if="item.doctor" class="arch-card-meta">
                    <b>{{ isArabic ? 'الطبيب:' : 'Dr:' }}</b> {{ item.doctor }}
                </span>
                <span v-if="item.branchName" class="arch-card-meta">
                    <b>{{ item.branchName }}</b>
                </span>
            </div>
            <div class="arch-card-meta-row" style="margin-top: 6px">
                <span class="arch-card-meta">{{ formattedDate }}</span>
                <span
                    v-if="item.status === 'pending_admin'"
                    class="arch-card-meta"
                    style="color: var(--orange)"
                >
                    {{ isArabic ? 'بانتظار الإدارة' : 'Pending admin' }}
                </span>
            </div>
        </div>

        <div class="arch-card-actions">
            <a
                v-if="item.pdfUrl"
                :href="item.pdfUrl"
                target="_blank"
                rel="noopener"
                class="btn btn-sm btn-primary"
            >
                {{ isArabic ? 'عرض PDF' : 'View PDF' }}
            </a>
        </div>
        </div>
    </div>
</template>

<style scoped>
.arch-card-wrapper {
    cursor: pointer;
}
</style>
