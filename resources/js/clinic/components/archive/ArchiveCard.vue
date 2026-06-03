<script setup lang="ts">
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { useLocalizedDate } from '@/clinic/composables/useLocalizedDate';
import { useClinicSession } from '@/clinic/composables/useClinicSession';
import { useClinicToast } from '@/clinic/composables/useClinicToast';

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
const { role } = useClinicSession();
const { toast } = useClinicToast();

const showModal = ref(false);
const selectedStatus = ref(props.item.status || 'completed');
const updating = ref(false);

function openStatusModal() {
    selectedStatus.value = props.item.status || 'completed';
    showModal.value = true;
}

function updateStatus() {
    updating.value = true;
    router.put(`/dashboard/clinic/submissions/${props.item.id}/status`, {
        status: selectedStatus.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            updating.value = false;
            showModal.value = false;
            toast(
                isArabic.value
                    ? 'تم تحديث حالة المستند بنجاح'
                    : 'Status updated successfully',
                'success'
            );
        },
        onError: () => {
            updating.value = false;
            toast(
                isArabic.value
                    ? 'فشل في تحديث الحالة'
                    : 'Failed to update status',
                'error'
            );
        }
    });
}

const typeLabel = computed(() =>
    isArabic.value ? props.item.typeName : props.item.typeNameEn || props.item.typeName,
);

const formattedDate = computed(() =>
    props.item.date ? formatDateTime(props.item.date) : '',
);

const statusLabel = computed(() => {
    const status = props.item.status || 'completed';
    if (status === 'pending') {
        return isArabic.value ? 'معلق' : 'Pending';
    }
    if (status === 'pending_admin') {
        return isArabic.value ? 'بانتظار الإدارة' : 'Pending admin';
    }
    if (status === 'pending_discount_review') {
        return isArabic.value ? 'مراجعة الخصم' : 'Discount review';
    }
    return isArabic.value ? 'مكتمل' : 'Completed';
});
</script>

<template>
    <div class="arch-card-wrapper">
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
            <span 
                :class="['status-badge', item.status || 'completed']"
                :style="isArabic ? { marginRight: 'auto' } : { marginLeft: 'auto' }"
            >
                {{ statusLabel }}
            </span>
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
            </div>
        </div>

        <div class="arch-card-actions">
            <button
                type="button"
                class="btn btn-sm btn-primary"
                @click.stop="router.visit(`/dashboard/clinic/submissions/${item.id}`)"
            >
                {{ isArabic ? 'عرض' : 'Show' }}
            </button>
            <button
                v-if="item.status === 'pending_admin' && role === 'admin'"
                type="button"
                class="btn btn-sm btn-approve"
                @click.stop="router.visit(`/dashboard/clinic/submissions/${item.id}/edit`)"
            >
                {{ isArabic ? 'اعتماد وختم' : 'Approve & Stamp' }}
            </button>
            <button
                v-else
                type="button"
                class="btn btn-sm btn-outline"
                @click.stop="router.visit(`/dashboard/clinic/submissions/${item.id}/edit`)"
            >
                {{ isArabic ? 'تعديل' : 'Edit' }}
            </button>
            <button
                type="button"
                class="btn btn-sm btn-outline"
                @click.stop="openStatusModal"
            >
                {{ isArabic ? 'تغيير الحالة' : 'Change Status' }}
            </button>
        </div>
        </div>

        <!-- Status Update Modal -->
        <div v-if="showModal" class="status-modal-overlay" @click.stop="showModal = false">
            <div class="status-modal-card" :dir="isArabic ? 'rtl' : 'ltr'" @click.stop>
                <div class="status-modal-header">
                    <h3>{{ isArabic ? 'تحديث حالة المستند' : 'Update Document Status' }}</h3>
                    <button type="button" class="btn-close" @click="showModal = false">&times;</button>
                </div>
                
                <div class="status-modal-body">
                    <p class="status-modal-desc">
                        {{ isArabic ? 'اختر الحالة الجديدة للمستند الحالي:' : 'Select the new status for this document:' }}
                    </p>

                    <div class="status-options-grid">
                        <!-- Pending / Draft Option -->
                        <label 
                            class="status-option-card" 
                            :class="{ active: selectedStatus === 'pending', pending: true }"
                        >
                            <input 
                                type="radio" 
                                v-model="selectedStatus" 
                                value="pending" 
                                class="hidden-radio"
                            />
                            <div class="status-option-content">
                                <span class="status-dot pending"></span>
                                <span class="status-name">{{ isArabic ? 'معلق / مسودة' : 'Pending / Draft' }}</span>
                            </div>
                            <span v-if="selectedStatus === 'pending'" class="status-check">&#10003;</span>
                        </label>

                        <!-- Pending Admin Option -->
                        <label 
                            class="status-option-card" 
                            :class="{ active: selectedStatus === 'pending_admin', pending_admin: true }"
                        >
                            <input 
                                type="radio" 
                                v-model="selectedStatus" 
                                value="pending_admin" 
                                class="hidden-radio"
                            />
                            <div class="status-option-content">
                                <span class="status-dot pending_admin"></span>
                                <span class="status-name">{{ isArabic ? 'بانتظار الإدارة' : 'Pending Admin' }}</span>
                            </div>
                            <span v-if="selectedStatus === 'pending_admin'" class="status-check">&#10003;</span>
                        </label>

                        <!-- Pending Discount Review Option -->
                        <label 
                            class="status-option-card" 
                            :class="{ active: selectedStatus === 'pending_discount_review', pending_discount_review: true }"
                        >
                            <input 
                                type="radio" 
                                v-model="selectedStatus" 
                                value="pending_discount_review" 
                                class="hidden-radio"
                            />
                            <div class="status-option-content">
                                <span class="status-dot pending_discount_review"></span>
                                <span class="status-name">{{ isArabic ? 'مراجعة الخصم' : 'Discount Review' }}</span>
                            </div>
                            <span v-if="selectedStatus === 'pending_discount_review'" class="status-check">&#10003;</span>
                        </label>

                        <!-- Completed Option -->
                        <label 
                            class="status-option-card" 
                            :class="{ active: selectedStatus === 'completed', completed: true }"
                        >
                            <input 
                                type="radio" 
                                v-model="selectedStatus" 
                                value="completed" 
                                class="hidden-radio"
                            />
                            <div class="status-option-content">
                                <span class="status-dot completed"></span>
                                <span class="status-name">{{ isArabic ? 'مكتمل' : 'Completed' }}</span>
                            </div>
                            <span v-if="selectedStatus === 'completed'" class="status-check">&#10003;</span>
                        </label>
                    </div>
                </div>

                <div class="status-modal-footer">
                    <button 
                        type="button" 
                        class="btn btn-outline" 
                        @click="showModal = false"
                        :disabled="updating"
                    >
                        {{ isArabic ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-primary" 
                        @click="updateStatus"
                        :disabled="updating"
                    >
                        {{ updating ? (isArabic ? 'جاري التحديث...' : 'Updating...') : (isArabic ? 'تحديث الحالة' : 'Update Status') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.arch-card-wrapper {
    cursor: default;
}
.arch-card-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}
.btn-approve {
    background-color: #f59e0b; /* Amber 500 */
    color: white !important;
    border-color: #d97706; /* Amber 600 */
}
.btn-approve:hover {
    background-color: #d97706;
    border-color: #b45309;
}
.status-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 6px;
    text-transform: uppercase;
}
.status-badge.completed {
    background-color: #ecfdf5;
    color: #059669;
}
.status-badge.pending_admin {
    background-color: #fffbeb;
    color: #d97706;
}
.status-badge.pending_discount_review {
    background-color: #fdf2f8;
    color: #db2777;
}
.status-badge.pending {
    background-color: #f3f4f6;
    color: #4b5563;
}

/* Glassmorphic Status Modal Styles */
.status-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(17, 24, 39, 0.4);
    backdrop-filter: blur(8px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    padding: 16px;
}
.status-modal-card {
    background: #ffffff;
    border-radius: 16px;
    width: 100%;
    max-width: 440px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border: 1px solid #f3f4f6;
    overflow: hidden;
    animation: modal-fade-in 0.25s ease-out;
}
@keyframes modal-fade-in {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.status-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
}
.status-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #111827;
}
.btn-close {
    background: transparent;
    border: none;
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
    line-height: 1;
    padding: 4px;
    transition: color 0.15s;
}
.btn-close:hover {
    color: #4b5563;
}
.status-modal-body {
    padding: 20px;
}
.status-modal-desc {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 16px 0;
}
.status-options-grid {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.status-option-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.2s;
}
.status-option-card:hover {
    border-color: #d1d5db;
}
.hidden-radio {
    display: none;
}
.status-option-content {
    display: flex;
    align-items: center;
    gap: 10px;
}
.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.status-dot.pending {
    background-color: #6b7280;
}
.status-dot.pending_admin {
    background-color: #d97706;
}
.status-dot.pending_discount_review {
    background-color: #db2777;
}
.status-dot.completed {
    background-color: #059669;
}
.status-name {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}
.status-check {
    font-weight: bold;
    font-size: 16px;
}

/* Active option styles */
.status-option-card.active.pending {
    border-color: #9ca3af;
    background: #f9fafb;
}
.status-option-card.active.pending .status-check {
    color: #4b5563;
}
.status-option-card.active.pending_admin {
    border-color: #f59e0b;
    background: #fffbeb;
}
.status-option-card.active.pending_admin .status-check {
    color: #d97706;
}
.status-option-card.active.pending_discount_review {
    border-color: #ec4899;
    background: #fdf2f8;
}
.status-option-card.active.pending_discount_review .status-check {
    color: #db2777;
}
.status-option-card.active.completed {
    border-color: #10b981;
    background: #ecfdf5;
}
.status-option-card.active.completed .status-check {
    color: #059669;
}

.status-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding: 16px 20px;
    border-top: 1px solid #f3f4f6;
    background: #f9fafb;
}
</style>
