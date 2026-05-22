export interface User {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    display_name_ar: string | null;
    status: 'active' | 'inactive';
    avatar_url: string | null;
    branch_id: number | null;
    department_id: number | null;
    branch?: Branch;
    department?: Department;
    roles?: Role[];
    created_at: string;
    updated_at: string;
}

export interface Role {
    id: number;
    name: string;
    guard_name: string;
    permissions?: Permission[];
}

export interface Permission {
    id: number;
    name: string;
    guard_name: string;
}

export interface Department {
    id: number;
    code: string;
    name_ar: string;
    name_en: string;
    status: string;
    description: string | null;
}

export interface Branch {
    id: number;
    code: string;
    name_ar: string;
    name_en: string;
    address: string | null;
    phone: string | null;
    is_active: boolean;
}

export interface FormCategory {
    id: number;
    department_id: number;
    code: string;
    name_ar: string;
    name_en: string;
    sort_order: number;
    is_active: boolean;
    department?: Department;
}

export interface FormTemplate {
    id: number;
    department_id: number;
    code: string;
    category_code: string | null;
    name_ar: string;
    name_en: string;
    is_bilingual: boolean;
    is_ar_only: boolean;
    sort_order: number;
    is_active: boolean;
    department?: Department;
    category?: FormCategory;
    fields?: FormField[];
}

export interface FormField {
    id: number;
    form_template_id: number;
    type: 'section' | 'text' | 'textarea' | 'number' | 'date' | 'select' | 'radio' | 'checkbox' | 'consent' | 'signature';
    key: string | null;
    label_ar: string | null;
    label_en: string | null;
    placeholder_ar: string | null;
    placeholder_en: string | null;
    content_ar: string | null;
    content_en: string | null;
    options: Array<{ value: string; label_ar: string; label_en: string }> | null;
    settings: {
        width?: string; // 'full' | 'half' | 'third'
        rows?: number;
        required?: boolean;
        [key: string]: any;
    } | null;
    is_required: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
}
