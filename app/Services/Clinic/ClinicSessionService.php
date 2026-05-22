<?php

namespace App\Services\Clinic;

use App\Contracts\Repositories\BranchRepositoryInterface;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class ClinicSessionService
{
    public const BRANCH_KEY = 'clinic.branch';

    public const ROLE_KEY = 'clinic.role';

    public function __construct(
        protected BranchRepositoryInterface $branchRepository,
    ) {}

    public function setBranch(string $code): void
    {
        session([self::BRANCH_KEY => $code]);
    }

    public function setRole(string $role): void
    {
        session([self::ROLE_KEY => $role]);
    }

    public function getBranchCode(): ?string
    {
        return session(self::BRANCH_KEY);
    }

    public function getRole(): ?string
    {
        return session(self::ROLE_KEY);
    }

    public function resolveBranch(?User $user = null): ?Branch
    {
        $code = $this->getBranchCode();

        if (! $code && $user?->branch_id) {
            $code = $user->branch?->code;
            if ($code) {
                $this->setBranch($code);
            }
        }

        return $code ? $this->branchRepository->findByCode($code) : null;
    }

    public function resolveBranchId(?User $user = null): ?int
    {
        return $this->resolveBranch($user)?->id;
    }

    public function clearRole(): void
    {
        session()->forget(self::ROLE_KEY);
    }

    public function sharePayload(?User $user = null): array
    {
        $branch = $this->resolveBranch($user);

        return [
            'branch' => $branch?->only(['code', 'name_ar', 'name_en']),
            'branchCode' => $branch?->code,
            'role' => $this->getRole(),
        ];
    }

    public function syncFromRequest(Request $request, ?User $user = null): void
    {
        if ($request->filled('branch')) {
            $this->setBranch($request->string('branch')->toString());
        }

        if ($request->filled('role')) {
            $this->setRole($request->string('role')->toString());
        }

        if (! $this->getBranchCode() && $user?->branch) {
            $this->setBranch($user->branch->code);
        }
    }
}
