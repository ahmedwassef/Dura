<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\FormSubmission;
use App\Models\User;

class FormSubmissionPolicy
{
    public function create(?User $user): bool
    {
        if (! $user) {
            return true;
        }

        return $user->can(PermissionName::FormsCreate->value);
    }

    public function view(?User $user, FormSubmission $submission): bool
    {
        if (! $user) {
            return true;
        }

        return $user->can(PermissionName::ArchiveView->value);
    }

    public function download(?User $user, FormSubmission $submission): bool
    {
        if (! $user) {
            return true;
        }

        return $user->can(PermissionName::ArchiveDownload->value);
    }
}
