import type { BranchOption } from '@/clinic/types/clinic';

export const BRANCHES: BranchOption[] = [
    {
        code: 'zulfi',
        name: { ar: 'فرع الزلفي', en: 'Zulfi Branch' },
        description: {
            ar: 'مجمع درة النخبة الطبي - الزلفي',
            en: 'Durat Alnukhba - Zulfi',
        },
    },
    {
        code: 'dawadmi',
        name: { ar: 'فرع الدوادمي', en: 'Dawadmi Branch' },
        description: {
            ar: 'مجمع درة النخبة الطبي - الدوادمي',
            en: 'Durat Alnukhba - Dawadmi',
        },
    },
];
