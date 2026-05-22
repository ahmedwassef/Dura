export type ClinicLang = 'ar' | 'en';

export type BranchCode = 'zulfi' | 'dawadmi';

export type ClinicRole = 'reception' | 'doctor' | 'admin' | '';

export type FormTemplateCode =
    | 'dental'
    | 'medhist'
    | 'medreport'
    | 'surgery'
    | 'rct'
    | 'afinash'
    | 'ortho'
    | 'veneer'
    | 'crown_rct'
    | 'crown_implant'
    | 'ortho_end'
    | 'ortho_photo'
    | 'ortho_extraction'
    | 'proc_card'
    | 'laser_hair'
    | 'laser_bleach'
    | 'self_laser'
    | 'morpheus'
    | 'roaccutane'
    | 'derm_photo';

export type CategoryCode =
    | 'extraction'
    | 'endo'
    | 'prostho'
    | 'ortho'
    | 'derm_clinic'
    | 'derm_devices';

export interface ServicePrice {
    code: string;
    ar: string;
    en: string;
    price: number;
    category: string;
    categoryAr: string;
    categoryEn: string;
}

export interface LocalizedString {
    ar: string;
    en: string;
}

export interface CategoryFormItem {
    type: FormTemplateCode;
    icon: string;
    title: LocalizedString;
    description: LocalizedString;
    bilingual?: boolean;
    arOnly?: boolean;
}

export interface FormCategory {
    code: CategoryCode;
    title: LocalizedString;
    header: LocalizedString;
    forms: CategoryFormItem[];
}

export interface BranchOption {
    code: BranchCode;
    name: LocalizedString;
    description: LocalizedString;
}
