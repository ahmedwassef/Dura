import type { FormTemplateCode, LocalizedString } from '@/clinic/types/clinic';

export interface StandaloneFormTemplate {
    code: FormTemplateCode;
    title: LocalizedString;
    subtitle: LocalizedString;
    icon: string;
    badge?: LocalizedString;
    primary?: boolean;
}

export const DENTAL_STANDALONE_FORMS: StandaloneFormTemplate[] = [
    {
        code: 'dental',
        title: { ar: 'الخطة العلاجية', en: 'Treatment Plan' },
        subtitle: {
            ar: 'نموذج الخطة العلاجية مع شارت الأسنان وحساب التكلفة',
            en: 'Treatment plan with teeth chart and cost calculation',
        },
        icon: 'primary-icon',
        badge: { ar: 'الأكثر استخداماً', en: 'Most Used' },
        primary: true,
    },
    {
        code: 'medhist',
        title: { ar: 'السجل الصحي', en: 'Medical History' },
        subtitle: {
            ar: 'بيانات المريض والتاريخ الصحي والحساسية',
            en: 'Patient data and medical history',
        },
        icon: 'medhist-icon',
        badge: { ar: 'نموذج واحد', en: '1 form' },
    },
    {
        code: 'medreport',
        title: { ar: 'التقرير الطبي', en: 'Medical Report' },
        subtitle: {
            ar: 'تقرير الطبيب مع توقيع وختم — يدعم العربي والإنجليزي',
            en: "Doctor's report with signature and stamp — bilingual",
        },
        icon: 'medreport-icon',
        badge: { ar: 'نموذج واحد', en: '1 form' },
    },
];

export const DENTAL_CATEGORY_CARDS = [
    { code: 'extraction', count: { ar: '١ نموذج', en: '1 form' } },
    { code: 'endo', count: { ar: '٢ نماذج', en: '2 forms' } },
    { code: 'prostho', count: { ar: '٣ نماذج', en: '3 forms' } },
    { code: 'ortho', count: { ar: '٤ نماذج', en: '4 forms' } },
] as const;

export const FORM_ROUTE_MAP: Record<FormTemplateCode, string> = {
    dental: 'clinic.forms.dental',
    medhist: 'clinic.forms.medhist',
    medreport: 'clinic.forms.medreport',
    surgery: 'clinic.forms.surgery',
    rct: 'clinic.forms.rct',
    afinash: 'clinic.forms.afinash',
    ortho: 'clinic.forms.ortho',
    veneer: 'clinic.forms.veneer',
    crown_rct: 'clinic.forms.crown-rct',
    crown_implant: 'clinic.forms.crown-implant',
    ortho_end: 'clinic.forms.ortho-end',
    ortho_photo: 'clinic.forms.ortho-photo',
    ortho_extraction: 'clinic.forms.ortho-extraction',
    proc_card: 'clinic.forms.proc-card',
    laser_hair: 'clinic.forms.laser-hair',
    laser_bleach: 'clinic.forms.laser-bleach',
    self_laser: 'clinic.forms.self-laser',
    morpheus: 'clinic.forms.morpheus',
    roaccutane: 'clinic.forms.roaccutane',
    derm_photo: 'clinic.forms.derm-photo',
};
