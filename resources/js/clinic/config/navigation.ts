import { CLINIC_BASE } from '@/clinic/constants';
import type { LocalizedString } from '@/clinic/types/clinic';
import { index as branchesIndex } from '@/routes/admin/branches';
import { index as departmentsIndex } from '@/routes/admin/departments';
import { index as rolesIndex } from '@/routes/admin/roles';
import { create as usersCreate, index as usersIndex } from '@/routes/admin/users';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import { toUrl } from '@/lib/utils';

export type NavRoute = {
    href: string;
    label: LocalizedString;
    match?: string;
    exact?: boolean;
};

export type NavGroup = {
    id: string;
    label: LocalizedString;
    match: string;
    requiresAuth?: boolean;
    requiresVerified?: boolean;
    items: NavRoute[];
};

function route(
    href: string | { url: string },
    label: LocalizedString,
    match?: string,
    exact?: boolean,
): NavRoute {
    const url = toUrl(href);

    return { href: url, label, match: match ?? url, exact };
}

export const NAV_GROUPS: NavGroup[] = [
    {
        id: 'clinic',
        label: { ar: 'العيادة', en: 'Clinic' },
        match: CLINIC_BASE,
        items: [
            route(`${CLINIC_BASE}/home`, { ar: 'الرئيسية', en: 'Home' }),
            route(`${CLINIC_BASE}/dental`, { ar: 'الأسنان', en: 'Dental' }),
            route(`${CLINIC_BASE}/dermatology`, { ar: 'الجلدية', en: 'Dermatology' }),
            route(`${CLINIC_BASE}/patients`, { ar: 'إدارة المرضى', en: 'Patients' }, `${CLINIC_BASE}/patients`),
            route(`${CLINIC_BASE}/doctors`, { ar: 'الأطباء المعالجون', en: 'Doctors' }, `${CLINIC_BASE}/doctors`),
            route(`${CLINIC_BASE}/services`, { ar: 'دليل الخدمات', en: 'Services' }, `${CLINIC_BASE}/services`),
            route(`${CLINIC_BASE}/nationalities`, { ar: 'الجنسيات والرموز', en: 'Nationalities' }, `${CLINIC_BASE}/nationalities`),
            route(`${CLINIC_BASE}/archive`, { ar: 'الأرشيف', en: 'Archive' }),
            route(`${CLINIC_BASE}/records`, { ar: 'سجلات النماذج', en: 'Form Records' }, `${CLINIC_BASE}/records`, true),
            route(CLINIC_BASE, { ar: 'اختيار الفرع', en: 'Branch Select' }, CLINIC_BASE, true),
            route(`${CLINIC_BASE}/role`, { ar: 'اختيار الدور', en: 'Role Select' }, `${CLINIC_BASE}/role`, true),
        ],
    },
    {
        id: 'management',
        label: { ar: 'الإدارة', en: 'Management' },
        match: '/dashboard',
        requiresAuth: true,
        requiresVerified: true,
        items: [
            route(usersIndex(), { ar: 'المستخدمون', en: 'Users' }, '/dashboard/users'),
            route(usersCreate(), { ar: 'مستخدم جديد', en: 'New User' }, '/dashboard/users/create', true),
            route(rolesIndex(), { ar: 'الأدوار', en: 'Roles' }, '/dashboard/roles'),
            route(branchesIndex(), { ar: 'الفروع', en: 'Branches' }, '/dashboard/branches'),
            route(departmentsIndex(), { ar: 'الأقسام', en: 'Departments' }, '/dashboard/departments'),
            route('/dashboard/form-categories', { ar: 'تصنيفات النماذج', en: 'Form Categories' }, '/dashboard/form-categories'),
            route('/dashboard/form-templates', { ar: 'نماذج النظام', en: 'Form Templates' }, '/dashboard/form-templates'),
        ],
    },
    {
        id: 'settings',
        label: { ar: 'الإعدادات', en: 'Settings' },
        match: '/settings',
        requiresAuth: true,
        items: [
            route(editProfile(), { ar: 'الملف الشخصي', en: 'Profile' }, '/settings/profile'),
            route(editSecurity(), { ar: 'الأمان', en: 'Security' }, '/settings/security'),
            route(editAppearance(), { ar: 'المظهر', en: 'Appearance' }, '/settings/appearance', true),
        ],
    },
];

export const STANDALONE_ROUTES: NavRoute[] = [
    route('/dashboard', { ar: 'لوحة التحكم', en: 'Dashboard' }, '/dashboard', true),
];

export function pathMatches(routeItem: NavRoute, path: string): boolean {
    const match = routeItem.match ?? routeItem.href;

    if (routeItem.exact) {
        return path === match;
    }

    return path === match || path.startsWith(`${match}/`);
}

export function groupIsActive(group: NavGroup, path: string): boolean {
    if (group.id === 'clinic') {
        const managementPrefixes = ['/dashboard/users', '/dashboard/roles', '/dashboard/branches', '/dashboard/departments'];

        if (managementPrefixes.some((prefix) => path.startsWith(prefix))) {
            return false;
        }

        return path === CLINIC_BASE || path.startsWith(`${CLINIC_BASE}/`);
    }

    if (group.id === 'management') {
        return group.items.some((item) => pathMatches(item, path));
    }

    return path.startsWith(group.match);
}
