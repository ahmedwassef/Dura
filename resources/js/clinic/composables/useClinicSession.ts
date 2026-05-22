import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { BranchCode, ClinicRole } from '@/clinic/types/clinic';
import { BRANCHES } from '@/clinic/data/branches';

const branch = ref<BranchCode | null>(null);
const role = ref<ClinicRole>('');

const BRANCH_KEY = 'durat_session_branch';
const ROLE_KEY = 'durat_session_role';

function readStorage(): void {
    if (typeof window === 'undefined') {
        return;
    }

    const savedBranch = localStorage.getItem(BRANCH_KEY) as BranchCode | null;
    const savedRole = localStorage.getItem(ROLE_KEY) as ClinicRole | null;

    if (savedBranch === 'zulfi' || savedBranch === 'dawadmi') {
        branch.value = savedBranch;
    }

    if (savedRole === 'reception' || savedRole === 'doctor' || savedRole === 'admin') {
        role.value = savedRole;
    }
}

readStorage();

export function useClinicSession() {
    const page = usePage();

    watch(
        () => page.props.clinic as { branchCode?: string; role?: string } | undefined,
        (clinic) => {
            if (typeof window === 'undefined') return;

            if (clinic?.branchCode === 'zulfi' || clinic?.branchCode === 'dawadmi') {
                branch.value = clinic.branchCode;
                localStorage.setItem(BRANCH_KEY, clinic.branchCode);
            }

            if (
                clinic?.role === 'reception' ||
                clinic?.role === 'doctor' ||
                clinic?.role === 'admin'
            ) {
                role.value = clinic.role;
                localStorage.setItem(ROLE_KEY, clinic.role);
            }
        },
        { immediate: true },
    );
    const branchLabel = computed(() => {
        const current = BRANCHES.find((item) => item.code === branch.value);

        return current ?? null;
    });

    function selectBranch(code: BranchCode): void {
        branch.value = code;
        localStorage.setItem(BRANCH_KEY, code);
    }

    function selectRole(next: ClinicRole): void {
        role.value = next;
        localStorage.setItem(ROLE_KEY, next);
    }

    function clearRole(): void {
        role.value = '';
        localStorage.removeItem(ROLE_KEY);
    }

    return {
        branch,
        role,
        branchLabel,
        selectBranch,
        selectRole,
        clearRole,
    };
}
