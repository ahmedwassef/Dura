<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import FormSection from '@/clinic/components/forms/FormSection.vue';
import PatientSelector from '@/clinic/components/forms/PatientSelector.vue';
import DoctorSelector from '@/clinic/components/forms/DoctorSelector.vue';
import SignaturePad from '@/clinic/components/forms/SignaturePad.vue';
import LocalizedText from '@/clinic/components/ui/LocalizedText.vue';
import { useClinicLocale } from '@/clinic/composables/useClinicLocale';

const { isArabic } = useClinicLocale();

const props = defineProps<{
    initialData?: any;
}>();

const serviceSearchResults = ref<any[]>([]);
const isSearching = ref(false);
const serviceSearch = ref('');
const showSearchResults = ref(false);

watch(serviceSearch, async (newVal) => {
    if (!newVal || newVal.trim().length < 1) {
        serviceSearchResults.value = [];
        return;
    }
    
    isSearching.value = true;
    try {
        const response = await axios.get('/dashboard/clinic/services/search', {
            params: { q: newVal }
        });
        serviceSearchResults.value = response.data.services.map((s: any) => ({
            code: s.code,
            ar: s.name_ar,
            en: s.name_en,
            price: s.price,
            category: s.category
        }));
    } catch (e) {
        console.error('Search failed', e);
    } finally {
        isSearching.value = false;
    }
});

// Constants
const ADULT_UPPER = [18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28];
const ADULT_LOWER = [48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38];
const CHILD_UPPER = [55,54,53,52,51, 61,62,63,64,65];
const CHILD_LOWER = [85,84,83,82,81, 71,72,73,74,75];







function getToothType(num, isChild = false) {
  const last = num % 10;
  if (isChild) {
    if (last === 1 || last === 2) return 'incisor';
    if (last === 3) return 'canine';
    if (last === 4 || last === 5) return 'molar-small';
    return 'incisor';
  }
  if (last === 1 || last === 2) return 'incisor';
  if (last === 3) return 'canine';
  if (last === 4 || last === 5) return 'premolar';
  return 'molar';
}

function buildToothSVG(num, isUpper, isChild = false) {
  const type = getToothType(num, isChild);
  const w = 42, h = 70;

  let svgInner = '';

  if (type === 'incisor') {
    // قاطع: تاج عريض من الأعلى مفلطح، حافة قص حادة، جذر طويل واحد
    svgInner = `
      <!-- الجذر -->
      <path class="root"
        d="M 14 36 Q 11 44 12 56 Q 13 64 17 67 Q 21 68 25 67 Q 29 64 30 56 Q 31 44 28 36 Z"
        fill="url(#rootGrad)" stroke="#a6855a" stroke-width=".6"/>
      <!-- ظل الجذر -->
      <path d="M 16 42 Q 14 50 16 60" stroke="#8a6d44" stroke-width=".5" fill="none" opacity=".4"/>

      <!-- التاج (شكل قاطع مفلطح) -->
      <path class="crown"
        d="M 8 28
           Q 7 18 9 14
           Q 12 8 16 7
           Q 21 6 26 7
           Q 30 8 33 14
           Q 35 18 34 28
           Q 33 35 28 38
           L 14 38
           Q 9 35 8 28 Z"
        fill="#fafaf5" stroke="#1a1a1a" stroke-width="1.1" stroke-linejoin="round"/>

      <!-- خطوط الحافة القاصة (mamelons) -->
      <path d="M 13 12 L 14 8 M 18 9 L 18 7 M 24 9 L 24 7 M 29 12 L 28 8"
        stroke="#c8c5b8" stroke-width=".7" fill="none" opacity=".5"/>

      <!-- لمعان -->
      <ellipse cx="14" cy="20" rx="2.5" ry="6" fill="rgba(255,255,255,.6)" transform="rotate(-15 14 20)"/>
      <ellipse cx="22" cy="18" rx="1.5" ry="4" fill="rgba(255,255,255,.4)"/>
    `;
  } else if (type === 'canine') {
    // ناب: تاج بطرف مدبب (cusp tip)، جذر طويل قوي
    svgInner = `
      <!-- الجذر (الأطول) -->
      <path class="root"
        d="M 14 36 Q 10 46 11 60 Q 12 67 17 68 Q 21 69 25 68 Q 30 67 31 60 Q 32 46 28 36 Z"
        fill="url(#rootGrad)" stroke="#a6855a" stroke-width=".6"/>
      <path d="M 16 44 Q 14 54 16 64" stroke="#8a6d44" stroke-width=".5" fill="none" opacity=".4"/>

      <!-- التاج بطرف مدبب -->
      <path class="crown"
        d="M 8 28
           Q 7 20 10 15
           Q 14 9 18 6
           L 21 4
           L 24 6
           Q 28 9 32 15
           Q 35 20 34 28
           Q 33 35 28 38
           L 14 38
           Q 9 35 8 28 Z"
        fill="#fafaf5" stroke="#1a1a1a" stroke-width="1.1" stroke-linejoin="round"/>

      <!-- ridge وسط الناب -->
      <path d="M 21 7 L 21 30" stroke="#c8c5b8" stroke-width=".8" fill="none" opacity=".6"/>

      <!-- لمعان -->
      <ellipse cx="14" cy="22" rx="2.5" ry="7" fill="rgba(255,255,255,.55)" transform="rotate(-18 14 22)"/>
      <ellipse cx="26" cy="18" rx="1.5" ry="5" fill="rgba(255,255,255,.35)" transform="rotate(15 26 18)"/>
    `;
  } else if (type === 'premolar') {
    // ضاحك: نتوءان (buccal + lingual cusps)، جذر واحد أو اثنين
    svgInner = `
      <!-- الجذر -->
      <path class="root"
        d="M 13 36 Q 10 46 11 58 Q 12 65 16 67 Q 21 68 26 67 Q 30 65 31 58 Q 32 46 29 36 Z"
        fill="url(#rootGrad)" stroke="#a6855a" stroke-width=".6"/>
      <path d="M 21 38 L 21 65" stroke="#a6855a" stroke-width=".4" fill="none" opacity=".5"/>

      <!-- التاج بنتوءين -->
      <path class="crown"
        d="M 6 28
           Q 5 20 8 15
           Q 11 10 14 9
           Q 17 8 18 11
           L 19 14
           Q 21 12 21 12
           Q 21 12 23 14
           L 24 11
           Q 25 8 28 9
           Q 31 10 34 15
           Q 37 20 36 28
           Q 35 36 30 38
           L 12 38
           Q 7 36 6 28 Z"
        fill="#fafaf5" stroke="#1a1a1a" stroke-width="1.1" stroke-linejoin="round"/>

      <!-- Cusps: نتوءات أعلى التاج -->
      <circle cx="13" cy="13" r="1.8" fill="#e8e5d8" opacity=".7"/>
      <circle cx="29" cy="13" r="1.8" fill="#e8e5d8" opacity=".7"/>

      <!-- central groove -->
      <path d="M 14 16 Q 21 22 28 16" stroke="#c8c5b8" stroke-width=".7" fill="none" opacity=".5"/>
      <path d="M 21 18 L 21 30" stroke="#d0cdbf" stroke-width=".5" fill="none" opacity=".4"/>

      <!-- لمعان -->
      <ellipse cx="11" cy="22" rx="2.5" ry="6" fill="rgba(255,255,255,.5)" transform="rotate(-15 11 22)"/>
    `;
  } else if (type === 'molar') {
    // ضرس: 4 نتوءات (cusps)، شكل عريض، جذرين منفصلين
    svgInner = `
      <!-- الجذور المنفصلة -->
      <path class="root"
        d="M 12 36 Q 8 44 9 56 Q 10 64 13 66 Q 16 67 19 65 Q 21 60 21 50 L 21 38 Z"
        fill="url(#rootGrad)" stroke="#a6855a" stroke-width=".6"/>
      <path class="root"
        d="M 30 36 Q 34 44 33 56 Q 32 64 29 66 Q 26 67 23 65 Q 21 60 21 50 L 21 38 Z"
        fill="url(#rootGrad)" stroke="#a6855a" stroke-width=".6"/>
      <path d="M 14 44 Q 12 54 14 62" stroke="#8a6d44" stroke-width=".5" fill="none" opacity=".4"/>
      <path d="M 28 44 Q 30 54 28 62" stroke="#8a6d44" stroke-width=".5" fill="none" opacity=".4"/>

      <!-- التاج العريض -->
      <path class="crown"
        d="M 4 28
           Q 3 18 6 13
           Q 9 8 12 8
           Q 14 8 15 11
           L 16 14
           Q 18 11 21 11
           Q 24 11 26 14
           L 27 11
           Q 28 8 30 8
           Q 33 8 36 13
           Q 39 18 38 28
           Q 37 36 32 38
           L 10 38
           Q 5 36 4 28 Z"
        fill="#fafaf5" stroke="#1a1a1a" stroke-width="1.1" stroke-linejoin="round"/>

      <!-- 4 نتوءات (cusps) -->
      <circle cx="11" cy="14" r="1.8" fill="#e8e5d8" opacity=".7"/>
      <circle cx="17" cy="14" r="1.6" fill="#e8e5d8" opacity=".6"/>
      <circle cx="25" cy="14" r="1.6" fill="#e8e5d8" opacity=".6"/>
      <circle cx="31" cy="14" r="1.8" fill="#e8e5d8" opacity=".7"/>

      <!-- النقوش السطحية (occlusal grooves) -->
      <path d="M 11 18 L 21 22 L 31 18" stroke="#c8c5b8" stroke-width=".6" fill="none" opacity=".55"/>
      <path d="M 21 22 L 21 32" stroke="#c8c5b8" stroke-width=".5" fill="none" opacity=".5"/>
      <path d="M 14 26 L 28 26" stroke="#d0cdbf" stroke-width=".4" fill="none" opacity=".4"/>

      <!-- لمعان -->
      <ellipse cx="9" cy="22" rx="2.5" ry="6" fill="rgba(255,255,255,.5)" transform="rotate(-15 9 22)"/>
      <ellipse cx="33" cy="22" rx="2" ry="5" fill="rgba(255,255,255,.35)" transform="rotate(15 33 22)"/>
    `;
  } else if (type === 'molar-small') {
    // ضرس أطفال: أصغر، جذور متباعدة
    svgInner = `
      <!-- جذور متباعدة (للأسنان اللبنية) -->
      <path class="root"
        d="M 12 36 Q 8 44 9 56 Q 10 62 13 64 Q 16 64 18 62 Q 19 58 19 50 L 19 38 Z"
        fill="url(#rootGrad)" stroke="#a6855a" stroke-width=".6"/>
      <path class="root"
        d="M 30 36 Q 34 44 33 56 Q 32 62 29 64 Q 26 64 24 62 Q 23 58 23 50 L 23 38 Z"
        fill="url(#rootGrad)" stroke="#a6855a" stroke-width=".6"/>

      <!-- التاج -->
      <path class="crown"
        d="M 6 28
           Q 5 18 8 13
           Q 11 8 14 9
           Q 17 11 18 13
           Q 21 11 24 13
           Q 25 11 28 9
           Q 31 8 34 13
           Q 37 18 36 28
           Q 35 36 30 38
           L 12 38
           Q 7 36 6 28 Z"
        fill="#fafaf5" stroke="#1a1a1a" stroke-width="1.1" stroke-linejoin="round"/>

      <circle cx="12" cy="14" r="1.5" fill="#e8e5d8" opacity=".6"/>
      <circle cx="21" cy="13" r="1.5" fill="#e8e5d8" opacity=".6"/>
      <circle cx="30" cy="14" r="1.5" fill="#e8e5d8" opacity=".6"/>
      <path d="M 12 17 L 21 20 L 30 17" stroke="#c8c5b8" stroke-width=".5" fill="none" opacity=".5"/>
      <ellipse cx="11" cy="22" rx="2" ry="5" fill="rgba(255,255,255,.45)"/>
    `;
  }

  // الفك العلوي: الجذر للأعلى (داخل اللثة العلوية)، التاج للأسفل
  // الفك السفلي: التاج للأعلى، الجذر للأسفل (داخل اللثة السفلية)
  const groupTransform = isUpper ? `transform="scale(1,-1) translate(0,-${h})"` : '';

  return `
    <svg width="${w}" height="${h}" viewBox="0 0 ${w} ${h}" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="rootGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#f0e2c0"/>
          <stop offset="60%" stop-color="#e8d4a0"/>
          <stop offset="100%" stop-color="#c9a661"/>
        </linearGradient>
      </defs>
      <g ${groupTransform}>${svgInner}</g>
    </svg>
  `;
}




const selectedPatient = ref<any>(null);
const selectedDoctor = ref<any>(null);
const patientName = ref('');
const fileNum = ref('');
const doctorName = ref('');
const ageMode = ref<'adult' | 'child'>('adult');
const chartMode = ref<'current' | 'plan'>('current');
const selectedTooth = ref<number | null>(null);
const toothStates = ref<Record<number, string>>({});
const planTeeth = ref<Set<number>>(new Set());

const patientSignature = ref('');
const doctorSignature = ref('');

onMounted(() => {
    if (props.initialData) {
        const d = props.initialData;
        selectedPatient.value = d.patient;
        selectedDoctor.value = d.doctor;
        patientName.value = d.patient_name || '';
        fileNum.value = d.file_number || '';
        doctorName.value = d.doctor_name || '';
        ageMode.value = d.age_mode || 'adult';
        
        if (d.payload) {
            toothStates.value = d.payload.tooth_states || {};
            planTeeth.value = new Set(d.payload.plan_teeth || []);
            extraDiscount.value = d.payload.extra_discount || 0;
            extraDiscountType.value = d.payload.extra_discount_type || 'amount';
        }

        if (d.signatures) {
            d.signatures.forEach((sig: any) => {
                if (sig.role === 'patient') patientSignature.value = sig.image || '';
                if (sig.role === 'doctor') doctorSignature.value = sig.image || '';
            });
        }

        if (d.services) {
            services.value = d.services.map((s: any) => ({
                ...s,
                price: Number(s.price || 0),
                quantity: Number(s.quantity || s.qty || 1),
                discount: Number(s.discount || 0),
                discountType: s.discountType || s.discount_type || 'amount'
            }));
        }
    }
});

const upperTeeth = computed(() => ageMode.value === 'child' ? CHILD_UPPER : ADULT_UPPER);
const lowerTeeth = computed(() => ageMode.value === 'child' ? CHILD_LOWER : ADULT_LOWER);

function selectTooth(num: number) {
    if (chartMode.value === 'current') {
        selectedTooth.value = num;
    } else {
        if (planTeeth.value.has(num)) {
            planTeeth.value.delete(num);
        } else {
            planTeeth.value.add(num);
        }
        planTeeth.value = new Set(planTeeth.value);
    }
}

function setToothState(state: string) {
    if (selectedTooth.value !== null) {
        toothStates.value[selectedTooth.value] = state;
    }
}

const services = ref<any[]>([]);


const filteredCatalog = computed(() => serviceSearchResults.value);

function addService(item: any) {
    services.value.push({
        id: Date.now().toString() + Math.random().toString(36).substr(2, 5),
        code: item.code,
        name_ar: item.ar,
        name_en: item.en,
        price: item.price,
        quantity: 1,
        discount: 0,
        discountType: 'amount',
        tooth: planTeeth.value.size > 0 ? Array.from(planTeeth.value).join(', ') : 'عام',
        category: item.category
    });
    serviceSearch.value = '';
    showSearchResults.value = false;
}

function removeService(id: string) {
    services.value = services.value.filter(s => s.id !== id);
}

const extraDiscount = ref(0);
const extraDiscountType = ref<'amount'|'percent'>('amount');

const subtotal = computed(() => services.value.reduce((sum, s) => sum + (Number(s.price || 0) * Number(s.quantity || 0)), 0));

const serviceDiscountTotal = computed(() => {
    return services.value.reduce((sum, s) => {
        let lineTotal = Number(s.price || 0) * Number(s.quantity || 0);
        let d = Number(s.discount) || 0;
        return sum + (s.discountType === 'percent' ? lineTotal * (d / 100) : d);
    }, 0);
});

const afterServiceDiscount = computed(() => subtotal.value - serviceDiscountTotal.value);

const extraDiscountVal = computed(() => {
    let d = Number(extraDiscount.value) || 0;
    return extraDiscountType.value === 'percent' ? afterServiceDiscount.value * (d / 100) : d;
});

const grandTotal = computed(() => Math.max(afterServiceDiscount.value - extraDiscountVal.value, 0));

const isSigned = ref(false);

function buildPayload() {
    return {
        patient_id: selectedPatient.value?.id || null,
        doctor_id: selectedDoctor.value?.id || null,
        patient_name: selectedPatient.value?.name || patientName.value || '—',
        file_number: selectedPatient.value?.file_number || fileNum.value || null,
        doctor_name: selectedDoctor.value?.name || doctorName.value || null,
        age_mode: ageMode.value,
        payload: {
            patient_id: selectedPatient.value?.id,
            doctor_id: selectedDoctor.value?.id,
            patient_name: selectedPatient.value?.name || patientName.value,
            file_number: selectedPatient.value?.file_number || fileNum.value,
            doctor_name: selectedDoctor.value?.name || doctorName.value,
            chart_mode: chartMode.value,
            tooth_states: toothStates.value,
            plan_teeth: Array.from(planTeeth.value),
            subtotal: subtotal.value,
            service_discount: serviceDiscountTotal.value,
            extra_discount: extraDiscountVal.value,
        },
        grand_total: grandTotal.value,
        is_signed: !!(patientSignature.value && doctorSignature.value),
        services: services.value,
        tooth_states: toothStates.value,
        plan_teeth: Array.from(planTeeth.value),
        signatures: [
            { role: 'patient', image: patientSignature.value },
            { role: 'doctor', image: doctorSignature.value, signer_name: selectedDoctor.value?.name }
        ]
    };
}

defineExpose({ buildPayload });
</script>

<template>
    <FormSection number="١" :title="{ ar: 'بيانات الحالة', en: 'Case Information' }">
        <div class="case-info-grid">
            <PatientSelector v-model="selectedPatient" />
            <DoctorSelector v-model="selectedDoctor" />
        </div>
    </FormSection>

    <div :class="{ 'form-locked': !selectedPatient }">
        <FormSection number="٢" :title="{ ar: 'التشخيص ومخطط الأسنان', en: 'Diagnosis & Teeth Chart' }" :disabled="!selectedPatient">
        <div class="chart-controls">
            <div class="age-toggle">
                <button type="button" class="age-btn" :class="{ active: ageMode === 'adult' }" @click="ageMode = 'adult'">
                    <LocalizedText :value="{ ar: 'بالغ', en: 'Adult' }" />
                </button>
                <button type="button" class="age-btn" :class="{ active: ageMode === 'child' }" @click="ageMode = 'child'">
                    <LocalizedText :value="{ ar: 'أطفال', en: 'Pediatric' }" />
                </button>
            </div>
        </div>

        <div class="teeth-mode-switch">
            <button type="button" class="mode-btn" :class="{ active: chartMode === 'current' }" @click="chartMode = 'current'">
                <LocalizedText :value="{ ar: 'الحالة الحالية', en: 'Current Status' }" />
            </button>
            <button type="button" class="mode-btn green" :class="{ active: chartMode === 'plan' }" @click="chartMode = 'plan'">
                <LocalizedText :value="{ ar: '✓ الخطة العلاجية', en: '✓ Treatment Plan' }" />
            </button>
        </div>

        <div class="teeth-chart-layout">
            <div class="teeth-chart-wrap" :data-mode-label="chartMode === 'current' ? (isArabic ? 'الحالة الحالية' : 'Current Status') : (isArabic ? 'الخطة العلاجية' : 'Treatment Plan')">
                <div class="jaw-label"><LocalizedText :value="{ ar: 'الفك العلوي', en: 'UPPER JAW' }" /></div>
                
                <div class="jaw-row upper">
                    <div v-for="num in upperTeeth" :key="'upper'+num"
                         class="tooth"
                         :class="{ selected: selectedTooth === num || planTeeth.has(num), 'plan-mode': chartMode === 'plan' }"
                         :data-state="toothStates[num] || 'healthy'"
                         :data-num="num"
                         @click="selectTooth(num)">
                        <div class="tooth-num">{{ num }}</div>
                        <div v-html="buildToothSVG(num, true, ageMode === 'child')"></div>
                    </div>
                </div>

                <div class="jaw-divider"></div>

                <div class="jaw-row lower">
                    <div v-for="num in lowerTeeth" :key="'lower'+num"
                         class="tooth"
                         :class="{ selected: selectedTooth === num || planTeeth.has(num), 'plan-mode': chartMode === 'plan' }"
                         :data-state="toothStates[num] || 'healthy'"
                         :data-num="num"
                         @click="selectTooth(num)">
                        <div class="tooth-num">{{ num }}</div>
                        <div v-html="buildToothSVG(num, false, ageMode === 'child')"></div>
                    </div>
                </div>
            </div>

            <!-- Side panel -->
            <aside class="teeth-side-panel" v-if="chartMode === 'current'">
                <div class="tsp-header">
                    <span style="font-size: 14px; font-weight: bold; color: var(--primary);">
                        <LocalizedText :value="{ ar: 'اختر حالة السن', en: 'Select tooth state' }" />
                    </span>
                </div>
                <div class="state-buttons" style="display:flex; flex-direction:column; gap:8px;">
                    <button type="button" class="state-btn" @click="setToothState('healthy')"><span class="dot" style="background:#fafaf5;border-color:#ccc;"></span><LocalizedText :value="{ ar: 'سليم', en: 'Healthy' }" /></button>
                    <button type="button" class="state-btn" @click="setToothState('caries')"><span class="dot" style="background:#d4a544;"></span><LocalizedText :value="{ ar: 'تسوس', en: 'Caries' }" /></button>
                    <button type="button" class="state-btn" @click="setToothState('filling')"><span class="dot" style="background:#9bb8d4;"></span><LocalizedText :value="{ ar: 'حشوة', en: 'Filling' }" /></button>
                    <button type="button" class="state-btn" @click="setToothState('rct')"><span class="dot" style="background:#fce8d0;border-color:#ea580c;"></span><LocalizedText :value="{ ar: 'معالج عصب', en: 'Root Canal' }" /></button>
                    <button type="button" class="state-btn" @click="setToothState('crown')"><span class="dot" style="background:#fff;border-color:#0d9488;"></span><LocalizedText :value="{ ar: 'تركيبة', en: 'Crown' }" /></button>
                    <button type="button" class="state-btn" @click="setToothState('implant')"><span class="dot" style="background:#0d9488;"></span><LocalizedText :value="{ ar: 'زراعة', en: 'Implant' }" /></button>
                    <button type="button" class="state-btn" @click="setToothState('missing')"><span class="dot" style="background:#888;opacity:.4;"></span><LocalizedText :value="{ ar: 'مفقود', en: 'Missing' }" /></button>
                </div>
            </aside>
            <aside class="teeth-side-panel" v-else>
                <div class="selected-teeth active">
                    <span class="label"><LocalizedText :value="{ ar: 'الأسنان المختارة للعلاج:', en: 'Selected for treatment:' }" /></span>
                    <div style="display:flex;gap:6px;flex-wrap:wrap; margin-top:8px;">
                        <span v-for="t in Array.from(planTeeth)" :key="t" class="tooth-chip">{{ t }}</span>
                    </div>
                </div>
            </aside>
        </div>
    </FormSection>

    <FormSection number="٣" :title="{ ar: 'الخدمات المطلوبة', en: 'Requested Services' }" :disabled="!selectedPatient">
        <div class="services-search" style="position:relative;">
            <input v-model="serviceSearch" @focus="showSearchResults = true" type="text" :placeholder="isArabic ? 'ابحث بالاسم أو الكود...' : 'Search by name or code...'" />
            <div class="search-results" :class="{ active: showSearchResults && filteredCatalog.length > 0 }">
                <div v-for="item in filteredCatalog" :key="item.code" class="search-result-item" @click="addService(item)">
                    <div class="info">
                        <div class="name">{{ isArabic ? item.ar : item.en }}</div>
                        <div class="meta">{{ item.code }} • {{ isArabic ? item.categoryAr : item.categoryEn }}</div>
                    </div>
                    <div class="price-tag">{{ item.price }} {{ isArabic ? 'ر.س' : 'SAR' }}</div>
                </div>
            </div>
            <!-- Overlay to close dropdown -->
            <div v-if="showSearchResults" @click="showSearchResults = false" style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:40;"></div>
            <!-- Bring dropdown forward -->
            <div class="search-results" :class="{ active: showSearchResults && filteredCatalog.length > 0 }" style="z-index: 50;">
                <div v-for="item in filteredCatalog" :key="item.code" class="search-result-item" @click="addService(item)">
                    <div class="info">
                        <div class="name">{{ isArabic ? item.ar : item.en }}</div>
                        <div class="meta">{{ item.code }} • {{ isArabic ? item.categoryAr : item.categoryEn }}</div>
                    </div>
                    <div class="price-tag">{{ item.price }} {{ isArabic ? 'ر.س' : 'SAR' }}</div>
                </div>
            </div>
        </div>

        <div v-if="services.length === 0" class="services-empty">
            <LocalizedText :value="{ ar: 'ابحث عن الخدمات وأضفها للخطة العلاجية', en: 'Search and add services to the treatment plan' }" />
        </div>
        
        <div v-else class="services-group">
            <table class="services-table">
                <thead>
                    <tr>
                        <th><LocalizedText :value="{ ar: 'الخدمة', en: 'Service' }" /></th>
                        <th><LocalizedText :value="{ ar: 'السن', en: 'Tooth' }" /></th>
                        <th><LocalizedText :value="{ ar: 'السعر الأساسي', en: 'Price' }" /></th>
                        <th><LocalizedText :value="{ ar: 'الكمية', en: 'Qty' }" /></th>
                        <th><LocalizedText :value="{ ar: 'خصم', en: 'Discount' }" /></th>
                        <th><LocalizedText :value="{ ar: 'الصافي', en: 'Net' }" /></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in services" :key="s.id">
                        <td style="font-weight:600; color:var(--primary);">{{ isArabic ? s.name_ar : s.name_en }}</td>
                        <td><span class="row-tooth">{{ s.tooth }}</span></td>
                        <td>{{ s.price }}</td>
                        <td><input type="number" v-model="s.quantity" min="1" style="width: 60px;" /></td>
                        <td>
                            <div class="discount-cell">
                                <input type="number" v-model="s.discount" style="width:60px;" />
                                <select v-model="s.discountType">
                                    <option value="amount">{{ isArabic ? 'ر.س' : 'SAR' }}</option>
                                    <option value="percent">%</option>
                                </select>
                            </div>
                        </td>
                        <td class="net-price">
                            {{ ((Number(s.price || 0) * Number(s.quantity || 0)) - (s.discountType === 'percent' ? (Number(s.price || 0) * Number(s.quantity || 0) * (Number(s.discount || 0) / 100)) : Number(s.discount || 0))).toFixed(2) }}
                        </td>
                        <td>
                            <button type="button" class="remove-btn" @click="removeService(s.id)">✕</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </FormSection>

    <FormSection number="٤" :title="{ ar: 'الحسابات النهائية', en: 'Final Calculations' }" :disabled="!selectedPatient">
        <div class="extra-discount">
            <label><LocalizedText :value="{ ar: 'خصم إضافي على الإجمالي:', en: 'Extra discount on total:' }" /></label>
            <input type="number" v-model="extraDiscount" placeholder="0" />
            <select v-model="extraDiscountType">
                <option value="amount">{{ isArabic ? 'ر.س' : 'SAR' }}</option>
                <option value="percent">%</option>
            </select>
        </div>

        <div class="totals-grid">
            <div class="total-block">
                <div class="label"><LocalizedText :value="{ ar: 'إجمالي الخدمات', en: 'Subtotal' }" /></div>
                <div class="value">{{ subtotal.toFixed(2) }}</div>
                <div class="currency"><LocalizedText :value="{ ar: 'ر.س', en: 'SAR' }" /></div>
            </div>
            <div class="total-block">
                <div class="label"><LocalizedText :value="{ ar: 'خصم الخدمات', en: 'Service Discounts' }" /></div>
                <div class="value" style="color:var(--red);">{{ serviceDiscountTotal.toFixed(2) }}</div>
                <div class="currency"><LocalizedText :value="{ ar: 'ر.س', en: 'SAR' }" /></div>
            </div>
            <div class="total-block">
                <div class="label"><LocalizedText :value="{ ar: 'خصم إضافي', en: 'Extra Discount' }" /></div>
                <div class="value" style="color:var(--orange);">{{ extraDiscountVal.toFixed(2) }}</div>
                <div class="currency"><LocalizedText :value="{ ar: 'ر.س', en: 'SAR' }" /></div>
            </div>
            <div class="total-block grand">
                <div class="label"><LocalizedText :value="{ ar: 'الصافي النهائي', en: 'Grand Total' }" /></div>
                <div class="value">{{ grandTotal.toFixed(2) }}</div>
                <div class="currency"><LocalizedText :value="{ ar: 'ر.س', en: 'SAR' }" /></div>
            </div>
        </div>
    </FormSection>
    
    <FormSection number="٥" :title="{ ar: 'التوثيق والاعتماد', en: 'Authentication & Approval' }" :disabled="!selectedPatient">
        <div class="consent-text-box">
            <span v-if="isArabic">
                أُقر أنا الموقع أدناه <b>بأنني اطلعت على الخطة العلاجية</b> المذكورة أعلاه بكافة الخدمات والتكاليف، وأني <b>موافق تماماً</b> على المتابعة بالمبلغ الإجمالي <b>{{ grandTotal.toFixed(2) }} ر.س</b>، وأن توقيعي أدناه يُعد قبولاً نهائياً.
            </span>
            <span v-else>
                I, the undersigned, acknowledge that <b>I have reviewed the treatment plan</b> above with all services and costs, and that I <b>fully agree</b> to proceed with the total of <b>{{ grandTotal.toFixed(2) }} SAR</b>, and my signature below constitutes final acceptance.
            </span>
        </div>

        <div class="signatures-grid">
           
            <SignaturePad 
                v-model="patientSignature" 
                :label="{ ar: 'توقيع المريض', en: 'Patient Signature' }" 
              
            />
            <SignaturePad 
                v-model="doctorSignature" 
                :label="{ ar: 'توقيع الطبيب', en: 'Doctor Signature' }" 
                
            />
        </div>

        <div class="audit-trail" v-if="initialData">
            <div class="audit-item">
                <span class="label">{{ isArabic ? 'تاريخ الإنشاء:' : 'Created At:' }}</span>
                <span class="value">{{ initialData.created_at || '—' }}</span>
            </div>
            <div class="audit-item" v-if="initialData.creator">
                <span class="label">{{ isArabic ? 'بواسطة:' : 'By:' }}</span>
                <span class="value">{{ initialData.creator }}</span>
            </div>
            <div v-if="initialData.updated_at" class="audit-item">
                <span class="label">{{ isArabic ? 'آخر تعديل:' : 'Last Updated:' }}</span>
                <span class="value">{{ initialData.updated_at }}</span>
                <span class="value" v-if="initialData.editor">({{ initialData.editor }})</span>
            </div>
        </div>
    </FormSection>
    </div>
</template>

<style scoped>
.form-locked {
    opacity: 0.8;
}
.case-info-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    align-items: start;
}
.consent-text-box {
    background: var(--bg-soft);
    padding: 15px;
    border-radius: 10px;
    border: 1px solid var(--line);
    margin-bottom: 20px;
    font-size: 14px;
    line-height: 1.6;
    color: var(--ink);
}
.signatures-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 20px;
}
.audit-trail {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding-top: 15px;
    border-top: 1px solid var(--line);
    font-size: 12px;
    color: var(--ink-mute);
}
.audit-item {
    display: flex;
    gap: 6px;
}
.audit-item .label { font-weight: 700; }
@media (max-width: 768px) {
    .case-info-grid, .signatures-grid { grid-template-columns: 1fr; }
}
</style>
