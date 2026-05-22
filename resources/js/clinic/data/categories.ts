import type { FormCategory } from '@/clinic/types/clinic';

export const FORM_CATEGORIES: Record<string, FormCategory> = {
    extraction: {
        code: 'extraction',
        title: { ar: 'نماذج الخلع', en: 'Extraction Forms' },
        header: { ar: 'نماذج الخلع', en: 'Extraction Forms' },
        forms: [
            {
                type: 'surgery',
                icon: 'surgery-icon',
                title: {
                    ar: 'موافقة الإجراءات الطبية',
                    en: 'Medical Procedures Consent',
                },
                description: {
                    ar: 'موافقة المريض على الجراحة والتخدير الموضعي وخلع الأسنان',
                    en: 'Patient consent for surgery, anesthesia, and tooth extraction',
                },
                bilingual: true,
            },
        ],
    },
    endo: {
        code: 'endo',
        title: { ar: 'نماذج العصب', en: 'Endodontic Forms' },
        header: { ar: 'نماذج العصب', en: 'Endodontic Forms' },
        forms: [
            {
                type: 'afinash',
                icon: 'afinash-icon',
                title: { ar: 'موافقة معالجة الجذور', en: 'Apexification Consent' },
                description: {
                    ar: 'موافقة مفصلة على علاج جذور الأسنان (افيناش سالجر)',
                    en: 'Detailed consent for root treatment',
                },
                arOnly: true,
            },
            {
                type: 'rct',
                icon: 'rct-icon',
                title: { ar: 'نهاية علاج العصب', en: 'Root Canal Completion' },
                description: {
                    ar: 'إشعار للمريض بضرورة التلبيس خلال 10 أيام من تاريخ النموذج',
                    en: 'Notice for crown placement within 10 days',
                },
                arOnly: true,
            },
        ],
    },
    prostho: {
        code: 'prostho',
        title: { ar: 'نماذج التركيبات', en: 'Prosthetic Forms' },
        header: { ar: 'نماذج التركيبات', en: 'Prosthetic Forms' },
        forms: [
            {
                type: 'veneer',
                icon: 'prostho-icon',
                title: { ar: 'موافقة الفينير (القشور الخزفية)', en: 'Veneer Consent' },
                description: {
                    ar: 'الموافقة المستنيرة على تركيب القشور الخزفية لتحسين المظهر الجمالي',
                    en: 'Informed consent for porcelain veneers',
                },
                arOnly: true,
            },
            {
                type: 'crown_rct',
                icon: 'prostho-icon',
                title: {
                    ar: 'تاج زركون/إيماكس على سن معالج خارجياً',
                    en: 'Zirconia/Emax Crown (External RCT)',
                },
                description: {
                    ar: 'تركيب تاج لسن تم علاج جذوره خارج العيادة',
                    en: 'Crown placement on tooth with external root canal',
                },
                arOnly: true,
            },
            {
                type: 'crown_implant',
                icon: 'prostho-icon',
                title: {
                    ar: 'تاج على زرعة مركبة خارجياً',
                    en: 'Crown on External Implant',
                },
                description: {
                    ar: 'تركيب تاج على زرعة أسنان تم تركيبها خارج العيادة',
                    en: 'Crown placement on externally placed implant',
                },
                arOnly: true,
            },
        ],
    },
    ortho: {
        code: 'ortho',
        title: { ar: 'نماذج التقويم', en: 'Orthodontic Forms' },
        header: { ar: 'نماذج التقويم', en: 'Orthodontic Forms' },
        forms: [
            {
                type: 'ortho',
                icon: 'ortho-icon',
                title: { ar: 'عقد التقويم', en: 'Orthodontic Contract' },
                description: {
                    ar: 'اتفاقية مالية وشروط علاج تقويم الأسنان (19 بند)',
                    en: 'Financial agreement and orthodontic terms (19 clauses)',
                },
                arOnly: true,
            },
            {
                type: 'ortho_end',
                icon: 'ortho-icon',
                title: { ar: 'إقرار إنهاء التقويم', en: 'Treatment Completion' },
                description: {
                    ar: 'إقرار إنهاء العلاج التقويمي والالتزام بجهاز التثبيت',
                    en: 'Treatment end declaration and retainer commitment',
                },
                arOnly: true,
            },
            {
                type: 'ortho_photo',
                icon: 'ortho-icon',
                title: { ar: 'موافقة تصوير التقويم', en: 'Orthodontic Photo Consent' },
                description: {
                    ar: 'موافقة على التصوير قبل وبعد علاج التقويم للمقارنة',
                    en: 'Before/after photo consent for orthodontic comparison',
                },
                arOnly: true,
            },
            {
                type: 'ortho_extraction',
                icon: 'ortho-icon',
                title: { ar: 'تحويل لخلع الأسنان', en: 'Extraction Referral' },
                description: {
                    ar: 'تحويل المريض لخلع سن أو أكثر داخل العيادة أو خارجها',
                    en: 'Refer patient for extraction inside or outside clinic',
                },
                bilingual: true,
            },
        ],
    },
    derm_clinic: {
        code: 'derm_clinic',
        title: { ar: 'عيادة الدكتورة', en: "Doctor's Clinic" },
        header: {
            ar: 'عيادة الدكتورة - قسم الجلدية',
            en: "Doctor's Clinic - Dermatology",
        },
        forms: [
            {
                type: 'proc_card',
                icon: 'derm-clinic-icon',
                title: { ar: 'كرت الإجراءات والحقن', en: 'Procedures & Injections Card' },
                description: {
                    ar: 'تسجيل الفيلر والمواد المستخدمة مع رسم توضيحي للمناطق',
                    en: 'Filler/material tracking with facial diagram',
                },
                bilingual: true,
            },
            {
                type: 'roaccutane',
                icon: 'derm-clinic-icon',
                title: { ar: 'موافقة الروكتان', en: 'Roaccutane Consent' },
                description: {
                    ar: 'موافقة على دواء الايزوتريتنوين مع تحذيرات الحمل',
                    en: 'Isotretinoin medication consent with pregnancy warnings',
                },
                bilingual: true,
            },
            {
                type: 'derm_photo',
                icon: 'derm-clinic-icon',
                title: { ar: 'موافقة التصوير', en: 'Photo Consent' },
                description: {
                    ar: 'موافقة على التصوير قبل وبعد العلاج للمقارنة',
                    en: 'Before/after photography consent',
                },
                arOnly: true,
            },
        ],
    },
    derm_devices: {
        code: 'derm_devices',
        title: { ar: 'قسم الأجهزة', en: 'Devices Section' },
        header: { ar: 'قسم الأجهزة - الجلدية', en: 'Devices Section - Dermatology' },
        forms: [
            {
                type: 'medhist',
                icon: 'medhist-icon',
                title: { ar: 'السجل الصحي', en: 'Medical History' },
                description: {
                    ar: 'تعبئة السجل الصحي للمريض قبل جلسة الليزر',
                    en: 'Patient medical history before laser session',
                },
                bilingual: true,
            },
            {
                type: 'laser_hair',
                icon: 'derm-devices-icon',
                title: { ar: 'موافقة الليزر', en: 'Laser Hair Removal' },
                description: {
                    ar: 'موافقة على إزالة الشعر بالليزر مع الاستبيان الطبي',
                    en: 'Laser hair removal consent with medical questionnaire',
                },
                bilingual: true,
            },
            {
                type: 'laser_bleach',
                icon: 'derm-devices-icon',
                title: { ar: 'موافقة التشقير', en: 'Laser Bleaching' },
                description: {
                    ar: 'موافقة على تشقير الشعر بالليزر مع الاستبيان الطبي',
                    en: 'Laser hair bleaching consent',
                },
                bilingual: true,
            },
            {
                type: 'self_laser',
                icon: 'derm-devices-icon',
                title: { ar: 'إقرار الليزر الذاتي', en: 'Self Laser Consent' },
                description: {
                    ar: 'موافقة على جلسة الليزر الذاتي وحساب الومضات',
                    en: 'Self-laser session consent and pulse counter',
                },
                arOnly: true,
            },
            {
                type: 'morpheus',
                icon: 'derm-devices-icon',
                title: { ar: 'موافقة المورفيس', en: 'Morpheus Consent' },
                description: {
                    ar: 'موافقة على جلسة Morpheus مع التاريخ المرضي الكامل',
                    en: 'Morpheus treatment consent with full medical history',
                },
                bilingual: true,
            },
        ],
    },
};
