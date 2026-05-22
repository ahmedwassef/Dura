<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';
import { Search, UserPlus, X, Calendar, Globe, Phone, FileText } from 'lucide-vue-next';

const props = defineProps<{
    modelValue?: any;
}>();

const emit = defineEmits(['update:modelValue', 'select']);

const { isArabic } = useClinicLocale();

const patientSearch = ref('');
const patientSearchResults = ref<any[]>([]);
const isSearching = ref(false);
const showSearchResults = ref(false);
const selectedPatient = ref<any>(props.modelValue || null);

const nationalities = ref<any[]>([]);

const showAddModal = ref(false);
const newPatient = ref({
    name: '',
    id_number: '',
    phone: '',
    file_number: '',
    sex: 'male',
    date_of_birth: '',
    nationality_id: '' as string | number
});

onMounted(async () => {
    try {
        const response = await axios.get('/dashboard/clinic/nationalities');
        nationalities.value = response.data.nationalities;
    } catch (e) {
        console.error('Failed to load nationalities', e);
    }
});

function calculateAge(dob: string) {
    if (!dob) return null;
    const birthDate = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

const displayAge = computed(() => {
    if (!selectedPatient.value?.date_of_birth) return '—';
    return calculateAge(selectedPatient.value.date_of_birth);
});

const displayNationality = computed(() => {
    if (!selectedPatient.value?.nationality_id || nationalities.value.length === 0) return '—';
    const n = nationalities.value.find(nat => nat.id === selectedPatient.value.nationality_id);
    return n ? (isArabic.value ? n.name_ar : n.name_en) : '—';
});

watch(patientSearch, async (newVal) => {
    if (!newVal || newVal.trim().length < 1) {
        patientSearchResults.value = [];
        return;
    }
    
    isSearching.value = true;
    try {
        const response = await axios.get('/dashboard/clinic/patients/search', {
            params: { q: newVal }
        });
        patientSearchResults.value = response.data.patients;
    } catch (e) {
        console.error('Patient search failed', e);
    } finally {
        isSearching.value = false;
    }
});

function selectPatient(patient: any) {
    selectedPatient.value = patient;
    showSearchResults.value = false;
    patientSearch.value = patient.name;
    emit('update:modelValue', patient);
    emit('select', patient);
}

function clearSelection() {
    selectedPatient.value = null;
    patientSearch.value = '';
    emit('update:modelValue', null);
    emit('select', null);
}

async function createPatient() {
    try {
        const response = await axios.post('/dashboard/clinic/patients', newPatient.value);
        const created = response.data.patient;
        selectPatient(created);
        showAddModal.value = false;
        // Reset form
        newPatient.value = { name: '', id_number: '', phone: '', file_number: '', sex: 'male', date_of_birth: '', nationality_id: '' };
    } catch (e) {
        console.error('Failed to create patient', e);
        alert(isArabic.value ? 'فشل إنشاء المريض' : 'Failed to create patient');
    }
}

watch(() => props.modelValue, (newVal) => {
    selectedPatient.value = newVal;
    if (newVal) patientSearch.value = newVal.name;
});

</script>

<template>
    <div class="patient-selector">
        <div class="selection-grid">
            <!-- Patient Name Search/Dropdown -->
            <div class="field relative search-field">
                <label><LocalizedText :value="{ ar: 'بحث عن مريض', en: 'Search Patient' }" /></label>
                <div class="search-input-wrap">
                    <input 
                        v-model="patientSearch" 
                        @focus="showSearchResults = true" 
                        type="text" 
                        :placeholder="isArabic ? 'ابحث بالاسم، رقم الملف، الهوية...' : 'Search by name, file, ID...'"
                        :readonly="!!selectedPatient"
                        class="search-input"
                    />
                    <div v-if="selectedPatient" class="clear-btn" @click="clearSelection">
                        <X class="size-4" />
                    </div>
                    <Search v-else class="search-icon size-4" />
                </div>

                <!-- Dropdown results -->
                <div v-if="showSearchResults && !selectedPatient" class="results-dropdown">
                    <div v-if="isSearching" class="no-results">
                         <LocalizedText :value="{ ar: 'جاري البحث...', en: 'Searching...' }" />
                    </div>
                    <template v-else-if="patientSearchResults.length > 0">
                        <div 
                            v-for="p in patientSearchResults" 
                            :key="p.id" 
                            @click="selectPatient(p)"
                            class="result-item"
                        >
                            <div class="name">{{ p.name }}</div>
                            <div class="meta">{{ p.file_number }} • {{ p.phone }}</div>
                        </div>
                    </template>
                    <div v-else-if="patientSearch.length > 0" class="no-results">
                        <LocalizedText :value="{ ar: 'لا يوجد نتائج', en: 'No results found' }" />
                    </div>
                    
                    <div class="dropdown-footer" @click="showAddModal = true">
                        <UserPlus class="size-4 mr-2" />
                        <LocalizedText :value="{ ar: 'إضافة مريض جديد', en: 'Add New Patient' }" />
                    </div>
                </div>

                <div v-if="showSearchResults" class="dropdown-overlay" @click="showSearchResults = false"></div>
            </div>
        </div>

        <div class="details-grid" v-if="selectedPatient">
            <div class="detail-card">
                <div class="card-label"><LocalizedText :value="{ ar: 'رقم الملف', en: 'File No.' }" /></div>
                <div class="card-value">
                    <FileText class="size-3.5 mr-1 text-primary" />
                    {{ selectedPatient.file_number || '—' }}
                </div>
            </div>
            <div class="detail-card">
                <div class="card-label"><LocalizedText :value="{ ar: 'الجنسية', en: 'Nationality' }" /></div>
                <div class="card-value">
                    <Globe class="size-3.5 mr-1 text-primary" />
                    {{ displayNationality }}
                </div>
            </div>
            <div class="detail-card">
                <div class="card-label"><LocalizedText :value="{ ar: 'العمر', en: 'Age' }" /></div>
                <div class="card-value">
                    <Calendar class="size-3.5 mr-1 text-primary" />
                    {{ displayAge }} <span class="ml-1 text-xs opacity-60">({{ isArabic ? 'سنة' : 'yrs' }})</span>
                </div>
            </div>
            <div class="detail-card">
                <div class="card-label"><LocalizedText :value="{ ar: 'رقم الجوال', en: 'Phone' }" /></div>
                <div class="card-value">
                    <Phone class="size-3.5 mr-1 text-primary" />
                    {{ selectedPatient.phone || '—' }}
                </div>
            </div>
        </div>

        <!-- Add Patient Modal -->
        <div v-if="showAddModal" class="modal-overlay" @click.self="showAddModal = false">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><LocalizedText :value="{ ar: 'إضافة مريض جديد', en: 'Add New Patient' }" /></h3>
                    <button @click="showAddModal = false" class="close-btn"><X /></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid-modal">
                        <div class="m-field full">
                            <label><LocalizedText :value="{ ar: 'الاسم الكامل', en: 'Full Name' }" /></label>
                            <input v-model="newPatient.name" type="text" required />
                        </div>
                        <div class="m-field">
                            <label><LocalizedText :value="{ ar: 'رقم الهوية', en: 'ID Number' }" /></label>
                            <input v-model="newPatient.id_number" type="text" />
                        </div>
                        <div class="m-field">
                            <label><LocalizedText :value="{ ar: 'رقم الجوال', en: 'Phone' }" /></label>
                            <input v-model="newPatient.phone" type="text" />
                        </div>
                        <div class="m-field">
                            <label><LocalizedText :value="{ ar: 'رقم الملف', en: 'File Number' }" /></label>
                            <input v-model="newPatient.file_number" type="text" />
                        </div>
                        <div class="m-field">
                            <label><LocalizedText :value="{ ar: 'تاريخ الميلاد', en: 'Date of Birth' }" /></label>
                            <input v-model="newPatient.date_of_birth" type="date" />
                        </div>
                        <div class="m-field">
                            <label><LocalizedText :value="{ ar: 'الجنسية', en: 'Nationality' }" /></label>
                            <select v-model="newPatient.nationality_id">
                                <option value=""><LocalizedText :value="{ ar: 'اختر الجنسية', en: 'Select Nationality' }" /></option>
                                <option v-for="n in nationalities" :key="n.id" :value="n.id">
                                    {{ isArabic ? n.name_ar : n.name_en }}
                                </option>
                            </select>
                        </div>
                        <div class="m-field">
                            <label><LocalizedText :value="{ ar: 'الجنس', en: 'Sex' }" /></label>
                            <select v-model="newPatient.sex">
                                <option value="male">{{ isArabic ? 'ذكر' : 'Male' }}</option>
                                <option value="female">{{ isArabic ? 'أنثى' : 'Female' }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="createPatient" class="btn-save">
                        <LocalizedText :value="{ ar: 'حفظ المريض', en: 'Save Patient' }" />
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
.selection-grid {
    margin-bottom: 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

@media (max-width: 768px) {
    .details-grid { grid-template-columns: repeat(2, 1fr); }
}

.detail-card {
    background: var(--bg-soft);
    padding: 12px;
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
    letter-spacing: 0.5px;
}

.card-value {
    font-size: 14px;
    font-weight: 700;
    color: var(--primary);
    display: flex;
    align-items: center;
}

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
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 3px var(--primary-soft);
}

.search-input[readonly] {
    background: var(--bg-soft);
    color: var(--primary);
    font-weight: 700;
    cursor: default;
    border-color: var(--primary-soft);
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

.dropdown-footer {
    padding: 12px;
    background: var(--bg-soft);
    border-top: 1px solid var(--line);
    text-align: center;
    font-weight: 700;
    color: var(--primary);
    cursor: pointer;
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
    max-width: 550px;
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

.modal-body { padding: 20px; }

.form-grid-modal {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.m-field { display: flex; flex-direction: column; gap: 6px; }
.m-field label { font-size: 12px; font-weight: 700; color: var(--ink-mute); }
.m-field input, .m-field select {
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
