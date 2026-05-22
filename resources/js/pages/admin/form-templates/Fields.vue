<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminPageLayout from '@/clinic/layouts/AdminPageLayout.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { wayfinderFormRoute } from '@/lib/utils';
import { store, reorder } from '@/routes/admin/form-templates/fields';
import { update, destroy } from '@/routes/admin/form-fields';
import type { FormTemplate, FormField } from '@/types/admin';
import { 
    Plus, Trash2, ArrowUp, ArrowDown, Settings, 
    Type, AlignLeft, CheckSquare, List, CircleDot, 
    Calendar, Hash, FileSignature, HelpCircle, Eye
} from 'lucide-vue-next';

const props = defineProps<{
    template: FormTemplate;
    fields: FormField[];
}>();

const { isArabic } = useClinicLocale();
const editingField = ref<FormField | null>(null);

const form = useForm({
    type: 'text' as FormField['type'],
    key: '',
    label_ar: '',
    label_en: '',
    placeholder_ar: '',
    placeholder_en: '',
    content_ar: '',
    content_en: '',
    options: [] as Array<{ value: string; label_ar: string; label_en: string }>,
    settings: {
        width: 'full',
        rows: 3,
        required: false,
    } as Record<string, any>,
    is_required: false,
});

// Options management for select/radio
const newOptionVal = ref('');
const newOptionAr = ref('');
const newOptionEn = ref('');

function addOption() {
    if (!newOptionVal.value.trim()) return;
    form.options.push({
        value: newOptionVal.value.trim(),
        label_ar: newOptionAr.value.trim() || newOptionVal.value.trim(),
        label_en: newOptionEn.value.trim() || newOptionVal.value.trim(),
    });
    newOptionVal.value = '';
    newOptionAr.value = '';
    newOptionEn.value = '';
}

function removeOption(index: number) {
    form.options.splice(index, 1);
}

function selectFieldForEdit(field: FormField | null) {
    editingField.value = field;
    if (field) {
        form.type = field.type;
        form.key = field.key ?? '';
        form.label_ar = field.label_ar ?? '';
        form.label_en = field.label_en ?? '';
        form.placeholder_ar = field.placeholder_ar ?? '';
        form.placeholder_en = field.placeholder_en ?? '';
        form.content_ar = field.content_ar ?? '';
        form.content_en = field.content_en ?? '';
        form.options = Array.isArray(field.options) ? [...field.options] : [];
        form.settings = field.settings ? { ...field.settings } : { width: 'full', rows: 3 };
        form.is_required = !!field.is_required;
    } else {
        form.reset();
        form.type = 'text';
        form.options = [];
        form.settings = { width: 'full', rows: 3 };
        form.is_required = false;
    }
}

function submit() {
    if (editingField.value) {
        // Update field
        const routeObj = update.form(editingField.value.id);
        const { method, url } = wayfinderFormRoute(routeObj);
        form.submit(method as any, url, {
            onSuccess: () => {
                selectFieldForEdit(null);
            }
        });
    } else {
        // Create field
        const routeObj = store.form(props.template.id);
        const { method, url } = wayfinderFormRoute(routeObj);
        form.submit(method as any, url, {
            onSuccess: () => {
                selectFieldForEdit(null);
            }
        });
    }
}

function deleteField(field: FormField) {
    if (confirm(isArabic.value ? 'هل أنت متأكد من حذف هذا الحقل؟' : 'Are you sure you want to delete this field?')) {
        const routeObj = destroy(field.id);
        router.delete(routeObj.url, {
            onSuccess: () => {
                if (editingField.value?.id === field.id) {
                    selectFieldForEdit(null);
                }
            }
        });
    }
}

function moveField(field: FormField, direction: 'up' | 'down') {
    const currentIndex = props.fields.findIndex(f => f.id === field.id);
    if (currentIndex === -1) return;
    
    const targetIndex = direction === 'up' ? currentIndex - 1 : currentIndex + 1;
    if (targetIndex < 0 || targetIndex >= props.fields.length) return;

    const reorderedFields = [...props.fields];
    // Swap
    const temp = reorderedFields[currentIndex];
    reorderedFields[currentIndex] = reorderedFields[targetIndex];
    reorderedFields[targetIndex] = temp;

    // Send reorder payload
    const payload = reorderedFields.map((f, idx) => ({
        id: f.id,
        order: (idx + 1) * 10
    }));

    router.post(reorder(props.template.id).url, {
        fields: payload
    });
}

const typeIcons = {
    section: AlignLeft,
    text: Type,
    textarea: AlignLeft,
    number: Hash,
    date: Calendar,
    select: List,
    radio: CircleDot,
    checkbox: CheckSquare,
    consent: HelpCircle,
    signature: FileSignature
};

function getTypeIcon(type: FormField['type']) {
    return typeIcons[type] || HelpCircle;
}

function getTypeLabel(type: FormField['type']) {
    const labels = {
        section: { ar: 'عنوان قسم', en: 'Section Header' },
        text: { ar: 'نص سطر واحد', en: 'Text (Single Line)' },
        textarea: { ar: 'نص متعدد الأسطر', en: 'Textarea (Multi-line)' },
        number: { ar: 'رقم', en: 'Number' },
        date: { ar: 'التاريخ', en: 'Date Picker' },
        select: { ar: 'قائمة منسدلة', en: 'Dropdown Select' },
        radio: { ar: 'خيارات متعددة (Radio)', en: 'Radio Buttons' },
        checkbox: { ar: 'مربع اختيار', en: 'Checkbox' },
        consent: { ar: 'نص إقرار / موافقة', en: 'Consent Text Block' },
        signature: { ar: 'توقيع إلكتروني', en: 'Signature Pad' },
    };
    return labels[type] || { ar: type, en: type };
}
</script>

<template>
    <Head :title="isArabic ? 'بناء النموذج الطبية' : 'Build Medical Form'" />

    <AdminPageLayout
        :form-name="{ ar: `بناء النموذج: ${template.name_ar}`, en: `Build Form: ${template.name_en}` }"
        show-back
        :back-href="`/dashboard/form-templates`"
    >
        <div class="admin-page-head">
            <div>
                <h2>
                    <LocalizedText :value="{ ar: 'مصمم النماذج الطبية', en: 'Medical Form Designer' }" />
                </h2>
                <p class="subtitle">
                    {{ isArabic ? 'قم بتعريف وتصميم الحقول والتوقيعات والإقرارات الخاصة بالنموذج' : 'Define and layout the fields, signatures and consent blocks for this form template' }}
                </p>
                <div class="template-badge">
                    <span class="code-pill">{{ template.code }}</span>
                    <span class="dept-tag">{{ isArabic ? template.department?.name_ar : template.department?.name_en }}</span>
                </div>
            </div>
            <div style="margin-top: 10px;">
                <a
                    :href="`/dashboard/clinic/forms/${template.code}`"
                    target="_blank"
                    class="btn btn-outline"
                    style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--accent); border-color: var(--accent); background: rgba(13, 148, 136, 0.05); text-decoration: none; padding: 10px 16px; border-radius: 8px;"
                >
                    <Eye class="size-4" />
                    {{ isArabic ? 'تعبئة ومعاينة النموذج 🔗' : 'Fill & Preview Form 🔗' }}
                </a>
            </div>
        </div>

        <div class="builder-layout">
            <!-- Left Panel: Fields List & Preview -->
            <div class="builder-main">
                <div class="card card-left">
                    <div class="card-header-row">
                        <h3>
                            <LocalizedText :value="{ ar: 'حقول النموذج الحالية', en: 'Current Form Fields' }" />
                            <span class="count-badge">{{ fields.length }}</span>
                        </h3>
                    </div>

                    <div class="fields-list">
                        <div v-if="fields.length === 0" class="empty-state">
                            <Settings class="empty-icon animate-pulse" />
                            <p>{{ isArabic ? 'لا توجد حقول في هذا النموذج بعد. ابدأ بإضافة حقل من اللوحة الجانبية.' : 'No fields added to this form yet. Start by adding a field from the side panel.' }}</p>
                        </div>

                        <div 
                            v-for="(field, idx) in fields" 
                            :key="field.id" 
                            class="field-item-row"
                            :class="{ 'editing-now': editingField?.id === field.id, 'section-row': field.type === 'section' }"
                        >
                            <div class="drag-handle">
                                <component :is="getTypeIcon(field.type)" class="type-icon-lg" />
                            </div>
                            
                            <div class="field-details">
                                <div class="field-header">
                                    <span class="type-badge">{{ isArabic ? getTypeLabel(field.type).ar : getTypeLabel(field.type).en }}</span>
                                    <span v-if="field.is_required" class="required-star">*</span>
                                    <code v-if="field.key" class="key-pill">{{ field.key }}</code>
                                </div>
                                <div class="field-title-labels">
                                    <div class="ar-title"><strong>AR:</strong> {{ field.label_ar || field.content_ar || '—' }}</div>
                                    <div class="en-title"><strong>EN:</strong> {{ field.label_en || field.content_en || '—' }}</div>
                                </div>
                            </div>

                            <div class="field-actions">
                                <button 
                                    type="button" 
                                    class="order-btn" 
                                    :disabled="idx === 0"
                                    @click="moveField(field, 'up')"
                                >
                                    <ArrowUp class="size-4" />
                                </button>
                                <button 
                                    type="button" 
                                    class="order-btn" 
                                    :disabled="idx === fields.length - 1"
                                    @click="moveField(field, 'down')"
                                >
                                    <ArrowDown class="size-4" />
                                </button>
                                <button 
                                    type="button" 
                                    class="edit-btn" 
                                    @click="selectFieldForEdit(field)"
                                >
                                    {{ isArabic ? 'تعديل' : 'Edit' }}
                                </button>
                                <button 
                                    type="button" 
                                    class="delete-btn" 
                                    @click="deleteField(field)"
                                >
                                    <Trash2 class="size-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Field Form Editor -->
            <div class="builder-side">
                <div class="card card-side">
                    <div class="card-header-row">
                        <h3>
                            {{ editingField ? (isArabic ? 'تعديل الحقل' : 'Edit Field') : (isArabic ? 'إضافة حقل جديد' : 'Add New Field') }}
                        </h3>
                        <button v-if="editingField" type="button" class="btn-clear" @click="selectFieldForEdit(null)">
                            {{ isArabic ? 'جديد' : 'New' }}
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="admin-form-stack">
                        <!-- Field Type -->
                        <div class="field">
                            <label for="field-type">{{ isArabic ? 'نوع الحقل' : 'Field Type' }}</label>
                            <select id="field-type" v-model="form.type" :disabled="!!editingField" required>
                                <option value="section">{{ isArabic ? 'عنوان قسم' : 'Section Header' }}</option>
                                <option value="text">{{ isArabic ? 'نص سطر واحد' : 'Text (Single Line)' }}</option>
                                <option value="textarea">{{ isArabic ? 'نص متعدد الأسطر' : 'Textarea (Multi-line)' }}</option>
                                <option value="number">{{ isArabic ? 'رقم' : 'Number' }}</option>
                                <option value="date">{{ isArabic ? 'التاريخ' : 'Date Picker' }}</option>
                                <option value="select">{{ isArabic ? 'قائمة منسدلة' : 'Dropdown Select' }}</option>
                                <option value="radio">{{ isArabic ? 'خيارات متعددة (Radio)' : 'Radio Buttons' }}</option>
                                <option value="checkbox">{{ isArabic ? 'مربع اختيار' : 'Checkbox' }}</option>
                                <option value="consent">{{ isArabic ? 'نص إقرار / موافقة' : 'Consent Text Block' }}</option>
                                <option value="signature">{{ isArabic ? 'توقيع إلكتروني' : 'Signature Pad' }}</option>
                            </select>
                        </div>

                        <!-- Data Key (Payload Key) -->
                        <div v-if="form.type !== 'section' && form.type !== 'consent'" class="field">
                            <label for="field-key">
                                {{ isArabic ? 'رمز الحقل (Key in database)' : 'Data Key (Unique Name)' }}
                                <span class="help-label">{{ isArabic ? 'مثال: heart_disease' : 'e.g. blood_pressure' }}</span>
                            </label>
                            <input 
                                id="field-key" 
                                v-model="form.key" 
                                required 
                                placeholder="key_name" 
                                dir="ltr"
                            />
                        </div>

                        <!-- Labels for Input Fields / Sections -->
                        <div v-if="form.type !== 'consent'" class="label-group">
                            <div class="field">
                                <label for="label-en">{{ isArabic ? 'العنوان بالإنجليزية (EN Label)' : 'Label (EN)' }}</label>
                                <input id="label-en" v-model="form.label_en" required dir="ltr" />
                            </div>
                            <div class="field">
                                <label for="label-ar">{{ isArabic ? 'العنوان بالعربية (AR Label)' : 'Label (AR)' }}</label>
                                <input id="label-ar" v-model="form.label_ar" required dir="rtl" />
                            </div>
                        </div>

                        <!-- Placeholders (only text / number / date / select) -->
                        <div v-if="['text', 'textarea', 'number', 'date', 'select'].includes(form.type)" class="label-group">
                            <div class="field">
                                <label for="placeholder-en">{{ isArabic ? 'نص تلميح EN (Placeholder)' : 'Placeholder (EN)' }}</label>
                                <input id="placeholder-en" v-model="form.placeholder_en" dir="ltr" />
                            </div>
                            <div class="field">
                                <label for="placeholder-ar">{{ isArabic ? 'نص تلميح AR (Placeholder)' : 'Placeholder (AR)' }}</label>
                                <input id="placeholder-ar" v-model="form.placeholder_ar" dir="rtl" />
                            </div>
                        </div>

                        <!-- Content Text blocks for consent & section bodies -->
                        <div v-if="['consent', 'section'].includes(form.type)" class="label-group">
                            <div class="field">
                                <label for="content-en">{{ isArabic ? 'نص المحتوى بالإنجليزية' : 'Content Text (EN)' }}</label>
                                <textarea id="content-en" v-model="form.content_en" rows="4" dir="ltr"></textarea>
                            </div>
                            <div class="field">
                                <label for="content-ar">{{ isArabic ? 'نص المحتوى بالعربية' : 'Content Text (AR)' }}</label>
                                <textarea id="content-ar" v-model="form.content_ar" rows="4" dir="rtl"></textarea>
                            </div>
                        </div>

                        <!-- Select/Radio options editor -->
                        <div v-if="['select', 'radio'].includes(form.type)" class="options-manager">
                            <h4 class="sub-title">{{ isArabic ? 'إدارة خيارات القائمة' : 'Options Manager' }}</h4>
                            <div class="options-list-preview">
                                <div v-if="form.options.length === 0" class="no-options">
                                    {{ isArabic ? 'لا توجد خيارات بعد.' : 'No options added yet.' }}
                                </div>
                                <div v-for="(opt, oIdx) in form.options" :key="oIdx" class="option-pill-row">
                                    <div class="opt-labels">
                                        <code>{{ opt.value }}</code>
                                        <span>AR: {{ opt.label_ar }}</span>
                                        <span>EN: {{ opt.label_en }}</span>
                                    </div>
                                    <button type="button" class="btn-icon-trash" @click="removeOption(oIdx)">
                                        <Trash2 class="size-3.5" />
                                    </button>
                                </div>
                            </div>
                            
                            <div class="add-option-grid">
                                <input v-model="newOptionVal" placeholder="value (e.g. yes)" dir="ltr" class="opt-val-input" />
                                <input v-model="newOptionAr" placeholder="اسم الخيار AR" dir="rtl" />
                                <input v-model="newOptionEn" placeholder="Option Label EN" dir="ltr" />
                                <button type="button" class="btn btn-outline btn-add-opt" @click="addOption">
                                    <Plus class="size-4 mr-1" /> {{ isArabic ? 'إضافة' : 'Add' }}
                                </button>
                            </div>
                        </div>

                        <!-- Layout & Width settings -->
                        <div v-if="form.type !== 'section' && form.type !== 'consent'" class="layout-settings">
                            <h4 class="sub-title">{{ isArabic ? 'إعدادات الحقل' : 'Field Settings' }}</h4>
                            <div class="field-settings-grid">
                                <div class="field">
                                    <label>{{ isArabic ? 'العرض' : 'Width' }}</label>
                                    <select v-model="form.settings.width">
                                        <option value="full">{{ isArabic ? 'كامل العرض' : 'Full Width' }}</option>
                                        <option value="half">{{ isArabic ? 'نصف العرض' : 'Half Width' }}</option>
                                        <option value="third">{{ isArabic ? 'ثلث العرض' : 'Third Width' }}</option>
                                    </select>
                                </div>

                                <div v-if="form.type === 'textarea'" class="field">
                                    <label>{{ isArabic ? 'عدد الأسطر' : 'Rows' }}</label>
                                    <input v-model.number="form.settings.rows" type="number" min="2" max="10" />
                                </div>
                            </div>
                        </div>

                        <!-- Is Required Checkbox -->
                        <div v-if="!['section', 'consent', 'signature'].includes(form.type)" class="field">
                            <label class="toggle-label">
                                <span>{{ isArabic ? 'حقل إلزامي (Required)' : 'Is Required' }}</span>
                                <div class="toggle-wrap">
                                    <input id="field-required" v-model="form.is_required" type="checkbox" class="toggle-input" />
                                    <label for="field-required" class="toggle-track"></label>
                                </div>
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="admin-form-actions">
                            <button 
                                v-if="editingField" 
                                type="button" 
                                class="btn btn-outline" 
                                @click="selectFieldForEdit(null)"
                            >
                                {{ isArabic ? 'إلغاء' : 'Cancel' }}
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                {{ editingField ? (isArabic ? 'تحديث' : 'Update') : (isArabic ? 'إضافة للنموذج' : 'Add to Form') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminPageLayout>
</template>

<style scoped>
.admin-page-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    border-bottom: 1.5px solid var(--line);
    padding-bottom: 16px;
}
.admin-page-head h2 { font-size: 22px; font-weight: 800; color: var(--primary); margin: 0 0 6px; }
.admin-page-head .subtitle { color: var(--ink-mute); font-size: 13px; margin: 0 0 12px; }

.template-badge {
    display: flex;
    gap: 8px;
    align-items: center;
}
.code-pill {
    background: var(--bg-soft);
    color: var(--primary);
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    border: 1px solid var(--line);
}
.dept-tag {
    background: rgba(30, 58, 95, 0.08);
    color: var(--primary);
    padding: 3px 10px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
}

.builder-layout {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    align-items: flex-start;
}
.builder-main { flex: 2; min-width: 320px; }
.builder-side { flex: 1.2; min-width: 290px; position: sticky; top: 100px; }

.card {
    background: white;
    border-radius: 1.25rem;
    border: 1.5px solid var(--line);
    padding: 20px;
}
.card-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
    border-bottom: 1px solid var(--line);
    padding-bottom: 12px;
}
.card-header-row h3 {
    font-size: 17px;
    color: var(--primary);
    margin: 0;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 10px;
}
.count-badge {
    background: var(--primary);
    color: white;
    font-size: 12px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
}

.btn-clear {
    background: transparent;
    border: none;
    color: var(--accent);
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
}

.fields-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.empty-state {
    text-align: center;
    padding: 48px 16px;
    color: var(--ink-mute);
}
.empty-icon {
    width: 48px;
    height: 48px;
    color: var(--primary-soft);
    margin: 0 auto 12px;
    opacity: 0.7;
}

.field-item-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 16px;
    border: 1.5px solid var(--line);
    border-radius: 12px;
    background: var(--bg-card);
    transition: all 0.2s ease;
}
.field-item-row:hover {
    border-color: var(--primary-soft);
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
}
.field-item-row.editing-now {
    border-color: var(--accent);
    background: rgba(13, 148, 136, 0.04);
}
.field-item-row.section-row {
    background: var(--bg-soft);
    border-left: 4px solid var(--primary);
}

.drag-handle {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ink-mute);
    cursor: grab;
}
.type-icon-lg {
    width: 20px;
    height: 20px;
    color: var(--primary);
}

.field-details {
    flex: 1;
}
.field-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}
.type-badge {
    background: var(--bg-soft);
    color: var(--ink-soft);
    font-size: 11px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
}
.required-star {
    color: var(--red);
    font-weight: 700;
}
.key-pill {
    font-size: 11px;
    background: rgba(13, 148, 136, 0.08);
    color: var(--accent);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
}

.field-title-labels {
    font-size: 13px;
    color: var(--ink);
}
.ar-title { text-align: right; margin-bottom: 2px; }
.en-title { text-align: left; color: var(--ink-soft); }

.field-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}
.order-btn, .edit-btn, .delete-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    border-radius: 8px;
    border: 1px solid var(--line);
    background: white;
    cursor: pointer;
    transition: all 0.15s;
    color: var(--ink-soft);
}
.order-btn:hover:not(:disabled) {
    background: var(--bg-soft);
    color: var(--primary);
}
.order-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}
.edit-btn {
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
}
.edit-btn:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: rgba(13, 148, 136, 0.04);
}
.delete-btn:hover {
    border-color: var(--red);
    color: var(--red);
    background: rgba(239, 68, 68, 0.04);
}

.admin-form-stack { display: flex; flex-direction: column; gap: 14px; }
.label-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
@media (max-width: 768px) {
    .label-group { grid-template-columns: 1fr; }
}

.help-label {
    font-size: 11px;
    color: var(--ink-mute);
    font-weight: normal;
    display: block;
    margin-top: 2px;
}

.sub-title {
    font-size: 13px;
    font-weight: 800;
    color: var(--primary);
    margin: 8px 0;
    border-bottom: 1.5px solid var(--line);
    padding-bottom: 4px;
}

/* Options Manager */
.options-manager {
    background: var(--bg-soft);
    border-radius: 12px;
    padding: 12px;
    border: 1px dashed var(--line);
}
.options-list-preview {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 10px;
    max-height: 180px;
    overflow-y: auto;
}
.no-options {
    text-align: center;
    font-size: 12px;
    color: var(--ink-mute);
    padding: 10px 0;
}
.option-pill-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid var(--line);
}
.opt-labels {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 11px;
}
.opt-labels code {
    background: var(--bg-soft);
    padding: 1px 4px;
    border-radius: 3px;
    font-weight: 700;
}
.btn-icon-trash {
    background: transparent;
    border: none;
    color: var(--red);
    cursor: pointer;
}

.add-option-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 6px;
}
.opt-val-input { grid-column: span 3; }
.btn-add-opt {
    grid-column: span 3;
    padding: 6px;
    font-size: 12px;
}

/* Field layout settings */
.field-settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

/* Toggle Switch */
.toggle-label { display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--ink-soft); }
.toggle-wrap { position: relative; display: flex; align-items: center; }
.toggle-input { opacity: 0; width: 0; height: 0; position: absolute; }
.toggle-track { display: block; width: 44px; height: 24px; background: var(--line); border-radius: 12px; cursor: pointer; transition: background 0.2s; position: relative; }
.toggle-track::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: white; border-radius: 50%; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
.toggle-input:checked + .toggle-track { background: var(--green); }
.toggle-input:checked + .toggle-track::after { transform: translateX(20px); }

.admin-form-actions {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    margin-top: 12px;
}
</style>
