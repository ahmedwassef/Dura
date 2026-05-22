<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { Search, UserPlus, X, User } from 'lucide-vue-next';

const props = defineProps<{
    modelValue?: any;
}>();

const emit = defineEmits(['update:modelValue', 'select']);

const { isArabic } = useClinicLocale();

const doctorSearch = ref('');
const doctorSearchResults = ref<any[]>([]);
const isSearching = ref(false);
const showSearchResults = ref(false);
const selectedDoctor = ref<any>(props.modelValue || null);

const showAddModal = ref(false);
const newDoctor = ref({
    name: '',
    specialty: '',
    phone: '',
    license_number: ''
});

watch(doctorSearch, async (newVal) => {
    if (!newVal || newVal.trim().length < 1) {
        doctorSearchResults.value = [];
        return;
    }
    
    isSearching.value = true;
    try {
        const response = await axios.get('/dashboard/clinic/doctors/search', {
            params: { q: newVal }
        });
        doctorSearchResults.value = response.data.doctors;
    } catch (e) {
        console.error('Doctor search failed', e);
    } finally {
        isSearching.value = false;
    }
});

function selectDoctor(doctor: any) {
    selectedDoctor.value = doctor;
    showSearchResults.value = false;
    doctorSearch.value = doctor.name;
    emit('update:modelValue', doctor);
    emit('select', doctor);
}

function clearSelection() {
    selectedDoctor.value = null;
    doctorSearch.value = '';
    emit('update:modelValue', null);
    emit('select', null);
}

async function createDoctor() {
    try {
        const response = await axios.post('/dashboard/clinic/doctors', newDoctor.value);
        const created = response.data.doctor;
        selectDoctor(created);
        showAddModal.value = false;
        // Reset form
        newDoctor.value = { name: '', specialty: '', phone: '', license_number: '' };
    } catch (e) {
        console.error('Failed to create doctor', e);
        alert(isArabic.value ? 'فشل إضافة الطبيب' : 'Failed to create doctor');
    }
}

watch(() => props.modelValue, (newVal) => {
    selectedDoctor.value = newVal;
    if (newVal) doctorSearch.value = newVal.name;
});

</script>

<template>
    <div class="doctor-selector">
        <div class="field relative">
            <label><LocalizedText :value="{ ar: 'اسم الطبيب', en: 'Doctor Name' }" /></label>
            <div class="search-input-wrap">
                <input 
                    v-model="doctorSearch" 
                    @focus="showSearchResults = true" 
                    type="text" 
                    :placeholder="isArabic ? 'ابحث عن طبيب...' : 'Search for doctor...'"
                    :readonly="!!selectedDoctor"
                    class="search-input"
                />
                <div v-if="selectedDoctor" class="clear-btn" @click="clearSelection">
                    <X class="size-4" />
                </div>
                <Search v-else class="search-icon size-4" />
            </div>

            <!-- Dropdown results -->
            <div v-if="showSearchResults && !selectedDoctor" class="results-dropdown">
                <div v-if="isSearching" class="no-results">
                     <LocalizedText :value="{ ar: 'جاري البحث...', en: 'Searching...' }" />
                </div>
                <template v-else-if="doctorSearchResults.length > 0">
                    <div 
                        v-for="d in doctorSearchResults" 
                        :key="d.id" 
                        @click="selectDoctor(d)"
                        class="result-item"
                    >
                        <div class="name">{{ d.name }}</div>
                        <div class="meta">{{ d.specialty || '—' }}</div>
                    </div>
                </template>
                <div v-else-if="doctorSearch.length > 0" class="no-results">
                    <LocalizedText :value="{ ar: 'لا يوجد نتائج', en: 'No results found' }" />
                </div>
                
                <div class="dropdown-footer" @click="showAddModal = true">
                    <UserPlus class="size-4 mr-2" />
                    <LocalizedText :value="{ ar: 'إضافة طبيب جديد', en: 'Add New Doctor' }" />
                </div>
            </div>

            <div v-if="showSearchResults" class="dropdown-overlay" @click="showSearchResults = false"></div>
        </div>

        <div class="doctor-details-grid" v-if="selectedDoctor">
            <div class="doctor-detail-card">
                <div class="card-label"><LocalizedText :value="{ ar: 'التخصص', en: 'Specialty' }" /></div>
                <div class="card-value">
                    <User class="size-3.5 mr-1 text-primary" />
                    {{ selectedDoctor.specialty || '—' }}
                </div>
            </div>
            <div class="doctor-detail-card">
                <div class="card-label"><LocalizedText :value="{ ar: 'رقم الترخيص', en: 'License No.' }" /></div>
                <div class="card-value">
                    <Check class="size-3.5 mr-1 text-primary" />
                    {{ selectedDoctor.license_number || '—' }}
                </div>
            </div>
        </div>

        <!-- Add Doctor Modal -->
        <div v-if="showAddModal" class="modal-overlay" @click.self="showAddModal = false">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><LocalizedText :value="{ ar: 'إضافة طبيب جديد', en: 'Add New Doctor' }" /></h3>
                    <button @click="showAddModal = false" class="close-btn"><X /></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid-modal">
                        <div class="m-field full">
                            <label><LocalizedText :value="{ ar: 'الاسم الكامل', en: 'Full Name' }" /></label>
                            <input v-model="newDoctor.name" type="text" required />
                        </div>
                        <div class="m-field">
                            <label><LocalizedText :value="{ ar: 'التخصص', en: 'Specialty' }" /></label>
                            <input v-model="newDoctor.specialty" type="text" />
                        </div>
                        <div class="m-field">
                            <label><LocalizedText :value="{ ar: 'رقم الجوال', en: 'Phone' }" /></label>
                            <input v-model="newDoctor.phone" type="text" />
                        </div>
                        <div class="m-field full">
                            <label><LocalizedText :value="{ ar: 'رقم الترخيص', en: 'License Number' }" /></label>
                            <input v-model="newDoctor.license_number" type="text" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="createDoctor" class="btn-save">
                        <LocalizedText :value="{ ar: 'حفظ الطبيب', en: 'Save Doctor' }" />
                    </button>
                    <button @click="showAddModal = false" class="btn-cancel">
                        <LocalizedText :value="{ ar: 'إلغاء', en: 'Cancel' }" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
    position: relative;
}

.field label {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink-mute);
}

.search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    width: 100%;
    padding: 10px 12px;
    padding-left: 35px;
    border: 1.5px solid var(--line);
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s;
}

[dir="rtl"] .search-input {
    padding-left: 12px;
    padding-right: 35px;
}

.search-input:focus {
    box-shadow: 0 0 0 3px var(--primary-soft);
}

.doctor-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 15px;
}

.doctor-detail-card {
    background: var(--bg-soft);
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid var(--line);
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.card-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--ink-mute);
    text-transform: uppercase;
}

.card-value {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
    display: flex;
    align-items: center;
}

.search-input[readonly] {
    background: var(--bg-soft);
    color: var(--primary);
    font-weight: 700;
    cursor: default;
}

.search-icon {
    position: absolute;
    left: 12px;
    color: var(--ink-mute);
}

[dir="rtl"] .search-icon {
    left: auto;
    right: 12px;
}

.clear-btn {
    position: absolute;
    left: 8px;
    cursor: pointer;
    color: var(--red);
    padding: 4px;
    border-radius: 4px;
}

[dir="rtl"] .clear-btn {
    left: auto;
    right: 8px;
}

.results-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid var(--line);
    border-radius: 10px;
    margin-top: 5px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    z-index: 100;
    max-height: 300px;
    overflow-y: auto;
}

.result-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid var(--line);
}

.result-item:hover { background: var(--bg-soft); }

.result-item .name { font-weight: 700; color: var(--ink); font-size: 14px; }
.result-item .meta { font-size: 12px; color: var(--ink-mute); margin-top: 2px; }

.no-results { padding: 15px; text-align: center; color: var(--ink-mute); font-size: 13px; }

.dropdown-footer {
    padding: 12px;
    background: var(--bg-soft);
    border-top: 1px solid var(--line);
    text-align: center;
    font-weight: 700;
    font-size: 13px;
    color: var(--primary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropdown-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 90;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1100;
}

.modal-content {
    background: white;
    width: 90%;
    max-width: 500px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid var(--line);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 { margin: 0; font-size: 18px; font-weight: 800; color: var(--primary); }

.modal-body { padding: 20px; }

.form-grid-modal {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.m-field { display: flex; flex-direction: column; gap: 6px; }
.m-field label { font-size: 12px; font-weight: 700; color: var(--ink-mute); }
.m-field input {
    padding: 10px 12px;
    border: 1.5px solid var(--line);
    border-radius: 10px;
    font-size: 14px;
}

.m-field.full { grid-column: span 2; }

.modal-footer {
    padding: 20px;
    background: var(--bg-soft);
    display: flex;
    gap: 12px;
}

.btn-save {
    background: var(--primary);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    flex: 2;
}

.btn-cancel {
    background: white;
    color: var(--ink-mute);
    border: 1px solid var(--line);
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    flex: 1;
}
</style>
